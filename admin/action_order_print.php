<?php
$_ACCESS = 'all';

require_once('../inc.php');

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
$tpl = new html_template();
$message = checkout::generate_body($order,'invoice',true,false,'admin');
echo $message;

?>

