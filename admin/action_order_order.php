<?php
$_ACCESS = 'order';

require_once('inc.php');

$form = html_form::get_form('form_order_order');

if ($form->clicked('submit_invoice')) {
	$ids = $form->get('checked_order');

	if (empty($ids)) {
		$form->set_failure('Please select at least one order.');
		http::redirect(http::get_path());
	}

	$url = 'order_print.php?';
	foreach ($ids as $id) {
		$url .= '&id[]='.$id;
	}
	http::redirect($url);
} elseif ($form->clicked('submit_delete')) {
	$ids = $form->get('checked_order');

	if (empty($ids)) {
		$form->set_failure('Please select at least one order.');
		http::redirect(http::get_path());
	}

	$url = 'order_delete.php?';
	foreach ($ids as $id) {
		$url .= '&id[]='.$id;
	}
	http::redirect($url);
}
?>