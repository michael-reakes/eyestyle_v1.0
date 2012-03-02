<?php
$_ACCESS = 'product.product';

require_once('inc.php');

$form = html_form::get_form('form_store_stock');

if (!$form->validate()) {
	$form->set_failure();
}

$pids = explode('|', $form->get('pids'));
foreach($pids as $id) {
	if (!empty($id)) {
		$lens = new dbo('lens',$id);
		$lens->quantity = $form->get('lens_'.$id);
		$lens->update();
	}
}

html_message::add('Stock has been updated successfully.', 'info');
http::redirect(http::get_path());

?>