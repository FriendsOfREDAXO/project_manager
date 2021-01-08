<?php

// Aufruf: 
// /?rex-api-call=project_manager&api_key=###

class rex_api_project_manager_server extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        ob_end_clean();
        $func = rex_request('func','string');
        $protocol = rex_request('protocol','string');
        $domain = rex_request('domain','string');
        $api_key = rex_request('api_key','string');
        $timestamp = time();

        if($func == "delLog") {

          // ?rex-api-call=project_manager&api_key=634de6b36b4b4fde90e09c0a9588a7df&func=delLog&t=1549014945&_=1549014940721
          
          $url = $protocol.urlencode($domain).'/index.php?rex-api-call=project_manager&api_key='.$api_key.'&func='.$func;          
          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_URL => $url
          ));
          $resp = curl_exec($curl);
         
          $json = json_decode($resp, true);
            
          if(json_last_error() === JSON_ERROR_NONE && $json !== null) {
            if ($json['delLog'] == 1) {
              $params['delLog'] = 1;
            }
          } else {
            $params['delLog'] = -1;
          }
          
          // reload data          
          $url = $protocol.urlencode($domain)."/index.php?rex-api-call=project_manager&api_key=".$api_key.'&t='.$timestamp;
          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_URL => $url
          ));
          
          $response = curl_exec($curl);
          $resp = $response;
          $json = json_decode($resp, true);
          
          $project_manager_domain = rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM ' . rex::getTable('project_manager_domain') . ' WHERE domain = ? LIMIT 1', [$domain]);
          
          if(json_last_error() === JSON_ERROR_NONE && $json !== null) {
            
            if ($json['status'] == 1) {
              
              rex_sql::factory()->setDebug(0)->setQuery('INSERT INTO ' . rex::getTable('project_manager_logs') . ' (`domain_id`, `createdate`, `raw`) VALUES(?,NOW(),?)', [$project_manager_domain[0]['id'],  $resp] );
              // SET STATUS
              rex_sql::factory()->setDebug(0)->setQuery("UPDATE " . rex::getTable('project_manager_domain') . " SET status = ?, updatedate = NOW() WHERE id = ?", [1, $project_manager_domain[0]['id']]);
              
            } else {
              // SET STATUS
              rex_sql::factory()->setDebug(0)->setQuery("UPDATE " . rex::getTable('project_manager_domain') . " SET status = ?, updatedate = NOW()WHERE id = ?", [0, $project_manager_domain[0]['id']]);
            }
            
          } else {
            // SET STATUS
            rex_sql::factory()->setDebug(0)->setQuery("UPDATE " . rex::getTable('project_manager_domain') . " SET status = ?, updatedate = NOW() WHERE id = ?", [-1, $project_manager_domain[0]['id']]);
          }          
          rex_sql::factory()->setDebug(0)->setQuery("UPDATE " . rex::getTable('project_manager_domain') . " SET updatedate = NOW() WHERE id = ?", [$project_manager_domain[0]['id']]);            
          
        }
        
        if($func == "updateData") {
          
          // reload data
          $url = $protocol.urlencode($domain)."?rex-api-call=project_manager&api_key=".$api_key.'&t='.$timestamp;
          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_MAXREDIRS      => 3,
            CURLOPT_HEADER         => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_URL => $url
          ));
          
          $response = curl_exec($curl);
          $resp = $response;
          $json = json_decode($resp, true);
          
          $project_manager_domain = rex_sql::factory()->setDebug(0)->getArray('SELECT * FROM ' . rex::getTable('project_manager_domain') . ' WHERE domain = ? LIMIT 1', [$domain]);
          
          if(json_last_error() === JSON_ERROR_NONE && $json !== null) {
            
            if ($json['status'] == 1) {
              
              rex_sql::factory()->setDebug(0)->setQuery('INSERT INTO ' . rex::getTable('project_manager_logs') . ' (`domain_id`, `createdate`, `raw`) VALUES(?,NOW(),?)', [$project_manager_domain[0]['id'],  $resp] );
              // SET STATUS
              rex_sql::factory()->setDebug(0)->setQuery("UPDATE " . rex::getTable('project_manager_domain') . " SET status = ?, updatedate = NOW() WHERE id = ?", [1, $project_manager_domain[0]['id']]);
              
            } else {
              // SET STATUS
              rex_sql::factory()->setDebug(0)->setQuery("UPDATE " . rex::getTable('project_manager_domain') . " SET status = ?, updatedate = NOW()WHERE id = ?", [0, $project_manager_domain[0]['id']]);
            }
            
          } else {
            // SET STATUS
            rex_sql::factory()->setDebug(0)->setQuery("UPDATE " . rex::getTable('project_manager_domain') . " SET status = ?, updatedate = NOW() WHERE id = ?", [-1, $project_manager_domain[0]['id']]);
          }
          rex_sql::factory()->setDebug(0)->setQuery("UPDATE " . rex::getTable('project_manager_domain') . " SET updatedate = NOW() WHERE id = ?", [$project_manager_domain[0]['id']]);       
          
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        $response = json_encode($params, true);
        echo $response;
        exit();
        
    }
}

?>