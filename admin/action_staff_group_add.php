<?php
$_ACCESS = 'staff.group';

require_once('inc.php');

$form = html_form::get_form('form_staff_group_add');

if (!$form->validate()) {
	$form->set_failure();
}

$group = new dbo('staff_group');
$group->name = $form->get('name');
$group->access = access::access_string($form->get('access'));
$group->insert();

html_message::add('User group created successfully.', 'info');
http::redirect(http::get_path());
?>