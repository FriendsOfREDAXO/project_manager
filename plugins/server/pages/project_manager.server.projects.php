<?php
$domain = rex_request('domain', 'string', "");
$csrfToken = rex_csrf_token::factory('project_manager_server_details');

  
$sel_editor = new rex_select();
$sel_editor->setName('domain');
$sel_editor->setId('rex-project-manager-domain');
$sel_editor->setAttribute('class', 'form-control selectpicker');
$sel_editor->setAttribute('data-live-search', 'true');
$sel_editor->setSize(1);
$sel_editor->setSelected($domain);
$sel_editor->addDBSqlOptions("select domain as name, domain as id FROM rex_project_manager_domain ORDER BY domain");

$formElements = [];
$n = [];
$n['label'] = '<label for="rex-id-editor">' .$this->i18n('project_manager_server_select_project')  . '</label>';
$n['field'] = $sel_editor->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content = $fragment->parse('core/form/form.php');


$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="sendit">' . 'Anzeigen' . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('project_detail'));
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

$content = '
  <form id="rex-form-system-setup" action="' . rex_url::currentBackendPage() . '" method="get">
  <input type="hidden" name="func" value="updateinfos" />
  <input type="hidden" name="page" value="project_manager/server/projects" />
  ' . $csrfToken->getHiddenField() . '
      ' . $content . '
  </form>';

echo $content;  

