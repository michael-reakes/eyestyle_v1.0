<?php
if (!isset($_SESSION['checkout'])) {
	$_SESSION['checkout'] = new checkout();
}
$_CHECKOUT = &$_SESSION['checkout'];
/*
$excludedUrl = array('checkout_paypal_ipn.php', 'checkout_paypal.php', 'checkout_complete.php');
if (!in_array($_SERVER['PHP_SELF'], $excludedUrl)) {
	// If we have just redirected to paypal, we want to clear the checkout if they come back via Back button or cancel link from PayPal
	if ($_CHECKOUT->redirectedtopaypal && !isset($_GET['order'])) {
		$orderId = $_CHECKOUT->order_id;
		$_SESSION['checkout'] = new checkout();
		http::redirect('checkout_cancelled.php?order='.$orderId);
	}
}
*/