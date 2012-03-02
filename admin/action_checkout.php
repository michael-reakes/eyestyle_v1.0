<?

require_once('inc.php');

if (!isset($_GET['step'])) {
	http::redirect('checkout.php?step=1');
} else {
	$step = $_GET['step'];
}

$form = html_form::get_form('form_checkout');

//set to true if SSL is used on the server
$secure_site = false;

switch ($step){
	case 1:
		if (!$form->validate()) {
			$form->set_failure();
		}
		$fields = array('billing_name'=>'Bfullname',
				'billing_address'=>'Baddress',
				'billing_suburb'=>'Bsuburb',
				'billing_state'=>'Bstate',
				'billing_postcode'=>'Bpostcode',
				'billing_phone'=>'Bphone',
				'billing_email'=>'Bemail');
		$billing_details = array();
		foreach($fields as $key=>$value){
			$billing_details[$key] = $form->get($value);
		}
		$_CHECKOUT->set_billing_details($billing_details);
		break;
	case 2:
		if (!$form->get('Dsame_as_billing')){
			if (!$form->validate()) {
				$form->set_failure();
			}
			$fields = array('delivery_name'=>'Dfullname',
					'delivery_address'=>'Daddress',
					'delivery_suburb'=>'Dsuburb',
					'delivery_state'=>'Dstate',
					'delivery_postcode'=>'Dpostcode',
					'delivery_phone'=>'Dphone',
					'delivery_email'=>'Demail');
			foreach($fields as $key=>$value){
				$delivery_details[$key] = $form->get($value);
			}
			$_CHECKOUT->set_delivery_details($delivery_details);
		}else{
			$_CHECKOUT->same_delivery_details();
		}
		break;
	case 3:
		$_CHECKOUT->set_comment($form->get('comment'));
		break;
	case 4:
		if (!$form->validate()) {
			$form->set_failure();
		}
		$payment_method = $form->get('payment_method');
		$status = 'unconfirmed';

		if ($payment_method == 'cc') {
			$payment_method = 'Credit Card';
			$status = 'confirmed';
		}
		else if ($payment_method == 'dd') $payment_method = 'Direct Deposit';
		else if ($payment_method == 'mo') $payment_method = 'Money Order';

		$_CHECKOUT->payment_method = $payment_method;
		
		//depending on the payment method, we might not want to send an invoice but more of confirmation
		//if its CC we send invoice otherwise we send confirmation 
		
		if ($order = $_CHECKOUT->create_order($status)){
			if ($_CHECKOUT->send_checkout_email($order)){
				$_CHECKOUT->send_notification_email($order);
				html_message::add("An email with your purchase details have been sent.",'info');
			}else{
				html_message::add("There is a problem with sending a purchase details email to your email account.");
			}
		}
		else{
			html_message::add("Your order cannot be processed at the moment. The server might be down or experiencing some difficulties. Please try again later or contact us at ".$_CONFIG['company']['contact_email']." for help");
		}
		$_CHECKOUT->order_id = $order->order_id;
		http::redirect('checkout_complete.php',$secure_site);
		break;
	case 5:
		break;
}

if ($step < 5){
	$step++;
	http::redirect('checkout.php?step='.$step,$secure_site);
}
else{
	http::redirect('checkout_complete.php',$secure_site);
}
?>