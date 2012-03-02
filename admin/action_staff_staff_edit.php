<?php
$_ACCESS = 'staff.account';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$form = html_form::get_form('form_staff_staff_edit');

if (!$form->validate()) {
	$form->set_failure();
} elseif ($form->get('new_password') != $form->get('new_password_confirm')) {
	$form->set_failure('The passwords entered do not match.');
}

$staff = new dbo('staff', $_GET['id']);

$staff->fullname = $form->get('fullname');
if ($form->get('new_password') != '') {
	$staff->password = md5($form->get('new_password'));
}
$staff->group_id = $form->get('group_id');
$staff->access = access::access_string($form->get('access'));
$staff->update();

html_message::add('Staff account updated successfully.', 'info');
http::redirect(http::get_path());
?>