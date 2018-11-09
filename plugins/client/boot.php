<?php 
if (rex::isBackend() && is_object(rex::getUser())) {
  rex_perm::register('project_manager_client[]');
}
