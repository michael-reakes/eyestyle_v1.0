<?php
$_ACCESS = 'staff.account';

require_once('inc.php');

$form = html_form::get_form('form_staff_staff_add');

if (!$form->validate()) {
	$form->set_failure();
}
if ($form->get('password') != $form->get('password_confirm')) {
	$form->set_failure('The passwords entered do not match.');
}

$staff_list = new dbo_list('staff', 'WHERE `staff_id` = "'.$form->get('staff_id').'"');
if ($staff_list->count() > 0) {
	$form->set_failure('Username already exists. Please choose another username.');
}

if (count($form->get('location')) == 0) {
	$form->set_failure('Please select at lease one accessible location.');
}

$staff = new dbo('staff');
$staff->staff_id = $form->get('staff_id');
$staff->fullname = $form->get('fullname');
$staff->password = md5($form->get('password'));
$staff->group_id = $form->get('group_id');
$staff->access = access::access_string($form->get('access'));
$staff->insert();

html_message::add('Staff account created successfully.', 'info');
http::redirect(http::get_path());
?>