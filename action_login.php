<?php
$_REQUIRE_SSL = true;

require_once('inc.php');

$form = html_form::get_form('form_login');

if ($form->get('email')!='' && $form->get('password')!= '') {
	$user_list = new dbo_list('customer', "WHERE `email` = '".$form->get('email')."' AND `password` = '".md5($form->get('password'))."'");

	if ($user = $user_list->get_first()) {
		$_CUSTOMER->load_customer($user);

		if (($_CHECKOUT->active)){
			http::redirect('checkout.php',true);
		}
		else if (http::get_path() != ''){
			http::redirect(http::get_path());
		}
		else {
			http::redirect('customer_account_details.php');
		}
	
	} else {
		html_message::add("Sorry your Email/Password is incorrect. Please try again.");
		http::redirect(http::get_path());
	}
} else {
	html_message::add("Sorry your Email/Password cannot be empty");
	http::redirect(http::get_path());
}
?>