<?php

require_once('inc.php');

$form = html_form::get_form('form_forgot_password');

if (!$form->validate()) {
	$form->set_failure();
}

$user_list = new dbo_list('customer', 'WHERE `email` = "'.$form->get('email').'"');
if (($user = $user_list->get_first()) === false) {
	$form->set_failure('Sorry we cannot find your email address in our records. Please ensure your email is typed correctly.');
}

$password = customer::gen_password();
$user->password = md5($password);
//$user->password = $password;
if ($user->update()){
	if (customer::forgot_password($user, $password)) {
		html_message::add('Your new password has been sent to your email account. Please check your email and return to Eyestyle to login.', 'info');
		http::redirect(http::get_path());
	} else {
		$form->set_failure('Sorry we could not send you your password. The server is either down or busy at the moment. Please try again later.');
		http::redirect(http::get_path());
	}
}else{
	$form->set_failure('Sorry we cannot update the record');
	
}

?>

