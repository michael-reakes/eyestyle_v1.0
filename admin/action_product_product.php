<?php
$_ACCESS = 'product.product';

require_once('inc.php');

$form = html_form::get_form('form_product_product');

if ($form->clicked('submit_add')) {
	http::redirect('product_product_add.php');
} elseif ($form->clicked('submit_delete')) {
	$ids = $form->get('checked_product');

	if (empty($ids)) {
		$form->set_failure('Please select at least one product.');
	}

	$url = 'product_product_delete.php?';
	foreach ($ids as $id) {
		$url .= '&id[]='.$id;
	}
	http::redirect($url);
}
?>