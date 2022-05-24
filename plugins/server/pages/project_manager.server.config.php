<?php
$message = '';

if (rex_post('btn_save', 'string') != '') {
  $this->setConfig(rex_post('config', [
    ['php_min', 'string'],
    ['cms_4_min', 'string'],
    ['cms_min', 'string'],
    ['php_min_color', 'string'],
    ['cms_min_color', 'string'],
    ['skip_addon', 'string'],
    ['skip_addon_version', 'string'],
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
$select->addOption('7.4', '7.4');
$select->addOption('8.0', '8.0');
$select->addOption('8.1', '8.1');
$select->addOption('8.2', '8.2');
$select->setSelected($this->getConfig('php_min'));
$n['field'] = $select->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

// CMS 4 MIN
$formElements = [];
$n = [];
$n['label'] = '<label for="cms_4_min">' . $this->i18n('project_manager_server_cms_4_min') . '</label>';
$select = new rex_select();
$select->setId('cms_4_min');
$select->setAttribute('class', 'form-control');
$select->setName('config[cms_4_min]');
$select->addOption('4.7.0', '4.7.0');
$select->addOption('4.7.1', '4.7.1');
$select->addOption('4.7.2', '4.7.2');
$select->addOption('4.7.3', '4.7.3');
$select->setSelected($this->getConfig('cms_4_min'));
$n['field'] = $select->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

// CMS 5 MIN
$formElements = [];
$n = [];
$n['label'] = '<label for="cms_min">' . $this->i18n('project_manager_server_cms_min') . '</label>';
$select = new rex_select();
$select->setId('cms_min');
$select->setAttribute('class', 'form-control');
$select->setName('config[cms_min]');
$select->addOption('5.3.0', '5.3.0');
$select->addOption('5.4.0', '5.4.0');
$select->addOption('5.5.1', '5.5.1');
$select->addOption('5.5.2', '5.5.2');
$select->addOption('5.6.0', '5.6.0');
$select->addOption('5.6.1', '5.6.1');
$select->addOption('5.6.2', '5.6.2');
$select->addOption('5.6.3', '5.6.3');
$select->addOption('5.6.4', '5.6.4');
$select->addOption('5.6.5', '5.6.5');
$select->addOption('5.7.0', '5.7.0');
$select->addOption('5.7.1', '5.7.1');
$select->addOption('5.8.0', '5.8.0');
$select->addOption('5.8.1', '5.8.1');
$select->addOption('5.9.0', '5.9.0');
$select->addOption('5.10.0', '5.10.0');
$select->addOption('5.11.0', '5.11.0');
$select->addOption('5.11.1', '5.11.1');
$select->addOption('5.11.2', '5.11.2');
$select->addOption('5.12.0', '5.12.0');
$select->addOption('5.12.1', '5.12.1');
$select->addOption('5.13.0', '5.13.0');
$select->addOption('5.13.1', '5.13.1');
$select->addOption('5.13.2', '5.13.2');
$select->addOption('5.13.3', '5.13.3');
$select->addOption('5.13.4', '5.13.4');
$select->addOption('5.14.0', '5.14.0');

$select->setSelected($this->getConfig('cms_min'));
$n['field'] = $select->get();
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

$formElements = [];
$n = [];
$n['label'] = '<h3>'.$this->i18n('project_manager_server_configuration').'</h3>';
$n['field'] = '';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

$formElements = [];
$n = [];
$n['label'] = '<label for="skip-addon">' . $this->i18n('project_manager_server_skip_addon') . '</label>';
$n['field'] = '<input class="form-control" type="text" id="skip_addon" name="config[skip_addon]" value="' . $this->getConfig('skip_addon') . '" data-role="tagsinput"/>
		 					 <p class="help-block">' . $this->i18n('project_manager_server_skip_addon_notice') . '</p>';
$formElements[] = $n;

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/container.php');

$formElements = [];
$n = [];
$n['label'] = '<label for="skip-addon-version">' . $this->i18n('project_manager_server_skip_addon_version') . '</label>';
$n['field'] = '<input class="form-control" type="text" id="skip_addon_version" name="config[skip_addon_version]" value="' . $this->getConfig('skip_addon_version') . '" data-role="tagsinput"/>
							 <p class="help-block">' . $this->i18n('project_manager_server_skip_addon_version_notice') . '</p>';
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
$fragment->setVar('title', $this->i18n('project_manager_server_title'), false);
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');
echo '
<form action="' . rex_url::currentBackendPage() . '" method="post">
    ' . $content . '
</form>';