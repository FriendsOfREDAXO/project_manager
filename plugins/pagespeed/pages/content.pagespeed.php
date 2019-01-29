<?php 

$domain = rex_request('domain', 'string', "");


if($domain) {
  
  // Domain-Übersicht ANFANG //
  
  $query = 'SELECT * FROM `rex_project_manager_logs` AS L
              INNER JOIN `rex_project_manager_domain` as D
              ON D.id = L.domain_id
              WHERE domain = ? ORDER BY L.id DESC LIMIT 1';
  $item = rex_sql::factory()->setDebug(0)->getArray($query, [$domain])[0];
    
  $raw = json_decode($item['raw'], true);
  
  if(is_array($raw)) {
    
    // PSI
    $query = '
        SELECT * FROM (SELECT * FROM  ' . rex::getTable('project_manager_domain') . ' ORDER BY domain) AS D
                    LEFT JOIN (
                                SELECT domain, raw, score_desktop AS psi_score_desktop
                                FROM ' . rex::getTable('project_manager_domain_psi') . '
                                WHERE score_desktop NOT LIKE "") as PSI
                    ON D.domain = PSI.domain
                    LEFT JOIN (
                                SELECT domain, score_mobile AS psi_score_mobile
                                FROM ' . rex::getTable('project_manager_domain_psi') . '
                                WHERE score_mobile NOT LIKE "") as PSI2
                    ON D.domain = PSI2.domain
                    WHERE D.domain = ?
                    GROUP BY D.domain
                    ORDER BY D.domain
    ';

    $item = rex_sql::factory()->setDebug(0)->getArray($query, [$domain])[0];
    
    // Screenshot
    $raw= json_decode($item['raw'], true);
    $image = '';
    $data = '';
    $data = $raw['screenshot']['data'];
    $data = str_replace(["_", "-"], ["/", "+"], $data);
    $image = 'data:'.$raw['screenshot']['mime_type'].';base64,'.$data;
        
    if($item['psi_score_desktop'] < 70) {
      $class = '<span class="rex-icon fa-desktop text-danger"></span> ';
    } else if($item['psi_score_desktop'] < 90) {
      $class = '<span class="rex-icon fa-desktop text-warning"></span> ';
    } else {
      $class = '<span class="rex-icon fa-desktop text-success"></span> ';
    }
    
    if($item['psi_score_mobile'] < 70) {
      $classmobile = '<span class="rex-icon fa-mobile text-danger"></span> ';
    } else if($item['psi_score_mobile'] < 90) {
      $classmobile = '<span class="rex-icon fa-mobile text-warning"></span> ';
    } else {
      $classmobile = '<span class="rex-icon fa-mobile text-success"></span> ';
    }
    
    $output = '';    
    $output = '<table class="table table-striped"><thead><tr><th>Name</th><th>Result</th><th>Screenshot</th></tr></thead><tbody>';
    $output .= '<tr><td>Page Speed</td><td>'.$class.$item['psi_score_desktop'].' | '.$classmobile.$item['psi_score_mobile'].'</td><td style="width: 320px"><img src="'.$image.'" alt="" title=""/></td></tr>';
    $output .= '</tbody></table>';

    return $output;
    
  }
}


