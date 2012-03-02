<?php
$_ACCESS = 'all';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$form = html_form::get_form('form_keywords');

if (!$form->validate()) {
	$form->set_failure();
}

$keyword_dbo = new dbo('preference','meta_keywords_'.$_GET['id']);
$keyword_dbo->value = $form->get('keywords');
$keyword_dbo->insert_update();

$description_dbo = new dbo('preference','meta_description_'.$_GET['id']);
$description_dbo->value = $form->get('description');
$description_dbo->insert_update();

html_message::add('Description and keywords updated successfully.', 'info');
http::redirect(http::get_path());
?>