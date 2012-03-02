<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	if (!isset($_REQUIRE_SSL)) {
		$_REQUIRE_SSL = false;
	}
	if ($_SERVER['SERVER_PORT']  == 443 || (!empty($_CONFIG['site']['https_port']) && $_SERVER['SERVER_PORT'] == $_CONFIG['site']['https_port'])) {
		$is_ssl = true;
	} else {
		$is_ssl = false;
	}

	$url = (SITE_APP_ROOT != '/' ? substr($_SERVER['REQUEST_URI'], strlen(SITE_APP_ROOT)) : $_SERVER['REQUEST_URI']);
	
	if (substr($url, 0, 1) != '/') $url = '/'.$url;

	if ($_REQUIRE_SSL && !$is_ssl) {
		$host = !empty($_CONFIG['site']['https_host']) ? $_CONFIG['site']['https_host'] : $_SERVER['SERVER_NAME'];
		$port = !empty($_CONFIG['site']['https_port']) ? ':'.$_CONFIG['site']['https_port'] : '';
		$dir = !empty($_CONFIG['site']['https_dir']) ? $_CONFIG['site']['https_dir'] : '';
		$url = 'https://'.$host.$port.$dir.$url;
		http::redirect($url);
	}
	if (!$_REQUIRE_SSL && $is_ssl) {
		$host = !empty($_CONFIG['site']['http_host']) ? $_CONFIG['site']['http_host'] : $_SERVER['SERVER_NAME'];
		$port = !empty($_CONFIG['site']['http_port']) ? ':'.$_CONFIG['site']['http_port'] : '';
		$url = 'http://'.$host.$port.$url;
		http::redirect($url);
	}
}
?>