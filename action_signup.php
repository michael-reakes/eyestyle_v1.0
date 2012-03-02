<?php

require_once('inc.php');

$form = html_form::get_form('form_customer_account_details');

// Check all required fields
if (!$form->validate()) {
	$form->set_failure();
}

// Error handling after required fields check
if (customer::check_existing_email($form->get('email'))) {
	$form->set_failure('Your email has already been registered with Eyestyle. Please enter another email.');
}
else if ($form->get('password') != $form->get('password_confirm')) {
	$form->set_failure('Your passwords do not match.');
}

// No errors
$_CUSTOMER->fullname = $form->get('fullname');
$_CUSTOMER->company = $form->get('company_name');
$_CUSTOMER->password = $form->get('password');
$_CUSTOMER->email = $form->get('email');
$_CUSTOMER->address = $form->get('address');
$_CUSTOMER->suburb = $form->get('suburb');
$_CUSTOMER->state = $form->get('state');
$_CUSTOMER->postcode = $form->get('postcode');
$_CUSTOMER->country = $form->get('country');
$_CUSTOMER->phone = $form->get('phone');
$_CUSTOMER->mobile = $form->get('mobile');
$_CUSTOMER->insert();

//also need to provide few functionalities to subscribe later if customer doesnt choose to do it now.
$subscribe = $form->get('subscribe');
if (count($subscribe) != 0){
	$subscriber_list = new dbo_list('subscriber','WHERE email = "'.$form->get('email').'"');
	if ($subscriber_list->count() == 0){
		$subscriber = new dbo('subscriber');
		$subscriber->fullname = $form->get('fullname');
		$subscriber->email = $form->get('email');
		$subscriber->date_created = utils_time::db_datetime();
		$subscriber->status = "active";
		$subscriber->insert();
	}
}

//send confirmation email
$customer = new dbo('customer',$_CUSTOMER->customer_id);
if (customer::send_signup_email($customer,$form->get('password'))) {
	$_CUSTOMER->password = md5($form->get('password'));
	$_CUSTOMER->update();

	html_message::add('Your are now registered to Eyestyle','info');
	if ($_CHECKOUT->active){
		http::redirect('checkout.php');
	}
	else{
		http::redirect('myaccount.php');
	}
}else{
	html_message::add('There is a problem in sending email to your account. Please contact us at '.$_CONFIG['company']['contact_email'].' for help');
	http::redirect('signup.php');
}


?>