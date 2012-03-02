<?php
$_ACCESS = 'delivery';

require_once('inc.php');

$form = html_form::get_form('form_system_delivery_classes');

if ($form->clicked('submit_add')) {
	http::redirect('delivery_class_add.php');
} elseif ($form->clicked('submit_delete')) {
	$ids = $form->get('checked');

	if (empty($ids)) {
		$form->set_failure('Please select at least one class.');
		http::redirect(http::get_path());
	}

	$url = 'delivery_class_delete.php?';
	foreach ($ids as $id) {
		$url .= '&id[]='.$id;
	}
	http::redirect($url);
}
?>