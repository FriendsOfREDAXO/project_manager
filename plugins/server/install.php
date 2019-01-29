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

rex_sql_table::get(rex::getTable('project_manager_logs'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('domain_id', 'int(10) unsigned', false))
    ->ensureForeignKey(new rex_sql_foreign_key('domain_id', rex::getTable('project_manager_domain'), ['domain_id' => 'id']))
    ->ensureColumn(new rex_sql_column('raw', 'text'))
    ->ensureColumn(new rex_sql_column('createdate', 'timestamp', false, '0000-00-00 00:00:00', 'default CURRENT_TIMESTAMP'))
    ->ensure();
    
    
// Create cronjob
$now = new DateTime();
$startdate = date('Y-m-d 00:00:00',strtotime( "tomorrow" ));

$cronjob = rex_sql::factory();
$cronjob->setDebug(false);
$cronjob->setQuery('SELECT id FROM '.rex::getTable('cronjob'). ' WHERE type LIKE "rex_cronjob_project_manager_data"');

if ($cronjob->getRows() == 0) {
  
  $cronjob = rex_sql::factory();
  $cronjob->setDebug(false);
  $cronjob->setTable(rex::getTable('cronjob'));
  $cronjob->setValue('name','Projekt Manager: Domaindaten');
  $cronjob->setValue('description','');
  $cronjob->setValue('type','rex_cronjob_project_manager_data');
  $cronjob->setValue('interval','{"minutes":[0],"hours":[0],"days":"all","weekdays":"all","months":"all"}');
  $cronjob->setValue('environment','|backend|');
  $cronjob->setValue('execution_start','1970-01-01 01:00:00');
  $cronjob->setValue('status','1');
  $cronjob->setValue('parameters','[]');
  $cronjob->setValue('nexttime',$startdate);
  $cronjob->setValue('createdate',$now->format('Y-m-d H:i:s'));
  $cronjob->setValue('updatedate',$now->format('Y-m-d H:i:s'));
  $cronjob->setValue('createuser',rex::getUser()->getLogin());
  $cronjob->setValue('updateuser',rex::getUser()->getLogin());
  
  try {
    $cronjob->insertOrUpdate();
    echo rex_view::success('Der Cronjob "Projekt Manager: Domaindaten" wurde angelegt. ');
  } catch (rex_sql_exception $e) {
    echo rex_view::warning('Der Cronjob "Projekt Manager: Domaindaten" wurde nicht angelegt.<br/>Wahrscheinlich existiert er schon.');
  }
}

$cronjob = rex_sql::factory();
$cronjob->setDebug(false);
$cronjob->setQuery('SELECT id FROM '.rex::getTable('cronjob'). ' WHERE type LIKE "rex_cronjob_project_manager_favicon"');

if ($cronjob->getRows() == 0) {
  
  $cronjob = rex_sql::factory();
  $cronjob->setDebug(false);
  $cronjob->setTable(rex::getTable('cronjob'));
  $cronjob->setValue('name','Projekt Manager: Favicon');
  $cronjob->setValue('description','');
  $cronjob->setValue('type','rex_cronjob_project_manager_favicon');
  $cronjob->setValue('interval','{"minutes":[0],"hours":[0],"days":"all","weekdays":"all","months":"all"}');
  $cronjob->setValue('environment','|backend|');
  $cronjob->setValue('execution_start','1970-01-01 01:00:00');
  $cronjob->setValue('status','1');
  $cronjob->setValue('parameters','[]');
  $cronjob->setValue('nexttime',$startdate);
  $cronjob->setValue('createdate',$now->format('Y-m-d H:i:s'));
  $cronjob->setValue('updatedate',$now->format('Y-m-d H:i:s'));
  $cronjob->setValue('createuser',rex::getUser()->getLogin());
  $cronjob->setValue('updateuser',rex::getUser()->getLogin());
  
  try {
    $cronjob->insertOrUpdate();
    echo rex_view::success('Der Cronjob "Projekt Manager: Favicons" wurde angelegt. ');
  } catch (rex_sql_exception $e) {
    echo rex_view::warning('Der Cronjob "Projekt Manager: Favicons" wurde nicht angelegt.<br/>Wahrscheinlich existiert er schon.');
  }
}