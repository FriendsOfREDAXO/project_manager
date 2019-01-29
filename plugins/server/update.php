<?php 
// Create tables
rex_sql_table::get(rex::getTable('project_manager_domain'))
->ensurePrimaryIdColumn()
->ensureColumn(new rex_sql_column('name', 'varchar(255)', true))
->ensureColumn(new rex_sql_column('domain', 'varchar(255)', true))
->ensureColumn(new rex_sql_column('tags', 'text', true))
->ensureColumn(new rex_sql_column('api_key', 'varchar(255)', true))
->ensureColumn(new rex_sql_column('is_ssl', 'tinyint', true))
->ensureColumn(new rex_sql_column('description', 'text'))
->ensureColumn(new rex_sql_column('cms', 'tinyint', true))
->ensureColumn(new rex_sql_column('status', 'text'))
->ensureColumn(new rex_sql_column('createdate', 'timestamp', false, '0000-00-00 00:00:00'))
->ensureColumn(new rex_sql_column('updatedate', 'timestamp', false, '0000-00-00 00:00:00', 'on update CURRENT_TIMESTAMP'))
->removeColumn('logdate')
->ensure();
