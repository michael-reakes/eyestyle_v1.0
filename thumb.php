<?php
/*****************************************************************************
* Filename: thumb.php
* Copyright: 2005 S3 Group Pty Ltd (www.s3design.com.au)
*
* GET Parameters:
* f - filename/path
* h - thumbnail height
* w - thumbnail width
* mh - thumbnail maximum height
* mw - thumbnail maximum width
* j - JPEG output quality (1-100, default 90)
*
* Precedence of parameters:
* m > w > h
*
* Example:
* <img src="thumb.php?f=./banner.jpg&w=200&j=80"/>
* <img src="thumb.php?f=http://www.s3design.com.au/banner.jpg&mw=200&mh=100"/>
*****************************************************************************/

require_once('config_global.php');
require_once('include/inc_db.php');
require_once("include/utils/utils_image_resize.php");

if (isset($_CONFIG['session']['use_db']) && $_CONFIG['session']['use_db']) {
	require_once('include/db/db_session.php');
	session_set_save_handler(
		array("db_session", "open"),
		array("db_session", "close"),
		array("db_session", "read"),
		array("db_session", "write"),
		array("db_session", "destroy"),
		array("db_session", "gc")
	);
}

if (isset($_GET["nocache"])) {
	session_cache_limiter('nocache');
} else {
	session_cache_limiter('public');
}

session_start();

if (empty($_GET["f"])) {
	exit();
}

$thumb = new utils_image_resize($_GET["f"]);

if (!empty($_GET["h"])) {
	$thumb->size_height($_GET["h"]);
}

if (!empty($_GET["w"])) {
	$thumb->size_width($_GET["w"]);
}

if (!empty($_GET["hf"])) {
	$thumb->size_height($_GET["hf"], false);
}

if (!empty($_GET["wf"])) {
	$thumb->size_width($_GET["wf"], false);
}

if (!empty($_GET["mh"]) && !empty($_GET["mw"])) {

	$thumb->size_auto($_GET["mh"], $_GET["mw"]);
}

if (!empty($_GET["j"])) {
	$thumb->jpeg_quality($_GET["j"]);
}

if (!empty($_GET['ds'])) {
	$thumb->show_ds();
} else {
	$thumb->show();
}

?>