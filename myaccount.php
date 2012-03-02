<?php
$_REQUIRE_SSL = true;
require_once('inc.php');
$_PAGE = 'My Account';
customer::check_login();

http::redirect('customer_account_details.php');
?>
