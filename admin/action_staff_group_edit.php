<?php
$_ACCESS = 'staff.group';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$form = html_form::get_form('form_staff_group_edit');

if (!$form->validate()) {
	$form->set_failure();
}

$group = new dbo('staff_group', $_GET['id']);

$group->name = $form->get('name');
$group->access = access::access_string($form->get('access'));
$group->update();

$staff_array = $group->load_children('staff');
foreach ($staff_array as $staff) {
	$staff->access = $group->access;
	$staff->update();
}

html_message::add('User group updated successfully.', 'info');
http::redirect(http::get_path());
?>