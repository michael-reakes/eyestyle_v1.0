<?php
$_ACCESS = 'delivery';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

foreach ($ids as $id) {
	$courier = new dbo('courier', $id);
	$courier->delete();
}

html_message::add('Courier(s) deleted successfully', 'info');
http::redirect(http::get_path());
?>