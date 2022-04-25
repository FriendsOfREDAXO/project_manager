<?php

// Create tables
rex_sql_table::get(rex::getTable('project_manager_domain_hosting'))
    //->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('domain', 'varchar(255)', false, null, ''))->setPrimaryKey('domain')
    ->ensureColumn(new rex_sql_column('raw', 'longtext', true))
    ->ensureColumn(new rex_sql_column('ip', 'text'))
    ->ensureColumn(new rex_sql_column('createdate', 'timestamp', false, '0000-00-00 00:00:00', 'on update CURRENT_TIMESTAMP'))
    ->ensureColumn(new rex_sql_column('status', 'text'))
    ->ensure();

    
// Create cronjob
$now = new DateTime();
$startdate = date('Y-m-d 00:00:00',strtotime( "tomorrow" ));
    
$cronjob = rex_sql::factory();
$cronjob->setDebug(true);
$cronjob->setQuery('SELECT id FROM '.rex::getTable('cronjob'). ' WHERE type LIKE "rex_cronjob_project_manager_hosting"');

if ($cronjob->getRows() == 0) {
  
  $cronjob = rex_sql::factory();
  $cronjob->setDebug(true);
  $cronjob->setTable(rex::getTable('cronjob'));
  $cronjob->setValue('name','Projekt Manager: Hosting Daten');
  $cronjob->setValue('description','');
  $cronjob->setValue('type','rex_cronjob_project_manager_hosting');
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
    echo rex_view::success('Der Cronjob "Projekt Manager: Hosting Daten" wurde angelegt. ');
  } catch (rex_sql_exception $e) {
    echo rex_view::warning('Der Cronjob "Projekt Manager: Hosting Daten" wurde nicht angelegt.<br/>Wahrscheinlich existiert er schon.');
  }
}