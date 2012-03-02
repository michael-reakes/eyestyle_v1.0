<?
$_REQUIRE_SSL = true;

require_once('inc.php');


if (!isset($_GET['order'])) {
	http::redirect('./');
}


$order = new dbo('order');
if (!$order->load($_GET['order'])) {
	http::redirect('./');
}
// Check if order has already been processed
if ($order->status != 'unconfirmed') {
	http::redirect('./');
}
else {
	$orderItem_list = new dbo_list('order_item','WHERE `order_id` = '.$order->order_id);
	foreach($orderItem_list->get_all() as $orderItem) {
		$orderItem->delete();
	}
	$order->delete();
	html_message::add("You have cancelled the order.");
	http::redirect('./');
}