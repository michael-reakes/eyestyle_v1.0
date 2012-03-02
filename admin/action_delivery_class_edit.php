<?php
$_ACCESS = 'delivery';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));
$class = new dbo('delivery_class', $_GET['id']);

$form = html_form::get_form('form_system_delivery_class_edit');

if (!$form->validate()) {
	$form->set_failure();
}

$class->name = $form->get('name');
$class->description = $form->get('description');
$class->weight = $form->get('weight');
$class->update();

html_message::add('Class updated successfully.', 'info');
http::redirect(http::get_path());
?>