<?php
$_ACCESS = 'delivery';

require_once('inc.php');

$form = html_form::get_form('form_delivery_delivery_courier_add');

if (!$form->validate()) {
	$form->set_failure();
}

$courier = new dbo('courier');
$courier->name = $form->get('name');
$courier->contact = $form->get('contact');
$courier->insert();

html_message::add('Courier created successfully.', 'info');
http::redirect(http::get_path());
?>