<?php
if (rex_plugin::get('project_manager', 'server')->isInstalled()) {
  rex_sql_table::get(rex::getTable('project_manager_domain'))
  ->ensureColumn(new rex_sql_column('tags', 'text', true))
  ->ensureColumn(new rex_sql_column('api_key', 'varchar(255)', true))
  ->ensureColumn(new rex_sql_column('maintenance', 'tinyint', true))
  ->ensureColumn(new rex_sql_column('hint', 'text', true))
  ->ensureColumn(new rex_sql_column('param', 'text', true))
  ->alter();
}
