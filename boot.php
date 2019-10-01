<?php

/** @var rex_addon project_manager */

// Addonrechte (permissions) registieren
if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('project_manager[]');
}

if (rex::isBackend() && rex_be_controller::getCurrentPagePart(1) == 'project_manager') {
    rex_view::addCssFile($this->getAssetsUrl('css/styles.css'));
}
