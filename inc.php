<?php

define('SITE_APP_ROOT','/');


$_SITEMAP['num_directories'] = substr_count(substr($_SERVER['PHP_SELF'], strlen(SITE_APP_ROOT)), '/');
$_LOCAL = '';
while ($_SITEMAP['num_directories'] > 0) {
	$_LOCAL .= '../';
	$_SITEMAP['num_directories']--;
}

$_ROOT = $_LOCAL;

require_once($_ROOT.'config_global.php');
require_once($_ROOT.'config_local.php');

// load library
require_once($_ROOT.'include/http/http.php');
require_once($_ROOT.'include/html/html.php');
require_once($_ROOT.'include/html/html_form.php');
require_once($_ROOT.'include/html/html_message.php');
require_once($_ROOT.'include/html/html_template.php');
require_once($_ROOT.'include/html/html_pager.php');
require_once($_ROOT.'include/html/html_text.php');
require_once($_ROOT.'include/utils/utils_time.php');
require_once($_ROOT.'include/utils/utils_email.php');
require_once($_ROOT.'include/utils/utils_image_resize.php');
require_once($_ROOT.'include/utils/utils_validation.php');
require_once($_ROOT.'include/utils/utils_thumbnails.php');
require_once($_ROOT.'include/utils/utils_data.php');
require_once($_ROOT.'include/webpulse/url.php');
require_once($_ROOT.'include/webpulse/webpulse.php');

// load checkout
require_once($_ROOT.'include/checkout/checkout.php');
require_once($_ROOT.'include/checkout/checkout_delivery.php');

// load user
require_once($_ROOT.'include/customer/customer.php');


// connect to database
require_once($_ROOT.'include/inc_db.php');
require_once($_ROOT.'include/inc_session.php');
require_once($_ROOT.'include/inc_checkout.php');
require_once($_ROOT.'include/inc_customer.php');

//SSL
require_once($_ROOT.'include/inc_ssl.php');

$_ROOT = webpulse::base_root().SITE_APP_ROOT;