<?php
$_ACCESS = 'order';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

foreach ($ids as $id) {
	$order = new dbo('order', $id);
	$order->delete_children('order_item');
	$order->delete();
}

html_message::add('Order(s) deleted successfully', 'info');
http::redirect(http::get_path());
?>