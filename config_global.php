<?php

$_CONFIG = array();

$_CONFIG['site']['http_host']	= 'www.eyestyle.com.au';		// default: requested hostname
$_CONFIG['site']['http_port']	= '';		// defalt: 80
$_CONFIG['site']['https_host']	= 'www.eyestyle.com.au';		// default: requrested hostname
$_CONFIG['site']['https_port']	= '';		// defaut: 430

$_CONFIG['db']['host']			= 'localhost';
$_CONFIG['db']['username']		= 'eye32354_admin';
$_CONFIG['db']['password']		= 'eyestyle321';
$_CONFIG['db']['db']			= 'eye32354_eyestyle';



$_CONFIG['db']['persistent']	= true;
$_CONFIG['db']['num_per_page']	= 24;

$_CONFIG['session']['use_db']	= true;
$_CONFIG['site']['upload_directory'] = "/userfiles/";
$_CONFIG['site']['absolute_path'] = "http://www.eyestyle.com.au";


$_CONFIG['company']['email_image_path'] = 'http://www.eyestyle.com.au/images/';
$_CONFIG['company']['company_name']='Eyestyle';
$_CONFIG['company']['website']='www.eyestyle.com.au';
$_CONFIG['company']['contact_email']='sales@eyestyle.com.au';
$_CONFIG['company']['contact_name']='EYESTYLE.COM.AU';
$_CONFIG['company']['abn']='50 991 738 594';
$_CONFIG['company']['phone']='(02) 6766 6766';
$_CONFIG['company']['fax']='';

$_CONFIG['company']['mail_address1']='PO Box 7';
$_CONFIG['company']['mail_address2']='Tamworth NSW 2340';
$_CONFIG['company']['mail_address3']='Australia';
$_CONFIG['site']['url'] = 'http://www.eyestyle.com.au/';

// PayPal
$_CONFIG['paypal']['server'] = 'www.paypal.com';
$_CONFIG['paypal']['account'] = 'sales@eyestyle.com.au';