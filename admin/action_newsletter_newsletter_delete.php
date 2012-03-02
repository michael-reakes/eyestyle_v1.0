<?php
$_ACCESS = 'newsletter.newsletter';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

foreach ($ids as $id) {
	$newsletter = new dbo('newsletter', $id);
	$newsletter->delete();
}

html_message::add('Newsletter(s) deleted successfully', 'info');
http::redirect(http::get_path());
?>