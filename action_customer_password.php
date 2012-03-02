<?php
$_REQUIRE_SSL = true;

require_once('inc.php');

customer::check_login();

$form = html_form::get_form('form_customer_password');

if (!$form->validate()) {
	$form->set_failure();
}


if ( md5($form->get('old_password')) != $_CUSTOMER->password) {
	$form->set_failure('Your current password is incorrect. Please ensure that you typed your password correctly.');
}

if ($form->get('new_password') != $form->get('confirm_password')) {
	$form->set_failure('Your new passwords do not match.');
}

if ($form->get('password') == $form->get('new_password')) {
	$form->set_failure('Your new password cannot be the same as your current one.');
}

$_CUSTOMER->password = md5($form->get('new_password'));


if ($_CUSTOMER->update()) {
	html_message::add('Your password has been changed', 'info');
} 

http::redirect(http::url('customer_password.php'));

?>