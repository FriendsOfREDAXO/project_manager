<?php


$showlist = true;
$data_id = rex_request('data_id', 'int', 0);
$func = rex_request('func', 'string');
$sort = rex_request('sort', 'string');
$sorttype = rex_request('sorttype', 'string');
$csrf = rex_csrf_token::factory('project_manager_domain');

if ($func != '') {

    $yform = new rex_yform();
    // $yform->setDebug(TRUE);
    $yform->setHiddenField('page', 'project_manager/server/overview');
    $yform->setHiddenField('func', $func);
    $yform->setHiddenField('save', '1');


    $yform->setObjectparams('main_table', rex::getTable('project_manager_domain'));
    $yform->setObjectparams('form_name', 'project_manager_form');

    $yform->setValueField('text', ['name', $this->i18n('project_manager_server_name'), 'notice' => '<small>'.$this->i18n('name_info').'</small>']);
    $yform->setValidateField('empty', ['name', $this->i18n('no_name_defined')]);
    $yform->setValidateField('unique', ['name', $this->i18n('name_already_defined')]);

    $yform->setValueField('text', ['domain', $this->i18n('project_manager_server_domain'), 'notice' => '<small>'.$this->i18n('domain_info').'</small>']);
    $yform->setValidateField('empty', ['domain', $this->i18n('no_domain_defined')]);
    $yform->setValidateField('unique', ['domain', $this->i18n('domain_already_defined')]);    

    $regex = '/^(?:(?!https?:\/\/).*)$/';
    $yform->setValidateField('preg_match', array('domain', $regex, $this->i18n('domain_no_protocol')));
    
    $yform->setValueField('select', array("is_ssl", $this->i18n('project_manager_server_ssl'),"Ja=1,Nein=0","","0","0"));
    
    $yform->setValueField('text', ['api_key', $this->i18n('project_manager_server_api_key_info'), 'notice' => '<small>'.$this->i18n('api_key_notice').'</small>']);
    $yform->setValidateField('empty', ['api_key', $this->i18n('no_api_key_defined')]);
    // $yform->setValidateField('unique', ['api_key', $this->i18n('api_key_already_defined')]);
    
    $yform->setValueField('select', array("cms", $this->i18n('project_manager_server_cms'),"REDAXO 5=5,REDAXO 4=4","","0","0"));
    

    if ($func == 'delete') {

        if (!$csrf->isValid()) {
            echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
        } else {
            $d = rex_sql::factory();
            $d->setQuery('delete from '.rex::getTable('project_manager_logs').' where domain_id=' . $data_id);
            $e = rex_sql::factory();
            $e->setQuery('delete from '.rex::getTable('project_manager_domain').' where id=' . $data_id);
            echo rex_view::success($this->i18n('project_manager_server_project_deleted'));
        }

    } else if ($func == 'edit') {

    
        $yform->setHiddenField('data_id', $data_id);
        $yform->setActionField('db', [rex::getTable('project_manager_domain'), 'id=' . $data_id]);
        $yform->setObjectparams('main_id', $data_id);
        $yform->setObjectparams('main_where', "id=$data_id");
        $yform->setObjectparams('getdata', true);
        $yform->setObjectparams('submit_btn_label', $this->i18n('save'));
        $form = $yform->getForm();

        if ($yform->objparams['actions_executed']) {

            echo rex_view::success($this->i18n('project_manager_server_project_added'));

        } else {

            $showlist = false;

            $fragment = new rex_fragment();
            $fragment->setVar('class', 'edit', false);
            $fragment->setVar('title', $this->i18n('edit_domain'));

            $fragment->setVar('body', $form, false);
            echo $fragment->parse('core/page/section.php');

        }
   

    } else if ($func == 'domain_add') {

        $yform->setActionField('db', [rex::getTable('project_manager_domain')]);
        $yform->setObjectparams('submit_btn_label', $this->i18n('save'));
        $form = $yform->getForm();

         if ($yform->objparams['actions_executed']) {
            echo rex_view::success($this->i18n('project_manager_server_project_added'));
         } else {

            $showlist = false;

            $fragment = new rex_fragment();
            $fragment->setVar('class', 'edit', false);
            $fragment->setVar('title', $this->i18n('add_domain'));
            $fragment->setVar('body', $form, false);
            echo $fragment->parse('core/page/section.php');
         }

        // Insert in DB 
        // API Call inkl. reponse into DB

    }
}

