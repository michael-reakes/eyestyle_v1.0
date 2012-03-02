<?php
	$_ACCESS = 'newsletter';

	require_once('inc.php');

	$form = html_form::get_form('form_newsletter_subscriber');

	if ($form->clicked('submit_add')) {
		http::redirect('newsletter_subscriber_add.php');
	} elseif ($form->clicked('submit_delete')) {
		$ids = $form->get('checked_id');

		if (empty($ids)) {
			$form->set_failure('Please select at least one member.');
		}

		http::redirect('newsletter_subscriber_delete.php?'.http::build_query(array('id'=>$ids)));
	}elseif ($form->clicked('submit_active')) {
		$ids = $form->get('checked_id');

		if (empty($ids)) {
			$form->set_failure('Please select at least one member.');
		}

		http::redirect('newsletter_subscriber_activate.php?'.http::build_query(array('id'=>$ids)));
	
	}
?>