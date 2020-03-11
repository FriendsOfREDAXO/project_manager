<?php 
$domain = rex_request('domain', 'string', "");

if($domain) {
  $output = '';
  
  $query = 'SELECT * FROM `rex_project_manager_domain_hosting` AS H
              INNER JOIN `rex_project_manager_domain` as D
              ON D.domain = H.domain
              WHERE H.domain = ? 
              LIMIT 1';
  
  $result = rex_sql::factory()->setDebug(0)->getArray($query, [$domain]);
  
  if (count($result) > 0) {
    
    $item = $result[0];
    $raw = json_decode($item['raw'], true);
  
    if(is_array($raw)) {
      
      if (is_numeric($raw['validTo'])) {
        
        if ($raw['validTo'] < (time() + 2764800) ) {
          $validTo = '<span data-color="alert-warning">'.rex_formatter::format($raw['validTo'],'date','d.m.Y H:i:s').'</span>';
          $validFrom = rex_formatter::format($raw['validFrom'],'date','d.m.Y H:i:s');
        } else if ($raw['validTo'] < time()) {
          $validTo = '<span data-color="alert-danger">'.rex_formatter::format($raw['validTo'],'date','d.m.Y H:i:s').'</span>';
          $validFrom = rex_formatter::format($raw['validFrom'],'date','d.m.Y H:i:s');
        } else {
          $validTo = rex_formatter::format($raw['validTo'],'date','d.m.Y H:i:s');
          $validFrom = rex_formatter::format($raw['validFrom'],'date','d.m.Y H:i:s');
        }
      } else {
        $validFrom = "-";
        $validTo = "-";
      }
           
      $output = '<table class="table table-striped"><thead><tr><th>'.$this->i18n('organisation').'</th><th>'.$this->i18n('isp').'</th><th>'. $this->i18n('project_manager_hosting_ip').'</th><th>'.$this->i18n('validFrom').'</th><th>'.$this->i18n('validTo').'</th></tr></thead><tbody>';
      $output .= '<tr><td>'.(isset($raw['org']) ? $raw['org'] : '').'</td><td>'.(isset($raw['isp']) ? $raw['isp'] : '').'<br />'.(isset($raw['zip']) ? $raw['zip'] : '').' '.(isset($raw['city']) ? $raw['city'] : '').'<br />'.(isset($raw['country']) ? $raw['country'] : '').'</td><td>'.(isset($item['ip']) ? $item['ip']: '').'</td><td class="project-manager rex-table-validFrom">'.$validFrom.'</td><td class="project-manager rex-table-validTo">'.$validTo.'</td></tr>';
      $output .= '</tbody></table>';
    }
    
  } else {
    $output = "Keine Hostingdaten vorhanden!";
  }

    return $output;

}


