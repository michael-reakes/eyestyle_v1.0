<?php
$_ACCESS = 'order';

require_once('inc.php');

$form = html_form::get_form('form_order_order_dispatch_edit');


$id = $form->get('id');
$fullname = $form->get('Dfullname');
$address = $form->get('Daddress');
$suburb = $form->get('Dsuburb');
$postcode = $form->get('Dpostcode');
$state = $form->get('Dstate');
$country = $form->get('Dcountry');
$phone = $form->get('Dphone');

$order = new dbo('order', $id);

$order->delivery_fullname = $fullname;
$order->delivery_address = $address;
$order->delivery_suburb = $suburb;
$order->delivery_postcode = $postcode;
$order->delivery_state = $state;
$order->delivery_country = $country;
$order->delivery_phone = $phone;

$order->update();

//var_dump(mysql_error()); exit;

html_message::add('Dispatch address updated successfully', 'info');
http::redirect('order_order_view.php?id='.$id);

?>