<?php
$_ACCESS = 'system';

require_once('inc.php');

if (isset($_GET['id'])) {
	$toset = new dbo('class', $_GET['id']);
} else {
	http::halt();
}

$class_list = new dbo_list('class', 'WHERE `default` = "true"');
foreach ($class_list->get_all() as $class) {
	$class->default = 'false';
	$class->update();
}

$toset->default = 'true';
$toset->update();

html_message::add('Class set to default successfully.', 'info');
http::redirect(http::get_path());
?>