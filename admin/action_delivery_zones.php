<?php
$_ACCESS = 'delivery';

require_once('inc.php');

$form = html_form::get_form('form_delivery_delivery_zones');

if ($form->clicked('submit_add')) {
	http::redirect('delivery_zone_add_edit.php');
} elseif ($form->clicked('submit_delete')) {
	$ids = $form->get('checked');

	if (empty($ids)) {
		$form->set_failure('Please select at least one zone.');
		http::redirect(http::get_path());
	}

	$url = 'delivery_zone_delete.php?';
	foreach ($ids as $id) {
		$url .= '&id[]='.$id;
	}
	http::redirect($url);
}
?>