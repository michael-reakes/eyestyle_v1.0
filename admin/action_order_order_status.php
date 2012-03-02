<?php
$_ACCESS = 'order';

require_once('inc.php');

$form = html_form::get_form('form_order_order_status');
if (!$form->validate()) {
	$form->set_failure();
}

$id = $form->get('id');
$status = $form->get('status');
if (isset($_GET['status'])){
	$status = $_GET['status'];
	
}

$order = new dbo('order', $id);

if ($status == 'unconfirmed') {
	$order->date_processed = '0000-00-00';
	$order->date_delivered = '0000-00-00';
	$order->payment_reference = '';
}

if($status == 'confirmed') {
	$order->date_processed = utils_time::db_datetime();
	$order->date_delivered = '0000-00-00';
	$order->payment_reference = empty($order->payment_reference) ? $form->get('comment') : '';
	
	if ($order->status == 'unconfirmed') {
		$checkout = new checkout();
		$checkout->send_invoice($order);
	}
}

$order->courier_id = 0;
$order->courier_no = '';

if ($status == 'processing') {
	$order->date_delivered = '0000-00-00';
	$order->courier_name = '';
	$order->tracking_no = '';
}

if($status == 'delivered') {
	$order->date_delivered = utils_time::db_datetime();
	$order->courier_name = $form->get('courier_id');
	$order->tracking_no = $form->get('comment');
}

$order->status = $status;
$order->update();

if ($status == 'processing' || $status == 'delivered'){
	$checkout = new checkout();
	$checkout->send_email('status_change','admin',$order);
}

html_message::add('Order status changed successfully', 'info');
http::redirect('order_order.php?status='.$order->status);
?>