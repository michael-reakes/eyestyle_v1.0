<?php
	$_ACCESS = 'newsletter.subscriber';

	require_once('inc.php');

	http::halt_if(!isset($_GET['id']));
	$subscriber = new dbo('subscriber', $_GET['id']);

	$form = html_form::get_form('form_subscriber_subscriber_edit');

	if (!$form->validate()) {
		$form->set_failure();
	}

	$subscriber->email = $form->get('email');
	$subscriber->update();

	html_message::add('Subscriber\'s details updated successfully.', 'info');

	http::redirect(http::get_path());
?>