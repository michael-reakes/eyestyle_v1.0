<?php
	$_ACCESS = 'newsletter.subscriber';

	require_once('inc.php');
	require_once('../include/utils/utils_validation.php');

	$form = html_form::get_form('form_newsletter_subscriber_add');

	if (!$form->validate()) {
		$form->set_failure();
	}

	function check_existing_email($email) {
		$user_list = new dbo_list('subscriber','WHERE `email` = "'.$email.'"');
		if ($user_list->count() > 0) return true;
		else return false;
	}
	$email = $form->get('email');
	if (check_existing_email($email)) $form->set_failure('Email address already existed in the database');
	if (!utils_validation::email($email)) $form->set_failure('Please enter a valid email address');

	$subscriber = new dbo('subscriber');
	$subscriber->email = $form->get('email');
	$subscriber->date_created = utils_time::db_datetime();
	$subscriber->status = "active";
	$subscriber->insert();

	html_message::add('Subscriber added successfully.', 'info');
	http::redirect('newsletter_subscriber.php');
?>