<?php

class rex_cronjob_project_manager_pagespeed extends rex_cronjob
{
  
    public function execute() {
      
      $count = rex_sql::factory()->setDebug(0)->getArray('SELECT count(*) as count FROM rex_project_manager_domain');
      if ($count[0]['count'] <= 10) {
        $count = 1;
      } else {
        $count = $count[0]['count'] / 10;
        $count = round($count, 0, PHP_ROUND_HALF_UP);
      }      
      
      for ($i = 0; $i < $count; $i++) {
        $this->getData();
      }
      
      return true;
      
    }
    
    public function getData() {

        $websites = rex_sql::factory()->setDebug(0)->getArray('SELECT D.domain AS domain, D.is_ssl as is_ssl FROM
        (SELECT domain, createdate FROM rex_project_manager_domain_psi) AS PSI
        RIGHT JOIN
        (SELECT domain, updatedate, is_ssl FROM rex_project_manager_domain) AS D
        ON
        PSI.domain = D.domain
        GROUP BY D.domain
        ORDER BY PSI.createdate ASC LIMIT 10');
        
        $error = false;
        $multi_curl = curl_multi_init();
        $resps = array();
        $options = array(
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_AUTOREFERER    => true,
          CURLOPT_MAXREDIRS      => 4,
          CURLOPT_HEADER         => false,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_SSL_VERIFYPEER => false,
          CURLOPT_TIMEOUT => 30 // seconds
        );
      
        foreach($websites as $website) {
          
          $fstreams = array();
          
          $domain = $website['domain'];
          if($website['is_ssl']) {
              $prefix = "https://";
          } else {
              $prefix = "http://";
          }
  
          $url_desktop = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?filter_third_party_resources=false&locale=de_DE&screenshot=true&snapshots=false&strategy=desktop&key='.rex_config::get('project_manager/pagespeed', 'project_manager_pagespeed_api_key').'&url='.urlencode($prefix.$website['domain']);
          $url_mobile = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed?filter_third_party_resources=false&locale=de_DE&screenshot=true&snapshots=false&strategy=mobile&key='.rex_config::get('project_manager/pagespeed', 'project_manager_pagespeed_api_key').'&url='.urlencode($prefix.$website['domain']);
          $resps[$domain.";mobile"] = curl_init($url_mobile);
          $resps[$domain.";desktop"] = curl_init($url_desktop);
          curl_setopt_array($resps[$domain.";mobile"], $options);
          curl_setopt_array($resps[$domain.";desktop"], $options);
          curl_multi_add_handle($multi_curl, $resps[$domain.";mobile"]);
          curl_multi_add_handle($multi_curl, $resps[$domain.";desktop"]);
         
        }
        
        $active = null;
        do {
          curl_multi_exec($multi_curl, $active);
        } while ($active > 0);
   
 
        foreach ($resps as $key => $response) {
          $domain = explode(";", $key)[0];
          $mode = explode(";", $key)[1];
          $resp = curl_multi_getcontent($response);
          curl_multi_remove_handle($multi_curl, $response);
          $pagespeed = json_decode($resp, true);
          
          $score_mobile = 0;
          $score_desktop = 0;
          if (isset($pagespeed['lighthouseResult'])) {
            if ($pagespeed['lighthouseResult']['categories']['performance']['score'] != NULL) $score_mobile = $pagespeed['lighthouseResult']['categories']['performance']['score'];
            if ($pagespeed['lighthouseResult']['categories']['performance']['score'] != NULL) $score_desktop = $pagespeed['lighthouseResult']['categories']['performance']['score'];
          }
          if(json_last_error() === JSON_ERROR_NONE && (!array_key_exists("error", $pagespeed)))  {
           		if($mode == "mobile") {
                  rex_sql::factory()->setDebug(0)->setQuery('INSERT INTO ' . rex::getTable('project_manager_domain_psi') . ' (`domain`, `raw`, `createdate`, `score_mobile`, `status`) VALUES(:domain, :resp, NOW(), :score_mobile, 1) 
                  ON DUPLICATE KEY UPDATE domain = :domain, `raw` = :resp, createdate = NOW(), `score_mobile` = :score_mobile,  `status` = 1', [":domain" => $domain, ":resp" => $resp, ":score_mobile" => $score_mobile] );
              } else if($mode == "desktop") {                  
              	  rex_sql::factory()->setDebug(0)->setQuery('INSERT INTO ' . rex::getTable('project_manager_domain_psi') . ' (`domain`, `raw`, `createdate`, `score_desktop`, `status`) VALUES(:domain, :resp, NOW(), :score_desktop, 1) 
                  ON DUPLICATE KEY UPDATE domain = :domain, `raw` = :resp, createdate = NOW(), `score_desktop` = :score_desktop, `status` = 1', [":domain" => $domain, ":resp" => $resp, ":score_desktop" => $score_desktop] );              
              } 
          } else {
            
            // ERROR HANDLE
            if($mode == "desktop") {
              
              rex_sql::factory()->setDebug(0)->setQuery('INSERT INTO ' . rex::getTable('project_manager_domain_psi') . ' (`domain`, `raw`, `createdate`,  `status`) VALUES(:domain, :resp, NOW(), -1)
                  ON DUPLICATE KEY UPDATE domain = :domain, `raw` = :resp, createdate = NOW(), `status` = -1', [":domain" => $domain, ":resp" => $resp] );
              
            } else  if($mode == "mobile") {
              rex_sql::factory()->setDebug(0)->setQuery('INSERT INTO ' . rex::getTable('project_manager_domain_psi') . ' (`domain`, `raw`, `createdate`, `status`) VALUES(:domain, :resp, NOW(), -1)
                  ON DUPLICATE KEY UPDATE domain = :domain, `raw` = :resp, createdate = NOW(),  `status` = -1', [":domain" => $domain, ":resp" => $resp] );
              
            }
            
            $this->setMessage($pagespeed['error']['errors'][0]['message'].': '.$pagespeed['error']['errors'][0]['reason']);              
            $error = true;

          }
        }
        curl_multi_close($multi_curl);
        
        if ($error === true) {
          return false;
        } else {
          return true;
        }
    }
    public function getTypeName()
    {
        return rex_i18n::msg('project_manager_cronjob_pagespeed_name');
    }

    public function getParamFields()
    {
        return [];
    }
}
?>