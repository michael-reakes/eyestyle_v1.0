<?php

require_once('inc.php');

$form = html_form::get_form('form_cart');

if (!$form->validate()) {
	$form->set_failure();
	http::redirect(http::get_path());
}

foreach(array_keys($_CHECKOUT->cart) as $pid) {
	$qty = $form->get($pid);
	$_CHECKOUT->cart_update($pid, $qty);
}

html_message::add('Your shopping cart has been updated.','info');
http::redirect(http::url('cart.php'));

?>