<?php
$_ACCESS = 'all';

require_once('inc.php');

$form = html_form::get_form('form_customer_customer');

if ($form->clicked('submit_delete')) {
	$ids = $form->get('checked');

	if (empty($ids)) {
		$form->set_failure('Please select at least one account.');
		http::redirect(http::get_path());
	}

	$url = 'customer_customer_delete.php?';
	foreach ($ids as $id) {
		$url .= '&id[]='.$id;
	}
	http::redirect($url);
}
?>