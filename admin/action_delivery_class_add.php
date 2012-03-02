<?php
$_ACCESS = 'delivery';

require_once('inc.php');

$form = html_form::get_form('form_system_delivery_class_add');

if (!$form->validate()) {
	$form->set_failure();
}

$class = new dbo('delivery_class');
$class->name = $form->get('name');
$class->description = $form->get('description');
$class->weight = $form->get('weight');
$class->insert();

html_message::add('Class created successfully.', 'info');
http::redirect('delivery_classes.php');
?>