<?
$_REQUIRE_SSL = true;

require_once('inc.php');
require_once('include/inc_paypal.php');

$logFile = '/home/eye32354/public_html/logs/paypal_ipn.txt';

// log ipn entry
$fh = fopen($logFile, 'a') or die("can't open file");
fwrite($fh, "PayPay IPN received on ".date('Y-m-d H:i:s').':');
fwrite($fh, "\n".print_r($_POST, true));
fclose($fh);

if (paypal_ipn()) {
	// assign posted variables to local variables
	/*
	$item_name = $_POST['item_name'];
	$item_number = $_POST['item_number'];
	$payment_status = $_POST['payment_status'];
	$payment_amount = $_POST['mc_gross'];
	$payment_currency = $_POST['mc_currency'];
	$txn_id = $_POST['txn_id'];
	$receiver_email = $_POST['receiver_email'];
	$payer_email = $_POST['payer_email'];
	$invoice = $_POST['invoice'];
	*/

	// validate ipn
	$errors = array();
	$order = new dbo('order');
	if (!$order->load((int)$_POST['invoice'])) {
		$errors[] = 'Order does not exist';
	} else {
		if ($order->status != 'unconfirmed') $errors[] = 'Order has already been processed previously';
		if ($_POST['mc_gross'] != $order->total) $errors[] = 'Order amount does not match gross amount from IPN (Expected: '.$order->total.')';
	}
	if ($_POST['receiver_email'] != $_CONFIG['paypal']['account']) $errors[] = 'Receive email from IPN does not match business paypal email (Expected: '.$_CONFIG['paypal']['account'].')';
	if ($_POST['payment_status'] != 'Completed') $errors[] = 'Payment status is not yet completed';

	if (!count($errors)) {
		$update = array(
			'status' => 'confirmed',
			'payment_reference' => $_POST['txn_id']
		);
		if ($order = $_CHECKOUT->update_order($update, $order->order_id)){
			$fh = fopen($logFile, 'a') or die("can't open file");
			fwrite($fh, "IPN successful\n\n");
			fclose($fh);

			$_CHECKOUT->send_checkout_email($order);
			$_CHECKOUT->send_notification_email($order);
		}
	} else {
		$fh = fopen($logFile, 'a') or die("can't open file");
		fwrite($fh, count($errors).' errors encountered:');
		fwrite($fh, "\n".implode("\n", $errors)."\n\n");
		fclose($fh);
	}
} else {
	$fh = fopen($logFile, 'a') or die("can't open file");
	fwrite($fh, 'Invalid IPN');
	fclose($fh);
}
exit;