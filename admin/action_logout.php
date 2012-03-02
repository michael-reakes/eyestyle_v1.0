<?php
$_ACCESS = 'all';

require_once('inc.php');

unset($_SESSION['_STAFF_ID']);
html_message::add('You have successfully logged out.', 'info');
http::redirect('index.php');
?>