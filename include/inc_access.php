<?php
require_once('access/access.php');

$_STAFF = false;

if (!isset($_ACCESS)) {
	print "define access level first";
	exit;
}

if ($_ACCESS !=  'public') {
	if (!isset($_SESSION['_STAFF_ID'])) {
		http::redirect($_CONFIG['access']['login_page']);
	} else {
		$_STAFF = new dbo('staff');
		if (!$_STAFF->load($_SESSION['_STAFF_ID'])) {
			$_STAFF = false;
			unset($_SESSION['_STAFF_ID']);
			http::redirect($_CONFIG['access']['login_page']);
		}

		if ( !access::verify($_STAFF->access, $_ACCESS)) {
			html_message::add("You do not have the right to access this page");
			http::redirect($_CONFIG['http']['error_page']);
		}
	}
}


?>