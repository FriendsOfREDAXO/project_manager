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
  
  $item = rex_sql::factory()->setDebug(0)->getArray($query, [$domain])[0];
  $raw = json_decode($item['raw'], true);
  
  if(is_array($raw)) {
    
    if ($item['cms'] == 4) {
      $content0 = '';
      
      // VERSIONS
      $output = '<table class="table table-striped"><thead><tr><th>Version</th><th>Version</th></tr></thead><tbody>';
      $output .= '<tr><td>Projekt Manager Version</td><td>'.$raw['pm_version'].'</td></tr>';
      $output .= '<tr><td>Projekt Manager Client Version</td><td>'.$raw['client_version'].'</td></tr>';
      $output .= '<tr><td>REDAXO Version</td><td>'.$raw['rex_version'].'</td></tr>';
      $output .= '<tr><td>PHP Version</td><td>'.$raw['php_version'].'</td></tr>';
      $output .= '<tr><td>MySQL Version</td><td>'.$raw['mysql_version'].'</td></tr>';
      $output .= '</tbody></table>';     
  
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'danger', false);
      $fragment->setVar('title', "Versionen", false);
      $fragment->setVar('body', $output, false);
      $content0 .= '<div class="col-md-12">'.$fragment->parse('core/page/section.php').'</div>';
  
      echo '<div class="row">'.$content0.'</div>';
    }
    
    if ($item['cms'] == 5) {
      
      $content1 = '';
      
      $output = '<table class="table table-striped"><thead><tr><th>Version</th><th>Version</th></tr></thead><tbody>';
      $output .= '<tr><td>Projekt Manager Version</td><td>'.$raw['pm_version'].'</td></tr>';
      $output .= '<tr><td>Projekt Manager Client Version</td><td>'.$raw['client_version'].'</td></tr>';
      $output .= '<tr><td>REDAXO Version</td><td>'.$raw['rex_version'].'</td></tr>';
      $output .= '<tr><td>PHP Version</td><td>'.$raw['php_version'].'</td></tr>';
      $output .= '<tr><td>MySQL Version</td><td>'.$raw['mysql_version'].'</td></tr>';
      $output .= '</tbody></table>';
      
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'danger', false);
      $fragment->setVar('title', "Versionen", false);
      $fragment->setVar('body', $output, false);
      $content1 .= '<div class="col-md-6">'.$fragment->parse('core/page/section.php').'</div>';
      
      // USER
      $user = $raw['user'];
      $output = '<table class="table table-striped"><thead><tr><th>Name</th><th>Benutzer</th><th>Letzter Login</th></tr></thead><tbody>';
      foreach ($user as $login) {
        $output .= '<tr>';
        $output .= '<td>'.$login["name"].'</td>';
        $output .= '<td>'.$login["login"].'</td>';
        $output .= '<td>'.$login["lastlogin"].'</td>';
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
      $article = $raw['article'];
  
      $output = '<table class="table table-striped"><thead><tr><th>Artikel</th><th>Benutzer</th><th>Änderungsdatum</th></tr></thead><tbody>';
      foreach ($article as $item) {
        $output .= '<tr>';
        $output .= '<td>'.$item["name"].'</td>';
        $output .= '<td>'.$item["updateuser"].'</td>';
        $output .= '<td>'.$item["updatedate"].'</td>';
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
        $output .= '<td>'.$file["updatedate"].'</td>';
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
        $output .= '<td>'.$file["updatedate"].'</td>';
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
        if(rex_string::versionCompare($value['version_current'], $value['version_latest'], '<')) {
          $output .= '<td><i title="" class="rex-icon fa-exclamation-triangle"></i> '.$value['version_current'].'</td>';
          $i++;
        } else {
          $output .= '<td>'.$value['version_current'].'</td>';
        }
        $output .= '<td>'.$value['version_latest'].'</td>';
        $output .= '</tr>';
      }
      $output .= '</tbody></table>';
      
      $updates = "(".$i."&nbsp;". $this->i18n('project_manager_server_updates') ." ".$this->i18n('updates_necessary').")";
      $fragment = new rex_fragment();
      $fragment->setVar('class', 'info', false);
      $fragment->setVar('title', "Installierte Addons ".$updates, false);
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
        
        if (!is_null($syslog)) {
          
          $icon = ' <i title="" class="rex-icon fa-exclamation-triangle"></i>';
          $output .= '<table class="table table-striped"><thead><tr><th>Zeitstempel</th><th>Typ</th><th>Nachricht</th><th>Datei</th><th>Zeile</th></tr></thead><tbody>';
       
          foreach ($syslog as $entry) {
            $output .= '<tr>';
            $output .= '<td>'.$entry["timestamp"].'</td>';
            $output .= '<td>'.$entry["syslog_type"].'</td>';
            $output .= '<td>'.$entry["syslog_message"].'</td>';
            $output .= '<td>'.$entry["syslog_file"].'</td>';
            $output .= '<td>'.$entry["syslog_line"].'</td>';
            $output .= '</tr>';
          }
          
          $output .= '</tbody></table>';
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