###############
###
### LISTVIEW
###
###############
if ($showlist) {

//     $sql = 'SELECT * FROM ' . rex::getTable('project_manager_domain') . ' as D
//             LEFT JOIN (SELECT domain_id, status, createdate, `raw` as log_raw FROM ' . rex::getTable('project_manager_logs') . ' WHERE id IN (SELECT MAX(createdate) FROM ' . rex::getTable('project_manager_logs') . ' GROUP BY domain_id)) as L
//             ON D.id = L.domain_id
//             WHERE L.status = 1
//             ORDER BY L.createdate DESC
//             LIMIT 1

// ';
// &sort=cms&sorttype=desc
    
    if ($sort == "") $sort = "D.name"; 
    if ($sorttype == "") $sorttype = "ASC";
    
    $sql = 'SELECT * FROM (
                          SELECT id, name, domain, is_ssl, status, cms FROM `rex_project_manager_domain` ORDER BY domain ASC
                          ) AS D
            LEFT JOIN (SELECT domain_id, `raw`, createdate FROM rex_project_manager_logs WHERE id IN (SELECT MAX(id) FROM rex_project_manager_logs GROUP BY domain_id)) AS L
            ON D.id = L.domain_id
            ORDER BY '.$sort.' '.$sorttype.'';


    $list = rex_list::factory($sql, 100);
    $list->setColumnFormat('id', 'Id');
    $list->addParam('page', 'project_manager/server/overview');
    
    $items = rex_sql::factory()->getArray($sql);
    echo rex_view::info("Anzahl der Domains und Projekte: ".count($items));

