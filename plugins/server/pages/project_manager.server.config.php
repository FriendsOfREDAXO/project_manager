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
    ['show_hint', 'string'],
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
$select->addOption('8.3', '8.3');
$select->addOption('8.4', '8.4');
$select->addOption('8.5', '8.5');
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
$select->addOption('5.12.0', '5.12.0');
$select->addOption('5.12.1', '5.12.1');
$select->addOption('5.13.0', '5.13.0');
$select->addOption('5.13.1', '5.13.1');
$select->addOption('5.13.2', '5.13.2');
$select->addOption('5.13.3', '5.13.3');
$select->addOption('5.13.4', '5.13.4');
$select->addOption('5.14.0', '5.14.0');
$select->addOption('5.14.1', '5.14.1');
$select->addOption('5.14.2', '5.14.2');
$select->addOption('5.15.0', '5.15.0');
$select->addOption('5.15.1', '5.15.1');
$select->addOption('5.16.0', '5.16.0');
$select->addOption('5.16.1', '5.16.1');
$select->addOption('5.17.0', '5.17.0');
$select->addOption('5.17.1', '5.17.1');
$select->addOption('5.18.0', '5.18.0');
$select->addOption('5.18.1', '5.18.1');
$select->addOption('5.18.2', '5.18.2');
$select->addOption('5.18.3', '5.18.3');

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


// Bemerkung in Übersicht anzeigen
$formElements = [];
$n = [];
$n['label'] = '<label for="show_hint">' . $this->i18n('project_manager_server_show_hint') . '</label>';
$select = new rex_select();
$select->setId('show_hint');
$select->setAttribute('class', 'form-control');
$select->setName('config[show_hint]');
$select->addOption('Nein', '0');
$select->addOption('Ja', '1');
$select->setSelected($this->getConfig('show_hint'));
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
$fragment->setVar('title', $this->i18n('project_manager_server_title'), false);
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');
echo '
<form action="' . rex_url::currentBackendPage() . '" method="post">
    ' . $content . '
</form>';