if($domain) {

  // Domain-Übersicht ANFANG //
  $query = 'SELECT * FROM `rex_project_manager_logs` AS L 
            INNER JOIN `rex_project_manager_domain` as D
            ON D.id = L.domain_id
            WHERE domain = ? ORDER BY L.id DESC LIMIT 1';
  
  
  try {
    $result = rex_sql::factory()->setDebug(0)->getArray($query, [$domain]);
  } catch (rex_sql_exception $e) {
    rex_logger::logException($e);
  }
  
  if (!empty($result)){
    $item = $result[0];
  }
  
  if ((isset($item)) && (null !== $item)){
    $raw = json_decode($item['raw'], true);
  }
  
  if((isset($raw)) && (is_array($raw))) {
    
    $ssl = $item['is_ssl'];
    $protocol = ($ssl == 1) ? "https://" : "http://";
    
    $refresh = '';
    $refresh = '<div class="btn-toolbar"><button data-func="updateData"  data-protocol="'.$protocol.'"  data-domain="'.$item['domain'].'" data-api_key="'.$item['api_key'].'" data-param="'.$item['param'].'" class="btn btn-save btn-project-manager-update pull-right" type="submit" name="del_btn"><i class="fa fa-refresh"></i> Projekt Aktualisieren</button></div>';
    
    echo $refresh;
    
    if ($item['cms'] == 4) {
      $content0 = '';
      
      // VERSIONS
      $output = '<table class="table table-striped"><thead><tr><th>Information</th><th>Version / Status</th></tr></thead><tbody>';
      $output .= '<tr><td>Projekt Manager Version</td><td>'.$raw['pm_version'].'</td></tr>';
      $output .= '<tr><td>Projekt Manager Client Version</td><td>'.$raw['client_version'].'</td></tr>';
      
      if ($item['status'] == "1") {
        $output .= '<tr><td>Projekt Manager Client Abruf</td><td><span class="hidden">1</span><span class="rex-icon fa-check text-success" title="'.$this->i18n('project_manager_server_status_code_1').'"></span> '.$this->i18n('project_manager_server_status_code_1').'</td></tr>';
      } else if ($item['status'] == "0") {
        $output .= '<tr><td>Projekt Manager Client Abruf</td><td><span class="hidden">2</span><span class="rex-icon fa-question text-warning" title="'.$this->i18n('project_manager_server_status_code_0').'"></span> '.$this->i18n('project_manager_server_status_code_0').'</td></tr>';
      } else if ($item['status'] == "-1") {
        $output .= '<tr><td>Projekt Manager Client Abruf</td><td><span class="hidden">3</span><span class="rex-icon fa-exclamation-triangle text-danger" title="'.$this->i18n('project_manager_server_status_code_minus_1').'"></span> '.$this->i18n('project_manager_server_status_code_minus_1').'</td></tr>';
      } else if ($item['status'] == "2") {
        $output .= '<tr><td>Projekt Manager Client Abruf</td><td><span class="hidden">3</span><span class="rex-icon fa-arrow-right text-danger" title="'.$this->i18n('project_manager_server_status_code_2').'"></span> '.$this->i18n('project_manager_server_status_code_2').'</td></tr>';
      }
      
      $output .= '<tr><td>REDAXO Version</td><td>'.$raw['rex_version'].'</td></tr>';
      $output .= '<tr><td>PHP Version</td><td>'.$raw['php_version'].'</td></tr>';
      $output .= '<tr><td>MySQL Version</td><td>'.$raw['mysql_version'].'</td></tr>';
      $output .= '</tbody></table>';     
  
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'danger', false);
      $fragment->setVar('title', "Informationen", false);
      $fragment->setVar('body', $output, false);
      $content0 .= '<div class="col-md-12">'.$fragment->parse('core/page/section.php').'</div>';
  
      echo '<div class="row">'.$content0.'</div>';
      
      $content0 = '';
      
      // UPDATES
      $output = '<table class="table table-striped"><thead><tr><th>Letzte Änderung</th><th>Datum</th></tr></thead><tbody>';
      
      if (array_key_exists('update_article', $raw) && array_key_exists('update_media', $raw)) {
        $output .= '<tr><td>Artikel</td><td>'.date('d.m.Y H:i:s', $raw['update_article']).'</td></tr>';
        $output .= '<tr><td>Medienpool</td><td>'.date('d.m.Y H:i:s', $raw['update_media']).'</td></tr>';
      }
      
      $output .= '<tr><td>Synchronisierung mit Projekt Manager</td><td>'.date('d.m.Y H:i:s', $item['updatedate']).'</td></tr>';
      $output .= '</tbody></table>';
      
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'info', false);
      $fragment->setVar('title', "Änderungen", false);
      $fragment->setVar('body', $output, false);
      $fragment->setVar('collapse', true);
      $fragment->setVar('collapsed', false);
      $content0 .= '<div class="col-md-12">'.$fragment->parse('core/page/section.php').'</div>';
      
      echo '<div class="row">'.$content0.'</div>';
    }
    
    if ($item['cms'] == 5) {

      $content1 = '';      
      
      $output = '<table class="table table-striped"><thead><tr><th>Information</th><th>Version / Status</th></tr></thead><tbody>';
      $output .= '<tr><td>Projekt Manager Version</td><td>'.$raw['pm_version'].'</td></tr>';
      $output .= '<tr><td>Projekt Manager Client Version</td><td>'.$raw['client_version'].'</td></tr>';
      
      if ($item['status'] == "1") {
        $output .= '<tr><td>Projekt Manager Client Abruf</td><td><span class="hidden">1</span><span class="rex-icon fa-check text-success" title="'.$this->i18n('project_manager_server_status_code_1').'"></span> '.$this->i18n('project_manager_server_status_code_1').'</td></tr>';
      } else if ($item['status'] == "0") {
        $output .= '<tr><td>Projekt Manager Client Abruf</td><td><span class="hidden">2</span><span class="rex-icon fa-question text-warning" title="'.$this->i18n('project_manager_server_status_code_0').'"></span> '.$this->i18n('project_manager_server_status_code_0').'</td></tr>';
      } else if ($item['status'] == "-1") {
        $output .= '<tr><td>Projekt Manager Client Abruf</td><td><span class="hidden">3</span><span class="rex-icon fa-exclamation-triangle text-danger" title="'.$this->i18n('project_manager_server_status_code_minus_1').'"></span> '.$this->i18n('project_manager_server_status_code_minus_1').'</td></tr>';
      } else if ($item['status'] == "2") {
        $output .= '<tr><td>Projekt Manager Client Abruf</td><td><span class="hidden">3</span><span class="rex-icon fa-arrow-right text-danger" title="'.$this->i18n('project_manager_server_status_code_2').'"></span> '.$this->i18n('project_manager_server_status_code_2').'</td></tr>';
      }      
      
      $output .= '<tr><td>REDAXO Version</td><td>'.$raw['rex_version'].'</td></tr>';
      $output .= '<tr><td>PHP Version</td><td>'.$raw['php_version'].'</td></tr>';
      $output .= '<tr><td>MySQL Version</td><td>'.$raw['mysql_version'].'</td></tr>';
      if ($raw['client_version'] >= "1.0.6") {
        if ($raw['debug'] == 1)
          $output .= '<tr><td>Debug Modus</td><td><i title="" class="rex-icon fa-exclamation-triangle text-danger"></i> aktiv</td></tr>';
        if ($raw['debug'] == 0)
          $output .= '<tr><td>Debug Modus</td><td><i title="" class="rex-icon fa-check text-success"></i> nicht aktiv</td></tr>';
      }
      
      if ($raw['client_version'] >= "1.1.0") {
        if (!empty($raw['rex_url_backend'])) 
          $output .= '<tr><td>REDAXO Backend</td><td><a href="'.$protocol.$item['domain'].$raw['rex_url_backend'].'" target="_blank" title=""> <i class="fa fa-external-link"></i> '.$raw['rex_url_backend'].'</a></td></tr>';
      }    
      
      if ($item['maintenance'] == "1") {
        $output .= '<tr><td>Wartungsvertrag</td><td><span class="hidden">1</span><span class="rex-icon fa-file-text-o text-success" title="'.$this->i18n('project_manager_server_maintenance_1').'"></span> '.$this->i18n('project_manager_server_maintenance_1').'</td></tr>';
      } else if ($item['maintenance'] == "0") {
        $output .= '<tr><td>Wartungsvertrag</td><td><span class="hidden">2</span><span class="rex-icon fa-file-text-o text-danger" title="'.$this->i18n('project_manager_server_maintenance_0').'"></span> '.$this->i18n('project_manager_server_maintenance_0').'</td></tr>';
      } else if ($item['maintenance'] == "") {
        $output .= '<tr><td>Wartungsvertrag</td><td><span class="hidden">3</span><span class="rex-icon fa-question text-warning" title="'.$this->i18n('project_manager_server_maintenance_2').'"></span> '.$this->i18n('project_manager_server_maintenance_2').'</td></tr>';
      }   
      
      
      $output .= '</tbody></table>';
      
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'danger', false);
      $fragment->setVar('title', "Informationen", false);
      $fragment->setVar('body', $output, false);
      $content1 .= '<div class="col-md-12">'.$fragment->parse('core/page/section.php').'</div>';
      
      echo '<div class="row">'.$content1.'</div>';
      
      $content1 = '';
      
      // UPDATES
      $output = '<table class="table table-striped"><thead><tr><th>Letzte Änderung</th><th>Datum</th></tr></thead><tbody>';
      $output .= '<tr><td>Artikel</td><td>'.rex_formatter::format($raw['article'][0]['updatedate'],'date','d.m.Y H:i:s').'</td></tr>';
      $output .= '<tr><td>Medienpool</td><td>'.rex_formatter::format($raw['media'][0]['updatedate'],'date','d.m.Y H:i:s').'</td></tr>';
      $output .= '<tr><td>Synchronisierung mit Projekt Manager</td><td>'.rex_formatter::format($item['updatedate'],'date','d.m.Y H:i:s').'</td></tr>';
      $output .= '</tbody></table>';
      
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'info', false);
      $fragment->setVar('title', "Änderungen", false);
      $fragment->setVar('body', $output, false);
      $fragment->setVar('collapse', true);
      $fragment->setVar('collapsed', false);
      $content1 .= '<div class="col-md-6">'.$fragment->parse('core/page/section.php').'</div>';
            
      
      // USER
      $user = $raw['user'];
      $output = '<table class="table table-striped"><thead><tr><th>Name</th><th>Benutzer</th><th>Letzter Login</th></tr></thead><tbody>';
      foreach ($user as $login) {
        $output .= '<tr>';
        $output .= '<td>'.$login["name"].'</td>';
        $output .= '<td>'.$login["login"].'</td>';
        $output .= '<td>'.rex_formatter::format($login["lastlogin"],'date','d.m.Y H:i:s').'</td>';
        $output .= '</tr>';
      }
      $output .= '</tbody></table>';
      
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'info', false);
      $fragment->setVar('title', "Benutzer", false);
      $fragment->setVar('body', $output, false);
      $fragment->setVar('collapse', true);
      $fragment->setVar('collapsed', false);
      $content1 .= '<div class="col-md-6">'.$fragment->parse('core/page/section.php').'</div>';
      
      echo '<div class="row">'.$content1.'</div>';
      
      
      $content2 = '';
        
      // ARTICLES
      $articles = $raw['article'];
  
      $output = '<table class="table table-striped"><thead><tr><th>Artikel</th><th>Benutzer</th><th>Änderungsdatum</th></tr></thead><tbody>';
      foreach ($articles as $article) {
        $output .= '<tr>';
        $output .= '<td>'.$article["name"].'</td>';
        $output .= '<td>'.$article["updateuser"].'</td>';
        $output .= '<td>'.rex_formatter::format($article["updatedate"],'date','d.m.Y H:i:s').'</td>';
        $output .= '</tr>';
      }
      $output .= '</tbody></table>';
  
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'info', false);
      $fragment->setVar('title', "Artikel", false);
      $fragment->setVar('body', $output, false);
      $fragment->setVar('collapse', true);
      $fragment->setVar('collapsed', true);
      $content2 .= '<div class="col-md-6">'.$fragment->parse('core/page/section.php').'</div>';      
      
      
      // MEDIAFILES
      $media = $raw['media'];
      $output = '<table class="table table-striped"><thead><tr><th>Datei</th><th>Benutzer</th><th>Änderungsdatum</th></tr></thead><tbody>';
      foreach ($media as $file) {
        $output .= '<tr>';
        $output .= '<td>'.$file["filename"].'</td>';
        $output .= '<td>'.$file["updateuser"].'</td>';
        $output .= '<td>'.rex_formatter::format($file["updatedate"],'date','d.m.Y H:i:s').'</td>';
        $output .= '</tr>';
      }
      $output .= '</tbody></table>';
  
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'info', false);
      $fragment->setVar('title', "Medienpool", false);
      $fragment->setVar('body', $output, false);
      $fragment->setVar('collapse', true);
      $fragment->setVar('collapsed', true);
      $content2 .= '<div class="col-md-6">'.$fragment->parse('core/page/section.php').'</div>';
  
      echo '<div class="row">'.$content2."</div>";
  
      
      $content2 = '';
      
      // Modules
      $modules = $raw['module'];
      $output = '<table class="table table-striped"><thead><tr><th>Modul</th><th>Änderungsdatum</th></tr></thead><tbody>';
      foreach ($modules as $file) {
        $output .= '<tr>';
        $output .= '<td>'.$file["name"].'</td>';
        $output .= '<td>'.rex_formatter::format($file["updatedate"],'date','d.m.Y H:i:s').'</td>';
        $output .= '</tr>';
      }
      $output .= '</tbody></table>';
      
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'info', false);
      $fragment->setVar('title', "Module", false);
      $fragment->setVar('body', $output, false);
      $fragment->setVar('collapse', true);
      $fragment->setVar('collapsed', true);
      $content2 .= '<div class="col-md-12">'.$fragment->parse('core/page/section.php').'</div>';
      
      error_reporting(E_ALL);
      
      echo '<div class="row">'.$content2."</div>";
      
      $content3 = '';
      
      // ADDONS
      $addons = $raw['rex_addons'];
      $i = 0;
      $output = '<table class="table table-striped"><thead><tr><th>Name</th><th>aktiv?</th><th>Version</th><th>Installer</th></tr></thead><tbody>';
      foreach($addons as $key => $value) {

        if ($value['status'] == 1) $status = 'Ja';
        if ($value['status'] == 0) $status = 'Nein';
        
        $output .= '<tr>';
        $output .= '<td>'.$value['name'].'</td>';
        $output .= '<td>'.$status.'</td>';
        
        $skip_addon_config = '';
        $skip_addon_config = rex_config::get('project_manager/server', 'skip_addon');
        $skip_addon_versions = '';        
        
        if ($skip_addon_config != "") {
        	$skip_addons = explode(',', $skip_addon_config);
        	if (in_array($value['name'], $skip_addons)) {
        		$output .= '<td>'.$value['version_current'].'</td>';
        		$output .= '<td>'.$value['version_latest'].'</td>';
        		$output .= '</tr>';
        		continue;
        	}
        }
        
        if(rex_string::versionCompare($value['version_current'], $value['version_latest'], '<')) {
          
            $skip_addon_version_config = rex_config::get('project_manager/server', 'skip_addon_version');
            
            if ($skip_addon_version_config != "") $skip_addon_versions = explode(',', $skip_addon_version_config);
            
            $skip = false;
            if (is_array($skip_addon_versions)) {
              foreach ($skip_addon_versions as $skip_addon_version) {
                if (strpos($value['version_latest'],'-'.$skip_addon_version)) {
                  $skip = true;
                }
              }
            }
            
            if ($value['version_latest'] == 0) $skip = true;
            
            if ($skip === false) {
             $output .= '<td ><i title="" class="rex-icon fa-exclamation-triangle text-danger"></i> '.$value['version_current'].'</td>';
             $i++;
            } else {
              $output .= '<td>'.$value['version_current'].'</td>';
            }
            
        } else {
          $output .= '<td>'.$value['version_current'].'</td>';
        }
        
        $output .= '<td>'.$value['version_latest'].'</td>';
        $output .= '</tr>';
      }
      $output .= '</tbody></table>';
      
      $icon = '';
      if ($i > 0 )  $icon = ' <i title="" class="rex-icon fa-exclamation-triangle"></i>';
      $updates = "(".$i."&nbsp;". $this->i18n('project_manager_server_updates') ." ".$this->i18n('updates_necessary').")";
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'info', false);
      $fragment->setVar('title', "Installierte Addons ".$updates .' '.$icon, false);
      $fragment->setVar('body', $output, false);
      $fragment->setVar('collapse', true);
      $fragment->setVar('collapsed', true);
      $content3 .= '<div class="col-md-12">'.$fragment->parse('core/page/section.php').'</div>';
      
      echo '<div class="row">'.$content3."</div>";
      $content3 = "";
      
      
      // DOMAINS
      $domains = $raw['domains'];
      
      $output = '<table class="table table-striped"><thead><tr><th>Domain</th><th>URL</th><th>404</th></tr></thead><tbody>';
      foreach($domains as $key => $value) {
        $output .= '<tr>';
        $output .= '<td>'.$value['name'].'</td>';
        $output .= '<td>'.$value['url'].'</td>';
        $output .= '<td>'.$value['url_404'].'</td>';
        $output .= '</tr>';
      }
      $output .= '</tbody></table>';
      
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'info', false);
      $fragment->setVar('title', "Domains in dieser Installation", false);
      $fragment->setVar('body', $output, false);
      $fragment->setVar('collapse', true);
      $fragment->setVar('collapsed', true);
      $content3 .= '<div class="col-md-12">'.$fragment->parse('core/page/section.php').'</div>';
      
      echo '<div class="row">'.$content3.'</div>';
      $content3 = "";

      
      $icon = "";
      if (array_key_exists("syslog", $raw)) {  
        
        $output = '';
        $syslog = $raw['syslog'];
        $show_triangle = false;
        
        if (!is_null($syslog)) {
          
          
          $output .= '<table class="table table-striped"><thead><tr><th>Zeitstempel</th><th>Typ</th><th>Nachricht</th><th>Datei</th><th>Zeile</th></tr></thead><tbody>';
          
          foreach ($syslog as $entry) {
            $output .= '<tr>';
            $output .= '<td>'.$entry["timestamp"].'</td>';
            $output .= '<td>'.$entry["syslog_type"].'</td>';
            $output .= '<td>'.$entry["syslog_message"].'</td>';
            $output .= '<td>'.$entry["syslog_file"].'</td>';
            $output .= '<td>'.$entry["syslog_line"].'</td>';
            $output .= '</tr>';
            
            if ($entry["syslog_type"] != 'Info') $show_triangle = true;
          }
          $output .= '</tbody></table>';
          
          if ($show_triangle == true) {
            $icon = ' <i title="" class="rex-icon fa-exclamation-triangle"></i>';
          } else {
            $icon = '';
          }
          
          
          if ($raw['client_version'] >= "1.0.6")
            $output .= '<div class="rex-form-panel-footer"><button data-func="delLog"  data-protocol="'.$protocol.'"  data-domain="'.$item['domain'].'" data-api_key="'.$item['api_key'].'" data-param="'.$item['param'].'" class="btn btn-delete btn-project-manager-update" type="submit" name="del_btn"><i class="fa fa-refresh"></i> Systemlog löschen</button></div>';
            
          
          
        } 
      }else {
        $output = 'Keine Einträge vorhanden.';        
      } 
      
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'info', false);
      $fragment->setVar('title', "Syslog".$icon, false);
      $fragment->setVar('body', $output, false);
      $fragment->setVar('collapse', true);
      $fragment->setVar('collapsed', true);
      $content3 .= '<div class="col-md-12">'.$fragment->parse('core/page/section.php').'</div>';
  
  
      echo '<div class="row">'.$content3."</div>";
      $content3 = "";
    }
    
    // ----- EXTENSION POINT
    $domain = '';
    $domain_id = '';
    
    $content_ep = '';
    $content_ep .= rex_extension::registerPoint(new rex_extension_point('PROJECT_MANAGER_SERVER_DETAIL_HOOK', '', [
      'domain' => $domain,
      'domain_id' => $domain_id
    ]));      
         
    echo '<div class="row">'.$content_ep."</div>";

    
  }
}

