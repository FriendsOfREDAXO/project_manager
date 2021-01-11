<?php

// Aufruf: 
// /?rex-api-call=project_manager&api_key=###

class rex_api_project_manager extends rex_api_function
{
    protected $published = true;

    public function execute()
    {
        rex_response::cleanOutputBuffers();
        $api_key = rex_request('api_key','string');
        $func = rex_request('func','string');
        
        if($api_key == rex_config::get('project_manager/client', 'project_manager_api_key')) {
          
            # SYSLOG LOESCHEN            
            $logFile = rex_logger::getPath();
            if ($func == 'delLog') {
              
              rex_logger::close();
              if (rex_log_file::delete($logFile)) {
                $params['delLog'] = '1';
              } else {
                $params['delLog'] = '0';
              }
              
            } else {

              # REDAXO / SERVER / ALLGEMEIN
  
              $params['pm_version']    		= rex_addon::get('project_manager')->getProperty('version');
              $params['client_version']   = rex_plugin::get('project_manager', 'client')->getProperty('version');
              $params['rex_version']      = rex::getVersion();
              $params['cms']              = "REDAXO";
              $params['cms_version']      = rex::getVersion();
              $params['rex_url_backend']  = rex_url::backend();
              $params['php_version']      = phpversion();
              $params['mysql_version']    = rex_sql::getServerVersion();
              $params['status']           = 1;
              $params['debug']            = rex::isDebugMode();
  
              # / REDAXO / SERVER / ALLGEMEIN
  
              # ADDONS
  
              $rex_addons = rex_addon::getInstalledAddons();
              
              rex_install_webservice::deleteCache();
              
              try {
                  $installer_addons = rex_install_packages::getAddPackages();
              } catch (rex_functional_exception $e) {
                  $params['message'][] = $e->getMessage();
              }
          
              foreach($rex_addons as $key => $addon) {
                  $params['rex_addons'][$key]['name'] = $addon->getName();
                  $params['rex_addons'][$key]['install'] = $addon->getProperty('install');
                  $params['rex_addons'][$key]['status'] = $addon->getProperty('status');
                  $params['rex_addons'][$key]['version_current'] = $addon->getProperty('version');             
                  if(!empty($installer_addons[$key])) {
                      $params['rex_addons'][$key]['version_latest'] = current($installer_addons[$key]["files"])["version"]; 
                  } else {
                      $params['rex_addons'][$key]['version_latest'] = 0; 
                  }
              }
  
              # / ADDONS
  
              # DOMAINS / WEBSITES
  
              $params['domains'][rex::getServer()]['name'] = rex::getServer();
              $params['domains'][rex::getServer()]['url'] = rex_getUrl(rex_article::getSiteStartArticleId());
              $params['domains'][rex::getServer()]['url_404'] = rex_getUrl(rex_article::getNotfoundArticleId());
  
              if(rex_addon::get('yrewrite')->isAvailable()) {
  
                  $yrewrite_domains = rex_yrewrite::getDomains(true);
                  foreach($yrewrite_domains as $key => $domain) {
                      $params['domains'][$key]['name'] = $domain->getName();
                      $params['domains'][$key]['url'] = $domain->getUrl();
                      $params['domains'][$key]['url_404'] = rex_yrewrite::getFullUrlByArticleId($domain->getNotfoundId());
                  }
              }
  
              # / DOMAINS / WEBSITES
  
              # SYSLOG 
  
              if (version_compare(rex::getVersion(), '5.9') >= 0) {
                $log = new rex_log_file(rex_path::log('system.log'));
              } else {
                $log = new rex_log_file(rex_path::coreData('system.log'));
              }
  
              $i = 0;
              foreach (new LimitIterator($log, 0, 30) as $entry) {
                  $data = $entry->getData();
                  $params['syslog'][$i]['timestamp'] = $entry->getTimestamp('%d.%m.%Y %H:%M:%S');
                  $params['syslog'][$i]['syslog_type'] = $data[0];
                  $params['syslog'][$i]['syslog_message'] = $data[1];
                  $params['syslog'][$i]['syslog_file'] = (isset($data[2]) ? $data[2] : '');
                  $params['syslog'][$i]['syslog_line'] = (isset($data[3]) ? $data[3] : '');
                  $i++;
              }
  
              # / SYSLOG
  
  
              # USER 
              $params['user'] = rex_sql::factory()->getArray('SELECT `name`, `login`, `email`, `status`, `admin`, `lasttrydate`, `lastlogin` FROM '.rex::getTablePrefix().'user ORDER BY `admin`, `id`');
              # / USER
  
              # TODO: Letzte Artikel 
              $params['article'] = rex_sql::factory()->getArray('SELECT `name`, `updateuser`, `updatedate`, `pid` FROM `'.rex::getTablePrefix().'article` ORDER BY `updatedate` DESC LIMIT 10');
              # / Letzte Artikel
  
              # TODO: Letzte Medien
              $params['media'] = rex_sql::factory()->getArray('SELECT `filename`, `updateuser`, `updatedate` FROM `'.rex::getTablePrefix().'media` ORDER BY `updatedate` DESC LIMIT 10');
              # / Letzte Medien
              
              # Modules
              $params['module'] = rex_sql::factory()->getArray('SELECT `name`, `updateuser`, `updatedate` FROM `'.rex::getTablePrefix().'module` ORDER BY `name` ASC');
              # / Modules
            }
            
            

        } else {
            $params['pm_version']    = rex_addon::get('project_manager')->getProperty('version');
            $params['cms']              = "REDAXO";
            $params['status']       = 0;
            $params['message'][]    = "Falscher API-Schlüssel.";
        }

        // TODO: EP, um weitere Parameter einzuhängen
        
        header('Content-Type: application/json; charset=UTF-8');  
        $response = json_encode($params, true);
        echo $response;
        exit();
    }
}

?>