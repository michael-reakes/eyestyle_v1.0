<?php
$_ACCESS = 'all';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

foreach ($ids as $id) {
	$user = new dbo('customer', $id);
	$user->delete();
}

html_message::add('Customer(s) deleted successfully', 'info');
http::redirect(http::get_path());
?>