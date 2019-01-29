<?php
$message = '';

if (rex_post('btn_save', 'string') != '') {
  $this->setConfig(rex_post('settings', [
    ['project_manager_api_key', 'string']
  ]));
  
  $message = $this->i18n('project_manager_api_key_saved_successful');
}

$content = '';


$formElements = [];
$n = [];
$n['label'] = '<label for="phpmailer-project_manager_api_key">' . $this->i18n('project_manager_api_key_label') . '</label>';
$n['field'] = '<input class="form-control" id="" name="settings[project_manager_api_key]" value="'.$this->getConfig('project_manager_api_key') . '" />';
$n['note'] = $this->i18n('project_manager_api_key_notice').' <code>13f15d69755585c3a825c3eccf2d654fc6578dadb7e05475</code>';

$formElements[] = $n;



$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$fragment->setVar('class', "panel panel-warning", false);
$content .= $fragment->parse('core/form/form.php');

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
$fragment->setVar('title', $this->i18n('project_manager_client_title'), false);
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');
echo '
<form action="' . rex_url::currentBackendPage() . '" method="post">
    ' . $content . '
</form>';