<?php
$_ACCESS = 'public';

require_once('inc.php');

$form = html_form::get_form('form_login');

if (!$form->validate()) {
	unset($_SESSION['_STAFF_ID']);
	$form->set_failure('Please log in with your usename & password.');
}

$staff_list = new dbo_list('staff', 'WHERE `staff_id` = "'.$form->get('username').'" AND `password` = "'.md5($form->get('password')).'"');
if ($staff = $staff_list->get_first()) {
	$_SESSION['_STAFF_ID'] = $staff->staff_id;
	$staff->last_login = utils_time::db_datetime();
	$staff->update();
	http::redirect('index.php');
} else {
	unset($_SESSION['_STAFF_ID']);
	html_message::add("Invalid Username/Password");
	http::redirect($_CONFIG['access']['login_page']);
}
?>