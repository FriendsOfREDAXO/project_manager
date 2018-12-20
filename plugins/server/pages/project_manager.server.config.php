<?php
$message = '';

if (rex_post('btn_save', 'string') != '') {
  $this->setConfig(rex_post('config', [
    ['php_min', 'string'],
    ['cms_min', 'string'],
    ['php_min_color', 'string'],
    ['cms_min_color', 'string'],
    
  ]));
  
  $message = $this->i18n('project_manager_server_config_saved_successful');
}

$content = '';


$formElements = [];
$n = [];
$n['label'] = '<h3>'.$this->i18n('project_manager_server_min_headline').'</h3>';
$n['field'] = '';
$formElements[] = $n;


// PHP MIN
$n = [];
$n['label'] = '<label for="php_min">' . $this->i18n('project_manager_server_php_min') . '</label>';
$select = new rex_select();
$select->setId('php_min');
$select->setAttribute('class', 'form-control');
$select->setName('config[php_min]');
$select->addOption('5.3', '5.3');
$select->addOption('5.4', '5.4');
$select->addOption('5.5', '5.5');
$select->addOption('5.6', '5.6');
$select->addOption('7.0', '7.0');
$select->addOption('7.1', '7.1');
$select->addOption('7.2', '7.2');
$select->addOption('7.3', '7.3');
$select->setSelected($this->getConfig('php_min'));
$n['field'] = $select->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

// CMS MIN
$formElements = [];
$n = [];
$n['label'] = '<label for="cms_min">' . $this->i18n('project_manager_server_cms_min') . '</label>';
$select = new rex_select();
$select->setId('cms_min');
$select->setAttribute('class', 'form-control');
$select->setName('config[cms_min]');
$select->addOption('4.7.0', '4.7.0');
$select->addOption('4.7.1', '4.7.1');
$select->addOption('4.7.2', '4.7.2');
$select->addOption('5.3.0', '5.3.0');
$select->addOption('5.4.0', '5.4.0');
$select->addOption('5.5.1', '5.5.1');
$select->addOption('5.5.2', '5.5.2');
$select->addOption('5.6.0', '5.6.0');
$select->addOption('5.6.1', '5.6.1');
$select->addOption('5.6.2', '5.6.2');
$select->addOption('5.6.3', '5.6.3');
$select->addOption('5.6.4', '5.6.4');
$select->setSelected($this->getConfig('cms_min'));
$n['field'] = $select->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="' . $this->i18n('save') . '">' . $this->i18n('save') . '</button>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('flush', true);
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('project_manager_pagespeed_api_key_title'), false);
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');
echo '
<form action="' . rex_url::currentBackendPage() . '" method="post">
    ' . $content . '
</form>';