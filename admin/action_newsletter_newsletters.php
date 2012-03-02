<?php
$_ACCESS = 'newsletter.newsletter';

require_once('inc.php');

$form = html_form::get_form('form_newsletter_newsletters');

if ($form->clicked('submit_add')) {
	http::redirect('newsletter_newsletter_add.php');
}elseif ($form->clicked('submit_duplicate')) {
	$ids = $form->get('checked_id');

	if (empty($ids)) {
		$form->set_failure('Please select at least one newsletter.');
	}

	foreach ($ids as $id) {
		$newsletter = new dbo('newsletter', $id);

		$newsletter->newsletter_id = '';
		$newsletter->name = 'Copy of '.$newsletter->name;
		$newsletter->date_created = utils_time::db_datetime();
		$newsletter->date_last_sent = '';
		$newsletter->insert();
	}

	http::redirect(http::get_path());
} elseif ($form->clicked('submit_delete')) {
	$ids = $form->get('checked_id');

	if (empty($ids)) {
		$form->set_failure('Please select at least one newsletter.');
	}

	http::redirect('newsletter_newsletter_delete.php?'.http::build_query(array('id'=>$ids)));
}
?>