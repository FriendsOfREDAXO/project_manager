<?php
$sql = rex_sql::factory();
$sql->setQuery("DELETE FROM ". rex::getTablePrefix() ."cronjob WHERE type LIKE 'rex_cronjob_project_manager%'");

rex_sql_table::get(rex::getTable('project_manager_logs'))->drop();
rex_sql_table::get(rex::getTable('project_manager_domain'))->drop();


