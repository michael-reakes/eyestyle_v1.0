<?php
$_ACCESS = 'delivery';

require_once('inc.php');

$form = html_form::get_form('form_delivery_delivery_couriers');

if ($form->clicked('submit_add')) {
	http::redirect('delivery_courier_add.php');
} elseif ($form->clicked('submit_delete')) {
	$ids = $form->get('checked');

	if (empty($ids)) {
		$form->set_failure('Please select at least one courier.');
		http::redirect(http::get_path());
	}

	$url = 'delivery_courier_delete.php?';
	foreach ($ids as $id) {
		$url .= '&id[]='.$id;
	}
	http::redirect($url);
}
?>