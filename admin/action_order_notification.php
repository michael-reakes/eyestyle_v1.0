<?php
$_ACCESS = 'order';

require_once('inc.php');

$to = new dbo('preference', 'email_notification_to');

$form = html_form::get_form('form_order_notification');

if (!$form->validate()) {
	$form->set_failure();
}

$to->value = $form->get('value');
$to->update();

html_message::add('Notification setting updated successfully', 'info');
http::redirect(http::get_path());
?>