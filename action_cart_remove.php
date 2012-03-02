<?php

require_once('inc.php');


if (isset($_GET['pid'])) {
	$_CHECKOUT->cart_remove($_GET['pid']);
}

html_message::add('Item has been removed from your shopping cart.','info');
http::redirect('cart.php');

?>