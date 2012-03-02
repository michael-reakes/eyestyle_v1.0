<?php
$_ACCESS = 'staff.account';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

foreach ($ids as $id) {
	$staff = new dbo('staff', $id);
	$staff->delete();
}

html_message::add('Staff account(s) deleted successfully', 'info');
http::redirect(http::get_path());
?>