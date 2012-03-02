<?php
$_ACCESS = 'delivery';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));
$courier = new dbo('courier', $_GET['id']);

$form = html_form::get_form('form_delivery_delivery_courier_edit');

if (!$form->validate()) {
	$form->set_failure();
}

$courier->name = $form->get('name');
$courier->contact = $form->get('contact');
$courier->update();

html_message::add('Courier updated successfully.', 'info');
http::redirect(http::get_path());
?>