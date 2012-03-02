<?php

if(!isset($_SESSION['customer'])) {
	$_SESSION['customer'] = new customer();
}

$_CUSTOMER = &$_SESSION['customer'];

?>