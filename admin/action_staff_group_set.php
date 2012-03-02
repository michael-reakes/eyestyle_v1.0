<?php
$_ACCESS = 'staff.account';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

$form = html_form::get_form('form_staff_group_set');

$staff_group = new dbo('staff_group', $form->get('group_id'));

foreach ($ids as $id) {
	$staff = new dbo('staff', $id);
	$staff->group_id = $staff_group->staff_group_id;
	$staff->access = $staff_group->access;
	$staff->update();
}

html_message::add('User group set successfully', 'info');
http::redirect(http::get_path());
?>