//     $tdIcon = '<i class="fa fa-sitemap"></i>';
//     $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '"' . rex::getAccesskey($this->i18n('add_project'), 'add') . '><i class="rex-icon rex-icon-add"></i></a>';
//     $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
//     $list->setColumnParams($thIcon, ['func' => 'edit', 'data_id' => '###id###']);    
    
    $thIcon = '<a href="'.$list->getUrl(['func' => 'domain_add']).'"><i class="rex-icon rex-icon-add-category"></i></a>';
    $tdIcon = '<i class="rex-icon rex-icon-structure-root-level"></i>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'domain_edit', 'id' => '###id###']);
    $list->setColumnFormat($thIcon, 'custom', function ($params) {    	
    	$filename = '';
    	if (file_exists($this->getAssetsPath('favicon/'.$params['list']->getValue('domain').'.png'))) {
    		
    		$filename = $this->getAssetsUrl('favicon/'.$params['list']->getValue('domain').'.png');
    		return '<a href="http://'.$params['list']->getValue('domain').'/" target="_blank"><img src="'.$filename.'" /></a>';
    	} else {
    		return '<a href="http://'.$params['list']->getValue('domain').'/" target="_blank"><i class="fa fa-sitemap"></i></a>';
    	}  	
    });

    $list->setColumnParams('id', ['data_id' => '###id###', 'func' => 'edit']);
    $list->setColumnSortable('id');

    $list->removeColumn('id');
    $list->removeColumn('description');
    $list->removeColumn('api_key');
    $list->removeColumn('domain_id');
    $list->removeColumn('raw');
    $list->removeColumn('domain');
    $list->removeColumn('cms');
    
    $list->setColumnLabel('name', $this->i18n('name'));
    $list->setColumnParams('name', ['page' => 'project_manager/server/projects', 'func' => 'updateinfos', 'domain' => '###domain###']);
    
    $list->setColumnLabel('domain', $this->i18n('domain'));
    $list->addColumn($this->i18n('domain'), '###domain###', 3);
    //$list->setColumnParams($this->i18n('domain'), ['page' => 'project_manager/server/projects', 'domain' => '###domain###']);
    $list->setColumnFormat($this->i18n('domain'), 'custom', function ($params) {
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
    
    
    $list->setColumnLabel('createdate', $this->i18n('createdate'));
    
    $list->setColumnLabel('status', $this->i18n('status'));
    $list->setColumnFormat('status', 'custom', function ($params) {
      if ($params['list']->getValue('status') == "1") {
        return '<span class="rex-icon fa-check text-success"></span>';
      } else if ($params['list']->getValue('status') == "0") {
        return '<span class="rex-icon fa-question text-warning"></span>';
      } else if ($params['list']->getValue('status') == "-1") {
        return '<span class="rex-icon fa-exclamation-triangle text-danger"></span>';
      } else if ($params['list']->getValue('status') == "2") {
        return '<span class="rex-icon fa-arrow-right text-danger"></span>';
      } else {
        
        $api_key = '';
        if (property_exists($params['list'], 'api_key')) $api_key = $params['list']->getValue('api_key');
        
      	if ($params['list']->getValue('is_ssl') == 1) {
      	  
      	  return '<a href="https://www.'.$params['list']->getValue('domain').'/?rex-api-call=project_manager&api_key='.$api_key.'"><span class="rex-icon fa-question"></span></a>';
      	}
      	
        if ($params['list']->getValue('is_ssl') == 0) {
          
          return '<a href="http://www.'.$params['list']->getValue('domain').'/?rex-api-call=project_manager&api_key='.$api_key.'"><span class="rex-icon fa-question"></span></a>';
        }
      }
    });
    $list->setColumnLayout('status', ['<th data-sorter="digit">###VALUE###</th>', '<td>###VALUE###</td>']);


    
    $list->addColumn($this->i18n('pm_client_version'), false, -1, ['<th>###VALUE###</th>', '<td class="rex-table-cms-version">###VALUE### <i class="tablesorter-icon"></i></td>']);
    $list->setColumnLabel($this->i18n('pm_client_version'), $this->i18n('pm_client_version'));
    $list->setColumnFormat($this->i18n('pm_client_version'), 'custom', function ($params) {
      if($params['list']->getValue('raw')) {
        $raw= json_decode($params['list']->getValue('raw'), true);
        return $raw['client_version'];
      }
    });
    
    $list->addColumn($this->i18n('cms_version'), false, -1, ['<th>###VALUE###</th>', '<td class="rex-table-cms-version">###VALUE###</td>']);
    $list->setColumnLabel($this->i18n('cms_version'), $this->i18n('cms_version'));
    $list->setColumnFormat($this->i18n('cms_version'), 'custom', function ($params) {
      if($params['list']->getValue('raw')) {
        $raw= json_decode($params['list']->getValue('raw'), true);
        return $raw['cms_version'];
      }
    });
      
    $list->addColumn($this->i18n('php_version'), false, -1, ['<th>###VALUE###</th>', '<td class="rex-table-php-version">###VALUE###</td>']);
    $list->setColumnLabel($this->i18n('php_version'), $this->i18n('php_version'));
    $list->setColumnFormat($this->i18n('php_version'), 'custom', function ($params) {
      if($params['list']->getValue('raw')) {
        $raw= json_decode($params['list']->getValue('raw'), true);
        return $raw['php_version'];
      }
    });
    

    $list->addColumn($this->i18n('updates'), false, -1, ['<th>###VALUE###</th>', '<td class="rex-table-updates">###VALUE###</td>']);
    $list->setColumnLabel($this->i18n('updates'), ($this->i18n('updates')));
    $list->setColumnFormat($this->i18n('updates'), 'custom', function ($params) {      
      $addon = '';
      $addon = $params['field'];
      if($params['list']->getValue('raw')) {
        $log = json_decode($params['list']->getValue('raw'), true);
        if (array_key_exists("rex_addons", $log)) {   
          if(json_last_error() === JSON_ERROR_NONE && $log["rex_addons"] && count($log["rex_addons"])) {
            $i = 0;
            $j = 0;
            foreach($log["rex_addons"] as $addon) {
              if(rex_string::versionCompare($addon['version_current'], $addon['version_latest'], '<')) {
                $i++;
              } else {
                $j++;
              }
            }
            return $i ."&nbsp;". $this->i18n('updates_necessary');
          } else {
            return "";
          }  
        } else if ($params['list']->getValue('cms') == 4) {
          return "-";
        }
      }
    });
    
    
    $list->addColumn($this->i18n('syslog'), false, -1, ['<th>###VALUE###</th>', '<td class="rex-table-cms-version">###VALUE###</td>']);
    $list->setColumnLabel($this->i18n('syslog'), $this->i18n('syslog'));
    $list->setColumnFormat($this->i18n('syslog'), 'custom', function ($params) {
      if($params['list']->getValue('raw')) {
        $raw= json_decode($params['list']->getValue('raw'), true);        
        if (array_key_exists("syslog", $raw)) {    
          return '<span class="rex-icon fa-exclamation-triangle text-danger"></span>';
        } else if ($params['list']->getValue('cms') == 5) {
          return '<span class="rex-icon fa-check text-success"></span>';
        }else {
          return '-';
        }
      }
    });
   
    $list->setColumnParams($this->i18n('domain'), ['page' => 'project_manager/server/projects', 'domain' => '###domain###']);
    
    $list->addColumn(rex_i18n::msg('view'), '<i class="rex-icon rex-icon-view"></i>');
    $list->setColumnLayout(rex_i18n::msg('view'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('view'), ['page' => 'project_manager/server/projects', 'domain' => '###domain###'] + $csrf->getUrlParams());
    
    $list->addColumn(rex_i18n::msg('function'), '<i class="rex-icon rex-icon-edit"></i>');
    $list->setColumnLayout(rex_i18n::msg('function'), ['<th class="rex-table-action" colspan="3">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('function'), ['data_id' => '###id###', 'func' => 'edit']);
    
    
    $list->addColumn(rex_i18n::msg('delete'), '<i class="rex-icon rex-icon-delete"></i>');
    $list->setColumnLayout(rex_i18n::msg('delete'), ['', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('delete'), ['data_id' => '###id###', 'func' => 'delete'] + $csrf->getUrlParams());
    $list->addLinkAttribute(rex_i18n::msg('delete'), 'onclick', 'return confirm(\' id=###id### ' . rex_i18n::msg('delete') . ' ?\')');

    $showArticle = function ($params) {
        $id = $params['list']->getValue($params['field']);
        if ($id == 0) {
            return $this->i18n('root');
        } else {
            if (($article = rex_article::get($id))) {
                if ($article->isStartArticle()) {
                    $link = 'index.php?page=structure&category_id='.$id.'&clang=1';
                } else {
                    $link = 'index.php?page=content&article_id='.$id.'&mode=edit&clang=1';
                }
                return $article->getName().' [<a href="'.$link.'">'.$id.'</a>]';
            }
        }
        return '['.$id.']';
    };

    $content = $list->get();
    $content = str_replace('<table class="', '<table class="project_manager-tablesorter ', $content);

    $fragment = new rex_fragment();
    $fragment->setVar('title', $this->i18n('projects'));
    $fragment->setVar('content', $content, false);
    echo $fragment->parse('core/page/section.php');
}
