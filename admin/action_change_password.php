<?php
$_ACCESS = 'all';

require_once('inc.php');

$form = html_form::get_form('form_change_password');

if (!$form->validate()) {
	$form->set_failure();
} elseif (md5($form->get('password')) != $_STAFF->password){
	$form->set_failure('Incorrect current password.');
} elseif ($form->get('new_password') != $form->get('new_password_confirm')) {
	$form->set_failure('The passwords entered do not match.');
}

$_STAFF->password = md5($form->get('new_password'));
$_STAFF->update();

html_message::add('Password changed successfully.', 'info');
http::redirect(http::get_path());
?>