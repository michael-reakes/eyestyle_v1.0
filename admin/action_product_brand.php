<?php
$_ACCESS = 'product.brand';

require_once('inc.php');

$form = html_form::get_form('form_product_brand');

if ($form->clicked('submit_add')) {
	http::redirect('product_brand_add.php');
} elseif ($form->clicked('submit_delete')) {
	$ids = $form->get('checked_brand');

	if (empty($ids)) {
		$form->set_failure('Please select at least one brand.');
	}

	$url = 'product_brand_delete.php?';
	foreach ($ids as $id) {
		$url .= '&id[]='.$id;
	}
	http::redirect($url);
}
?>