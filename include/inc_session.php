<?php
/*****************************************************************************
* Filename: inc_session.php
* Copyright: 2005 S3 Group Pty Ltd (www.s3design.com.au)
*****************************************************************************/

if (isset($_CONFIG['session']['use_db']) && $_CONFIG['session']['use_db']) {
	include_once('db/db_session.php');
	session_set_save_handler(
		array("db_session", "open"),
		array("db_session", "close"),
		array("db_session", "read"),
		array("db_session", "write"),
		array("db_session", "destroy"),
		array("db_session", "gc")
	);
}

ini_set('url_rewriter.tags', ''); // 'session.use_trans_id' cannot be set at runtime until PHP5. This line disables duplicate PHPSESSID

session_cache_limiter('nocache');
register_shutdown_function('session_write_close');
session_start();

if (!isset($_SESSION['gender'])){
	$_SESSION['gender'] = 'male';
}

?>