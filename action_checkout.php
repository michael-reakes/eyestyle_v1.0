<?
$_REQUIRE_SSL = true;
require_once('inc.php');
require_once('EwayPayment.php');

if (!isset($_GET['step'])) {
	http::redirect('checkout.php?step=1');
} else {
	$step = $_GET['step'];
}

$form = html_form::get_form('form_checkout');

//set to true if SSL is used on the server
$secure_site = true;

switch ($step){
	case 1:
		if (!$form->validate()) {
			$form->set_failure();
		}
		$fields = array('billing_name'=>'Bfullname',
				'billing_address'=>'Baddress',
				'billing_suburb'=>'Bsuburb',
				'billing_state'=>'Bstate',
				'billing_country'=>'Bcountry',
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
					'delivery_country'=>'Dcountry',
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

			$cc_type = $form->get('cc_type');
			$cc_no = $form->get('cc_no');
			$ccv_no = $form->get('ccv_no');
			$cc_name = $form->get('cc_name');
			$cc_exp_year = $form->get('cc_exp_year');
			$cc_exp_month = $form->get('cc_exp_month');
			
			$cc_result = utils_validation::cc($form->get('cc_no'),$form->get('cc_type'),$form->get('cc_exp_month'),$form->get('cc_exp_year'));
			if ($cc_result !== true) {
				$form->set_failure(utils_validation::cc_error($cc_result));
			}

			if ($cc_type == '' || $cc_no == '' || $cc_name == ''  || $cc_exp_year == '' || $cc_exp_month == ''){
				$form->set_failure('Some of the credit card details are wrong or missing');
			}

			if ($cc_type == 'master') $cc_type = "Master Card";
			else if ($cc_type == 'visa') $cc_type = "Visa";
			else if ($cc_type == 'bankcard') $cc_type = "Bankcard";

			$billing_details = $_CHECKOUT->billing_details;
			$name = explode(" ", $cc_name);
			$firstname = $name[0];
			$len = count($name);
			$lastname = $name[$len-1];
			$address = $billing_details['billing_address'].", ".$billing_details['billing_suburb'].", ".$billing_details['billing_postcode']." ".$billing_details['billing_state']." ".$billing_details['billing_postcode'];
	
			$micro = explode(' ', microtime());
			$unique_ref = date('YmdHis',$micro[1]).substr($micro[0], 2, 6);

			/***** EWAY CODES *******/
			//''
			/* Live code */ 
			$eway = new EwayPayment('18044398');
			$eway->setCustomerFirstname($firstname);
			$eway->setCustomerLastname($lastname);
			$eway->setCustomerEmail($_CHECKOUT->billing_details['billing_email']);
			$eway->setCustomerAddress($address);
			$eway->setCustomerPostcode($_CHECKOUT->billing_details['billing_postcode']);
			$eway->setCustomerInvoiceDescription($unique_ref);
			$eway->setCustomerInvoiceRef($unique_ref);
			$eway->setCardHoldersName( $cc_name );
			$eway->setCardNumber( $cc_no );
			$eway->setCardExpiryMonth( $cc_exp_month );
			$eway->setCardExpiryYear( substr($cc_exp_year,2) );
			$eway->setCardCVN( $ccv_no );
			$eway->setTrxnNumber( $unique_ref );
			$eway->setTotalAmount($_CHECKOUT->total() * 100);
			
			if( $eway->doPayment() == EWAY_TRANSACTION_OK ) {
				//Additional testing needs to be done here
				if ($_CHECKOUT->total() != $eway->getReturnAmount()/100 ){
					$form->set_failure("Error: the amount on the cart is not matching to the payment gateway record");
				}
				$_CHECKOUT->payment_reference = "EWAY Auth Code: ".$eway->getAuthCode();
			} else {
				$form->set_failure("Error: " . $eway->getErrorMessage());
			}

			/****************************/
		}
		else if ($payment_method == 'dd') $payment_method = 'Direct Deposit';
		else if ($payment_method == 'mo') $payment_method = 'Money Order';
		else if ($payment_method == 'pp') {
			$_CHECKOUT->payment_method = 'PayPal';
			if ($order = $_CHECKOUT->create_order($status)){
				/*
				if ($_CHECKOUT->send_checkout_email($order)){
					$_CHECKOUT->send_notification_email($order);
					html_message::add("An email with your purchase details have been sent.",'info');
				}else{
					html_message::add("There is a problem with sending a purchase details email to your email account.");
				}
				*/
			}
			else{
				$form->set_failure("Your order cannot be processed at the moment. The server might be down or experiencing some difficulties. Please try again later or contact us at ".$_CONFIG['company']['contact_email']." for help");
			}
			$_CHECKOUT->order_id = $order->order_id;
			http::redirect('checkout_paypal.php',true);
			break;
			
		}


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
			$form->set_failure("Your order cannot be processed at the moment. The server might be down or experiencing some difficulties. Please try again later or contact us at ".$_CONFIG['company']['contact_email']." for help");
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