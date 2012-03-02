<?php
$_FCKPATH = str_replace("\\", '/', __FILE__);
$_FCKPATH = substr($_FCKPATH, 0, strrpos($_FCKPATH, '/') + 1);

require_once($_FCKPATH.'../config_global.php');
require_once($_FCKPATH.'config_local.php');

// connect to database
require_once($_FCKPATH.'../include/inc_db.php');
require_once($_FCKPATH.'../include/inc_session.php');

// apply access restriction
require_once($_FCKPATH.'../include/access/access.php');

$_STAFF = false;

if (!isset($_ACCESS)) {
	print "define access level first";
	exit;
}

$_FCKUPLOAD = false;

if ($_ACCESS !=  'public') {
	if (isset($_SESSION['_STAFF_ID'])) {
		$_STAFF = new dbo('staff');
		if (!$_STAFF->load($_SESSION['_STAFF_ID'])) {
			$_STAFF = false;
			unset($_SESSION['_STAFF_ID']);
		}

		if ( access::verify($_STAFF->access, $_ACCESS)) {
			$_FCKUPLOAD = true;
		}
	}
}

?>