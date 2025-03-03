<?php 
if (rex_plugin::get('project_manager', 'server')->isInstalled()) {
  rex_sql_table::get(rex::getTable('project_manager_domain'))
  ->ensureColumn(new rex_sql_column('maintenance', 'tinyint', true))  
  ->ensureColumn(new rex_sql_column('param', 'text', true))
  ->ensureColumn(new rex_sql_column('hint', 'text', true))
  ->alter();
}
