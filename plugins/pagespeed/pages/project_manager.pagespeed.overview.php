<?php

$showlist = true;
$data_id = rex_request('data_id', 'int', 0);
$func = rex_request('func', 'string');
$csrf_token = (rex_csrf_token::factory('cronjob'))->getValue();
$csrf = rex_csrf_token::factory('project_manager');

###############
###
### LISTVIEW
###
###############
if ($showlist) {
					
//   $sql = 'SELECT * FROM (SELECT * FROM  '. rex::getTable('project_manager_domain') . ' ORDER BY domain) AS D
// 					LEFT JOIN (
//                    SELECT domain, MAX(score_desktop) AS psi_score_desktop, MAX(score_mobile) AS psi_score_mobile FROM ' . rex::getTable('project_manager_domain_psi') . '
// 					WHERE domain IN (SELECT domain FROM ' . rex::getTable('project_manager_domain_psi') . ' GROUP BY domain)) as PSI
// 			ON D.domain = PSI.domain
// 					ORDER BY D.domain';
	  $sql = 'SELECT * FROM (SELECT * FROM  '. rex::getTable('project_manager_domain') . ' ORDER BY domain ASC) AS D
						LEFT JOIN (
						  					SELECT status as status_psi, createdate as createdate_psi, domain, score_desktop AS psi_score_desktop, score_mobile AS psi_score_mobile
						  					FROM ' . rex::getTable('project_manager_domain_psi') . '
											 ) as PSI
						ON D.domain = PSI.domain
            GROUP by D.domain
            ORDER BY name ASC
            ';
	  
	$items = rex_sql::factory()->getArray($sql);
	
	$sql2 = 'SELECT * FROM  '. rex::getTable('cronjob').'
          WHERE type = "rex_cronjob_project_manager_pagespeed"';
	$cronjob = rex_sql::factory()->getArray($sql2);
	$cronjobId = $cronjob[0]['id'];
	
	$refresh = '';
	if ($cronjobId != NULL) {
	  $refresh = '<a href="/redaxo/index.php?page=project_manager/pagespeed/overview/#" data-cronjob="/redaxo/index.php?page=cronjob/cronjobs&func=execute&oid='.$cronjobId.'&_csrf_token='.$csrf_token.'" target="_blank" class="pull-right callCronjob"><i class="fa fa-refresh"></i> PageSpeed Daten aktualisieren</a>';
	}
	echo rex_view::info("Anzahl der Domains und Projekte: ".count($items) . $refresh);
	
  $list = rex_list::factory($sql, 1000);
  $list->addTableAttribute('class', 'table-striped');
  $list->setNoRowsMessage($this->i18n('project_manager_pagespeed_domain_norows_message'));

  $list->setColumnFormat('id', 'Id');
  $list->addParam('page', 'project_manager/server');
  
  $list->setColumnParams('id', ['data_id' => '###id###', 'func' => 'edit']);
  $list->setColumnSortable('id');
  
  $list->removeColumn('id');
  $list->removeColumn('is_ssl');
  $list->removeColumn('description');
  $list->removeColumn('api_key');
  $list->removeColumn('tags');
  $list->removeColumn('cms');
  $list->removeColumn('cms_version');
  $list->removeColumn('createdate');
  $list->removeColumn('rex_version');  
  $list->removeColumn('status');
  $list->removeColumn('http_code');
  $list->removeColumn('is_ssl');
  $list->removeColumn('domain');
  $list->removeColumn('updatedate');
  $list->removeColumn('createdate');
  $list->removeColumn('psi_domain');
  $list->removeColumn('psi_score_desktop');
  $list->removeColumn('psi_score_mobile');
  $list->removeColumn('maintenance');
  
  $list->setColumnLabel('name', $this->i18n('project_manager_pagespeed_name'));
  $list->setColumnParams('name', ['page' => 'project_manager/server/projects', 'func' => 'updateinfos', 'domain' => '###domain###']);
  
  $list->setColumnLabel('createdate_psi', $this->i18n('project_manager_pagespeed_updatedate'));
  $list->setColumnFormat('createdate_psi', 'custom', function ($params) {
    return (rex_formatter::format($params['list']->getValue('createdate_psi'),'date','d.m.Y H:i:s'));
  });
  
  // icon column (Domain hinzufügen bzw. bearbeiten)
  $thIcon = '<a href="'.$list->getUrl(['func' => 'domain_add']).'"><i class="rex-icon rex-icon-add-category"></i></a>';
  $tdIcon = '<i class="rex-icon rex-icon-structure-root-level"></i>';
  $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
  $list->setColumnParams($thIcon, ['func' => 'domain_edit', 'id' => '###id###']);
  $list->setColumnFormat($thIcon, 'custom', function ($params) {
      $filename = '';
      if (file_exists(rex_plugin::get('project_manager', 'server')->getAssetsPath('favicon/'.$params['list']->getValue('domain').'.png'))) {
        $filename = rex_plugin::get('project_manager', 'server')->getAssetsUrl('favicon/'.$params['list']->getValue('domain').'.png');
        return '<a href="http://'.$params['list']->getValue('domain').'/" target="_blank"><img src="'.$filename.'" /></a>';
      } else {
        return '<a href="http://'.$params['list']->getValue('domain').'/" target="_blank"><i class="fa fa-sitemap"></i></a>';
      }
  });
    
  $list->addColumn($this->i18n('project_manager_pagespeed_domain'), '###domain###', 3);
  //$list->setColumnParams($this->i18n('project_manager_pagespeed_domain'), ['page' => 'project_manager/server/projects', 'func' => 'updateinfos', 'domain' => '###domain###']);
  $list->setColumnFormat($this->i18n('project_manager_pagespeed_domain'), 'custom', function ($params) {
    return '<a href="http://'.$params['list']->getValue('domain').'/" target="_blank">'.$params['list']->getValue('domain').'</a>';
  });

  
  $list->setColumnLabel('status_psi', $this->i18n('status'));
  $list->setColumnFormat('status_psi', 'custom', function ($params) {
    if ($params['list']->getValue('status_psi') == "1") {
      return '<span class="hidden">1</span><span class="rex-icon fa-check text-success"></span>';
    } else if ($params['list']->getValue('status_psi') == "0") {
      return '<span class="hidden">2</span><span class="rex-icon fa-question text-warning"></span>';
    } else if ($params['list']->getValue('status_psi') == "-1") {
      return '<span class="hidden">3</span><span class="rex-icon fa-exclamation-triangle text-danger"></span>';
    } else if ($params['list']->getValue('status_psi') == "2") {
      return '<span class="hidden">3</span><span class="rex-icon fa-arrow-right text-danger"></span>';
    } else {
      if ($params['list']->getValue('is_ssl') == 1)
        return '<a href="https://www.'.$params['list']->getValue('domain').'/?rex-api-call=project_manager&api_key='.$params['list']->getValue('api_key').'"><span class="rex-icon fa-question"></span></a>';
        
        if ($params['list']->getValue('is_ssl') == 0)
          return '<a href="http://www.'.$params['list']->getValue('domain').'/?rex-api-call=project_manager&api_key='.$params['list']->getValue('api_key').'"><span class="rex-icon fa-question"></span></a>';
    }
  });
  
  $list->setColumnLayout('status', ['<th data-sorter="digit">###VALUE###</th>', '<td>###VALUE###</td>']);
    
    
  $list->addColumn("Pagespeed", false, -1, ['<th>PageSpeed</th>', '<td data-title="psi" width="150px">###VALUE###</td>']);
  $list->setColumnFormat("Pagespeed", 'custom', function ($params) {

      if($params['list']->getValue('psi_score_desktop') < 0.7) {
        $return = '<span class="rex-icon fa-desktop text-danger"></span> '.$params['list']->getValue('psi_score_desktop');
      } else if($params['list']->getValue('psi_score_desktop') < 0.9) {
        $return = '<span class="rex-icon fa-desktop text-warning"></span> '.$params['list']->getValue('psi_score_desktop');
      } else {
        $return = '<span class="rex-icon fa-desktop text-success"></span> '.$params['list']->getValue('psi_score_desktop');
      }
      $return .= " | ";
      if($params['list']->getValue('psi_score_mobile') < 0.7) {
        $return .= '<span class="rex-icon fa-mobile text-danger"></span> '.$params['list']->getValue('psi_score_mobile');
      } else if($params['list']->getValue('psi_score_mobile') < 0.9) {
        $return .= '<span class="rex-icon fa-mobile text-warning"></span> '.$params['list']->getValue('psi_score_mobile');
      } else {
        $return .= '<span class="rex-icon fa-mobile text-success"></span> '.$params['list']->getValue('psi_score_mobile');
      }
      return $return;
  });  

    
  $content = $list->get();
  $content = str_replace('<table class="', '<table class="project_manager-tablesorter ', $content);
    
  $fragment = new rex_fragment();
  $fragment->setVar('title', $this->i18n('projects'));
  $fragment->setVar('content', $content, false);
  echo $fragment->parse('core/page/section.php');
}
