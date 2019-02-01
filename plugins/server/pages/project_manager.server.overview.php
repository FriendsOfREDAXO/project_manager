<?php


$showlist = true;
$data_id = rex_request('data_id', 'int', 0);
$func = rex_request('func', 'string');
$sort = rex_request('sort', 'string');
$sorttype = rex_request('sorttype', 'string');
$csrf_token = (rex_csrf_token::factory('cronjob'))->getValue();
$csrf = rex_csrf_token::factory('project_manager');


if ($func != '') {

    $yform = new rex_yform();
    $yform->setDebug(FALSE);
    $yform->setHiddenField('page', 'project_manager/server/overview');
    $yform->setHiddenField('func', $func);
    $yform->setHiddenField('save', '1');
    
    $yform->setObjectparams('main_table', rex::getTable('project_manager_domain'));
    $yform->setObjectparams('form_name', 'project_manager_form');
    
    $yform->setValueField('text', ['name', $this->i18n('project_manager_server_name'), 'notice' => '<small>'.$this->i18n('name_info').'</small>']);
    $yform->setValidateField('empty', ['name', $this->i18n('no_name_defined')]);
    $yform->setValidateField('unique', ['name', $this->i18n('name_already_defined')]);
    
    $yform->setValueField('text', ['tags', $this->i18n('project_manager_server_tags'), '#attributes: {"data-role":"tagsinput"}']);

    $yform->setValueField('text', ['domain', $this->i18n('project_manager_server_domain'), 'notice' => '<small>'.$this->i18n('domain_info').'</small>']);
    $yform->setValidateField('empty', ['domain', $this->i18n('no_domain_defined')]);
    $yform->setValidateField('unique', ['domain', $this->i18n('domain_already_defined')]);    

    $regex = '/^(?:(?!https?:\/\/).*)$/';
    $yform->setValidateField('preg_match', array('domain', $regex, $this->i18n('domain_no_protocol')));
    
    $yform->setValueField('select', array("is_ssl", $this->i18n('project_manager_server_ssl'),"Ja=1,Nein=0","","0","0"));
    
    $yform->setValueField('hidden', array("createdate", date ('Y-m-d H:i:s', time())));

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

    
      $yform->setValueField('text', ['api_key', $this->i18n('project_manager_server_api_key_info'), 'notice' => '<small>'.$this->i18n('api_key_notice').'</small>']);
      $yform->setValidateField('empty', ['api_key', $this->i18n('no_api_key_defined')]);
      // $yform->setValidateField('unique', ['api_key', $this->i18n('api_key_already_defined')]);
      
      $yform->setValueField('select', array("cms", $this->i18n('project_manager_server_cms'),"REDAXO 5=5,REDAXO 4=4","","0","0"));
      
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
        
      $yform->setValueField('text', ['api_key', $this->i18n('project_manager_server_api_key_info'), 'default' => bin2hex(random_bytes(24)), 'notice' => '<small>'.$this->i18n('api_key_notice').'</small>']);
      $yform->setValidateField('empty', ['api_key', $this->i18n('no_api_key_defined')]);
      //$yform->setValidateField('unique', ['api_key', $this->i18n('api_key_already_defined')]);
      
      $yform->setValueField('select', array("cms", $this->i18n('project_manager_server_cms'),"REDAXO 5=5,REDAXO 4=4","","0","0"));
        
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
            LEFT JOIN (SELECT domain_id, `raw`, createdate  FROM rex_project_manager_logs WHERE id IN (SELECT MAX(id) FROM rex_project_manager_logs GROUP BY domain_id)) AS L
            ON D.id = L.domain_id
            ORDER BY '.$sort.' '.$sorttype.'';


    $list = rex_list::factory($sql, 2000);
    $list->setColumnFormat('id', 'Id');
    $list->addParam('page', 'project_manager/server/overview');
    
    $items = rex_sql::factory()->getArray($sql);
    
    // Cronjobcall
    $sql2 = 'SELECT * FROM  '. rex::getTable('cronjob').'
          WHERE type = "rex_cronjob_project_manager_data"';
    $cronjob = rex_sql::factory()->getArray($sql2);
    $cronjobId = $cronjob[0]['id'];
    
    $refresh = '';
    if ($cronjobId != NULL) {
      $refresh = '<a href="/redaxo/index.php?page=project_manager/server/overview/#" data-cronjob="/redaxo/index.php?page=cronjob/cronjobs&func=execute&oid='.$cronjobId.'&_csrf_token='.$csrf_token.'" target="_blank" class="pull-right callCronjob"><i class="fa fa-refresh"></i> Projektdaten aktualisieren</a>';
    }
    echo rex_view::info("Anzahl der Domains und Projekte: ".count($items) . $refresh);
    
    
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
    $list->removeColumn('tags');
    $list->removeColumn('createdate');
    
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
        return '<span class="hidden">1</span> <span class="rex-icon fa-lock text-success"></span>';
      } else if ($params['list']->getValue('is_ssl') == "0") {
        return '<span class="hidden">2</span> <span class="rex-icon fa-unlock text-danger"></span>';
      } else {
        return "?";
      }
    });    
    
    $list->addColumn($this->i18n('update_content'), false, -1, ['<th>###VALUE###</th>', '<td class="rex-table-last_content_update">###VALUE### <i class="tablesorter-icon"></i></td>']);
    $list->setColumnLabel('update_content', $this->i18n('update_content'));
    $list->setColumnFormat($this->i18n('update_content'), 'custom', function ($params) {
      if($params['list']->getValue('raw')) {
        $raw= json_decode($params['list']->getValue('raw'), true);
//         dump($raw);
        if (substr($raw['cms_version'], 0, 1) == 4 ) { //if REX 4.x
          if (array_key_exists('update_article', $raw) && array_key_exists('update_media', $raw)) {
            if ($raw['update_media'] > $raw['update_article']) {
              return date('Y-m-d H:i:s', $raw['update_media']);
            } else {
              return date('Y-m-d H:i:s', $raw['update_article']);
            }
          } else {
            return "-";
          }
        } else { //if REX 5.x
          return date('Y-m-d H:i:s', strtotime($raw['article'][0]['updatedate']));
        }
      }
    });
    
    
    $list->setColumnLabel('status', $this->i18n('status'));
    $list->setColumnFormat('status', 'custom', function ($params) {
      if ($params['list']->getValue('status') == "1") {
        return '<span class="hidden">1</span><span class="rex-icon fa-check text-success"></span>';
      } else if ($params['list']->getValue('status') == "0") {
        return '<span class="hidden">2</span><span class="rex-icon fa-question text-warning"></span>';
      } else if ($params['list']->getValue('status') == "-1") {
        return '<span class="hidden">3</span><span class="rex-icon fa-exclamation-triangle text-danger"></span>';
      } else if ($params['list']->getValue('status') == "2") {
        return '<span class="hidden">3</span><span class="rex-icon fa-arrow-right text-danger"></span>';
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
        
        $cms_min = rex_config::get('project_manager/server', 'cms_min');
        $cms_4_min = rex_config::get('project_manager/server', 'cms_4_min');

        if ($params['list']->getValue('cms') == '4') {
        	if ( $raw['cms_version'] < $cms_4_min) {
        		return '<span data-color="alert-danger">'.$raw['cms_version'].'</span>';
        	} else {
        		return $raw['cms_version'];
        	}
        } else if ($params['list']->getValue('cms') == '5') {       
	        if ( $raw['cms_version'] < $cms_min) {
	          return '<span data-color="alert-danger">'.$raw['cms_version'].'</span>';
	        } else {
	          return $raw['cms_version'];
	        }
        }
        
        return $raw['cms_version'];
      }
    });
    

    $list->addColumn($this->i18n('php_version'), false, -1, ['<th>###VALUE###</th>', '<td class="rex-table-php-version">###VALUE###</td>']);
    
    $list->setColumnLabel($this->i18n('php_version'), $this->i18n('php_version'));
    $list->setColumnFormat($this->i18n('php_version'), 'custom', function ($params) {
        if($params['list']->getValue('raw')) {
          $raw= json_decode($params['list']->getValue('raw'), true);
          $php_min = rex_config::get('project_manager/server', 'php_min');
          if ( $raw['php_version'] < $php_min) {
            return '<span data-color="alert-danger">'.substr($raw['php_version'],0,3).'</span>';
          } else {
            return substr($raw['php_version'],0,3);
          }
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
            	
            	$skip_addon_config = '';
            	$skip_addon_config = rex_config::get('project_manager/server', 'skip_addon');
            	if ($skip_addon_config != "") {
            		$skip_addons = explode(',', $skip_addon_config);
            		if (in_array($addon['name'], $skip_addons)) continue;
            	}
            	
              if(rex_string::versionCompare($addon['version_current'], $addon['version_latest'], '<')) {
                
              	$skip_addon_version_config = '';
              	$skip_addon_version_config = rex_config::get('project_manager/server', 'skip_addon_version');
              	
              	if ($skip_addon_version_config != "") $skip_addon_versions = explode(',', $skip_addon_version_config);
                
                $skip = false;
                
                if (is_array($skip_addon_versions)) {
                  foreach ($skip_addon_versions as $skip_addon_version) {
  
                    if (strpos($addon['version_latest'], $skip_addon_version)) {
                      $skip = true;
                    }
                  }
                }
                
                if ($addon['version_latest'] == 0) $skip = true;
                
                if ($skip === false) {
                  $i++;
                }
                
              } else {
                $j++;
              }
            }
            
            if ($i > 0) { 
              return $i ."&nbsp;". $this->i18n('updates_necessary');
            } else {
              return "-";
            } 
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
          return '<span class="hidden">2</span><span class="rex-icon fa-exclamation-triangle text-danger"></span>';
        } else if ($params['list']->getValue('cms') == 5) {
          return '<span class="hidden">1</span><span class="rex-icon fa-check text-success"></span>';
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
    $list->setColumnLayout(rex_i18n::msg('function'), ['<th class="rex-table-action" colspan="3" data-sorter="false">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
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
