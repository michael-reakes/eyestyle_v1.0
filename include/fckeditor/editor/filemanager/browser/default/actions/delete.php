<?php
	require_once('../../../../../inc.php');
	
	if (!isset($_GET['filename'])){
		echo "Filename to be deleted is not set";
		exit;
	}else{
		$filename = $_GET['filename'];
	}

	$root = '/';
	$_SITEMAP['current_path'] = substr( $_SERVER['PHP_SELF'], $root );
	$_SITEMAP['num_directories'] = substr_count($_SITEMAP['current_path'], '/');
	$_LOCAL = '';
	while ($_SITEMAP['num_directories'] > 1) {
		$_LOCAL .= '../';
		$_SITEMAP['num_directories']--;
	}
	$path = $_LOCAL;

	$result = unlink( $path.(substr($filename,1)) );
	if ($result) {
		echo 'true';
	}
	else {
		echo 'Delete unsuccesful:'.$path.(substr($filename,1)).'<br />';
		echo 'Current path = '.$_SITEMAP['current_path'].'<br />';
	}
?>