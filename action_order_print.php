<?php
$_ACCESS = 'all';

require_once('inc.php');

if (isset($_GET['id'])) {
	$order_array = array();
	if(is_array($_GET['id'])) {
		foreach ($_GET['id'] as $id) {
			$order_array[] = $id;
		}
	} else {
		$order_array[] = $_GET['id'];
	}
} else {
	http::halt();
}

$order = new dbo('order',$_GET['id']);

if ($order->status != 'unconfirmed'){
	$message = checkout::generate_body($order,'invoice',true,true,'');
	echo $message;
}else{
	$message = checkout::generate_body($order,'confirmation',true,true,'');
	echo $message;
}	
?>

