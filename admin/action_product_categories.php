<?php
$_ACCESS = 'product.category';

require_once('inc.php');

$form = html_form::get_form('form_product_categories');

if ($form->clicked('submit_add')) {
	http::redirect('product_category_add.php');
} elseif ($form->clicked('submit_delete')) {
	$ids = $form->get('checked_id');

	if (empty($ids)) {
		$form->set_failure('Please select at least one category.');
		http::redirect(http::get_path());
	}
	$url = 'product_category_delete.php?';
	foreach ($ids as $id) {
		$url .= '&id[]='.$id;
	}
	http::redirect($url);}
?>