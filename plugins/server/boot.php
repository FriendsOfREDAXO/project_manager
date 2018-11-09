<?php
// CRONJOB REGISTER
if (rex_addon::get('cronjob')->isAvailable()) {
	rex_cronjob_manager::registerType('rex_cronjob_project_manager_data');
  rex_cronjob_manager::registerType('rex_cronjob_project_manager_favicon');
}

// CSS und JavaScript einbinden

if (rex::isBackend() && is_object(rex::getUser())) {
    //rex_view::addJsFile($this->getAssetsUrl('js/project-manager-server.js'));
}

if (rex::isBackend() && is_object(rex::getUser())) {
  
  if (rex::getUser()->hasPerm('project_manager_server[]')) { 
    //  set as start page to server if perm is ok
    rex_extension::register('PAGES_PREPARED', function (rex_extension_point $ep) {
      $pages = $ep->getSubject();
      $pages[$this->getAddOn()->getName()]->setHref(rex_url::backendPage($this->getProperty('package')));
      $ep->setSubject($pages);
    }, rex_extension::LATE);
  }
}

if (rex::isBackend() && is_object(rex::getUser())) {
  rex_perm::register('project_manager_server[]');
}

rex_view::addCssFile($this->getAssetsUrl('css/theme.default.min.css'));
rex_view::addJsFile($this->getAssetsUrl('js/jquery.tablesorter.combined.min.js'));
rex_view::addJsFile($this->getAssetsUrl('js/tablesorter-custom.js'));

