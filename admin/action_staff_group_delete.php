<?php
$_ACCESS = 'staff.group';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

foreach ($ids as $id) {
	$group = new dbo('staff_group', $id);
	$group->delete();
}

html_message::add('User group(s) deleted successfully', 'info');
http::redirect(http::get_path());
?>