<?php

$showlist = true;
$data_id = rex_request('data_id', 'int', 0);
$func = rex_request('func', 'string');
$csrf = rex_csrf_token::factory('project_manager_domain');


###############
###
### LISTVIEW
###
###############
if ($showlist) {

  $sql = 'SELECT * FROM (SELECT * FROM  '. rex::getTable('project_manager_domain') . ' ORDER BY domain ASC) AS D
					LEFT JOIN (
					  					SELECT  status as status_hosting, createdate as createdate_hosting, domain, ip, raw
					  					FROM ' . rex::getTable('project_manager_domain_hosting') . '
										 ) as H
					ON D.domain = H.domain
          GROUP by D.domain
          ORDER BY name ASC
          ';
  
  $items = rex_sql::factory()->getArray($sql);
  echo rex_view::info("Anzahl der Domains und Projekte: ".count($items));
  
  $list = rex_list::factory($sql, 1000);
  $list->addTableAttribute('class', 'table-striped');
  $list->setNoRowsMessage($this->i18n('project_manager_hosting_domain_norows_message'));

  
  $list->setColumnFormat('id', 'Id');
  $list->addParam('page', 'project_manager/server');
  
  $list->setColumnParams('id', ['data_id' => '###id###', 'func' => 'edit']);
  $list->setColumnSortable('id');
  
  $list->removeColumn('id');
  $list->removeColumn('description');
  $list->removeColumn('api_key');
  $list->removeColumn('cms');
  $list->removeColumn('cms_version');
  $list->removeColumn('createdate');
  $list->removeColumn('rex_version');  
  $list->removeColumn('status');
  $list->removeColumn('http_code');
  $list->removeColumn('raw');
  $list->removeColumn('domain');
  $list->removeColumn('updatedate');
  $list->removeColumn('logdate');
  
  $list->setColumnLabel('name', $this->i18n('project_manager_hosting_name'));
  $list->setColumnParams('name', ['page' => 'project_manager/server/projects', 'func' => 'updateinfos', 'domain' => '###domain###']);
  
  $list->setColumnLabel('createdate_psi', $this->i18n('project_manager_hosting_updatedate'));
  
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
    
  $list->addColumn($this->i18n('project_manager_hosting_domain'), '###domain###', 3);
  //$list->setColumnParams($this->i18n('project_manager_hosting_domain'), ['page' => 'project_manager/server/projects', 'func' => 'updateinfos', 'domain' => '###domain###']);
  $list->setColumnFormat($this->i18n('project_manager_hosting_domain'), 'custom', function ($params) {
    return '<a href="http://'.$params['list']->getValue('domain').'/" target="_blank">'.$params['list']->getValue('domain').'</a>';
  });


  $list->setColumnLabel('is_ssl', $this->i18n('is_ssl'));
  $list->setColumnLayout('is_ssl', ['<th data-sorter="string">###VALUE###</th>', '<td>###VALUE###</td>']);
  $list->setColumnFormat('is_ssl', 'custom', function ($params) {
    if ($params['list']->getValue('is_ssl') == "1") {
      return '<span class="rex-icon fa-lock text-success"></span>';
    } else if ($params['list']->getValue('is_ssl') == "0") {
      return '<span class="rex-icon fa-unlock text-danger"></span>';
    } else {
      return "?";
    }
  });
  
  $list->addColumn($this->i18n('organisation'), false, -1, ['<th>###VALUE###</th>', '<td class="rex-table-organisation">###VALUE### <i class="tablesorter-icon"></i></td>']);
  $list->setColumnLabel($this->i18n('organisation'), $this->i18n('organisation'));
  $list->setColumnFormat($this->i18n('organisation'), 'custom', function ($params) {
        
    if($params['list']->getValue('raw')) {      
      $raw= json_decode($params['list']->getValue('raw'), true);
      if (array_key_exists("org", $raw)) {
        return $raw['org'];
      }
    }
  });   
 
  
  $list->setColumnLabel('ip', $this->i18n('project_manager_hosting_ip'));
  
  $list->setColumnLabel('createdate_hosting', $this->i18n('project_manager_hosting_createdate'));
  
  $list->setColumnLabel('status_hosting', $this->i18n('status'));
  $list->setColumnFormat('status_hosting', 'custom', function ($params) {
    if ($params['list']->getValue('status_hosting') == "1") {
      return '<span class="rex-icon fa-check text-success"></span>';
    } else if ($params['list']->getValue('status_hosting') == "0") {
      return '<span class="rex-icon fa-question text-warning"></span>';
    } else if ($params['list']->getValue('status_hosting') == "-1") {
      return '<span class="rex-icon fa-exclamation-triangle text-danger"></span>';
    } else if ($params['list']->getValue('status_hosting') == "2") {
      return '<span class="rex-icon fa-arrow-right text-danger"></span>';
    }
  });
  $list->setColumnLayout('status', ['<th data-sorter="digit">###VALUE###</th>', '<td>###VALUE###</td>']);
  
  $content = $list->get();
  $content = str_replace('<table class="', '<table class="project_manager-tablesorter ', $content);
  
  $fragment = new rex_fragment();
  $fragment->setVar('title', $this->i18n('projects'));
  $fragment->setVar('content', $content, false);
  echo $fragment->parse('core/page/section.php');
}
