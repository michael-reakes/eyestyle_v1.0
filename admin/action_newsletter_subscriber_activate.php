<?php
	$_ACCESS = 'newsletter.subscriber';

	require_once('inc.php');

	http::halt_if(!isset($_GET['id']));

	$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

	foreach ($ids as $id) {
		$subscriber = new dbo('subscriber', $id);
		$subscriber->status = "active";
		$subscriber->update();
	}

	html_message::add('Subscriber(s) subscribed successfully', 'info');
	http::redirect(http::get_path());
?>