<?php
$_ACCESS = 'all';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$form = html_form::get_form('form_content_content');

if (!$form->validate()) {
	$form->set_failure();
}

$content = new dbo('page',$_GET['id']);
$content->content = $form->get('html');
$content->update();

html_message::add('Content updated successfully.', 'info');
http::redirect(http::get_path());
?>