<?php
$_REQUIRE_SSL = true;

require_once('inc.php');

customer::check_login();

$form = html_form::get_form('form_customer_account_details');

// Check all required fields
if (!$form->validate()) {
	$form->set_failure();
}


// No errors
$_CUSTOMER->fullname = $form->get('fullname');
$_CUSTOMER->company = $form->get('company_name');
$_CUSTOMER->phone = $form->get('phone');
$_CUSTOMER->mobile = $form->get('mobile');
$_CUSTOMER->email = $form->get('email');
$_CUSTOMER->address = $form->get('address');
$_CUSTOMER->suburb = $form->get('suburb');
$_CUSTOMER->country = $form->get('country');
$_CUSTOMER->state = $form->get('state');
$_CUSTOMER->postcode = $form->get('postcode');
$_CUSTOMER->update();

html_message::add('Your details are now updated','info');
http::redirect('customer_account_details.php');

?>