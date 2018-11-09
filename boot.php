<?php

/** @var rex_addon project_manager */

// Addonrechte (permissions) registieren
if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('project_manager[]');
}