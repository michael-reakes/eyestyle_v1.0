<?php
define('SITE_APP_ROOT', '/');

require_once('../config_global.php');
require_once('config_local.php');

// load library
require_once('../include/http/http.php');
require_once('../include/html/html.php');
require_once('../include/html/html_form.php');
require_once('../include/html/html_message.php');
require_once('../include/html/html_pager.php');
require_once('../include/html/html_text.php');
require_once('../include/html/html_template.php');
require_once('../include/utils/utils_time.php');
require_once('../include/utils/utils_email.php');
require_once('../include/utils/utils_image_resize.php');
require_once('../include/utils/utils_validation.php');
require_once('../include/utils/utils_thumbnails.php');
require_once('../include/utils/utils_smtp.php');
require_once('../include/webpulse/webpulse.php');

// load checkout
require_once('../include/checkout/checkout.php');
require_once('../include/checkout/checkout_delivery.php');

// connect to database
require_once('../include/inc_db.php');
require_once('../include/inc_session.php');

// apply access restriction
require_once('../include/inc_access.php');