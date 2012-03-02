<?php


// All information stored in $_SESSION['checkout']
// -----------------------------------------------
// STEPS IN CHECKOUT:
// 1: Delivery
// 2. Payment
// 3. Receipt

class checkout {

	var $billing_details;
	var $delivery_details;
	var $payment_reference;
	var $payment_method;
	var $active;
	var $cart; // array(product_id=>qty)
	var $order_id; 
	var $comment;
	var $same_to_billing; //a flag to record customer's preference
	var $redirectedtopaypal;

	function checkout() {
		$this->cart = array();
		$this->active = false;
		$this->delivery_details = array();
		$this->billing_details = array();
		$this->comment = "";
		$this->redirectedtopaypal = FALSE;
	}

	//****** BILLING and DELIVERY ADDRESS HANDLERS ******//
	//to set delivery details from an input array
	function set_delivery_details($delivery_arr) {
		$this->delivery_details = $delivery_arr;
		$this->same_to_billing = false;
	}
	
	//to set billing details from an input array
	function set_billing_details($billing_arr){
		$this->billing_details = $billing_arr;
	}
	
	//return true if the billing details are already filled with values
	function is_set_billing_details(){
		if (count($this->billing_details) > 0) return true;
		else return false;
	}

	//to set billing details from customer object store in session
	//make sure that all fields are represented especially whent he customer
	//object is different from normal
	function set_billing_from_db(){
		GLOBAL $_CUSTOMER;
		$fields = array('billing_name'=>'fullname',
		'billing_address'=>'address',
		'billing_suburb'=>'suburb',
		'billing_state'=>'state',
		'billing_postcode'=>'postcode',
		'billing_country'=>'country',
		'billing_phone'=>'phone',
		'billing_email'=>'email',
		'billing_mobile'=>'mobile');
		$billing_arr = array();
		foreach($fields as $key=>$value){
			$billing_arr[$key] = $_CUSTOMER->$value;
		}
		$this->billing_details = $billing_arr;
	}
	
	//copying the delivery details from the billing details
	function same_delivery_details(){
		GLOBAL $_CUSTOMER;
		$fields = array('delivery_name'=>'billing_name',
		'delivery_address'=>'billing_address',
		'delivery_suburb'=>'billing_suburb',
		'delivery_state'=>'billing_state',
		'delivery_postcode'=>'billing_postcode',
		'delivery_country'=>'billing_country',
		'delivery_phone'=>'billing_phone',
		'delivery_email'=>'billing_email');
		$delivery_arr = array();
		foreach($fields as $key=>$value){
			$delivery_arr[$key] = $this->billing_details[$value];
		}
		$this->delivery_details = $delivery_arr;
		$this->same_to_billing = 'true';
	}

	// -------------- CART HANDLERS --------------
	function cart_add ($product_id, $qty) {
		if ($this->__cart_get_item($product_id) !== false) {
			$this->cart[$product_id] += $qty;
		} else {
			$this->cart[$product_id] = $qty;
		}
		$this->active = true;
		$this->cart_confirm_stock($product_id, $this->cart[$product_id]);

	}



	function __cart_get_item ($product_id) {
		for ($i=0; $i<count($this->cart); $i++) {
			if (isset($this->cart[$product_id])) {
				return true;
			}
		}
		return false;
	}

	function cart_remove ($product_id) {
		if ($this->__cart_get_item($product_id)) {
			unset($this->cart[$product_id]);
		}
	}

	function cart_update ($product_id, $qty) {
		if ($this->__cart_get_item($product_id) !== false) {
			if (intval($qty) <= 0) {
				$this->cart_remove($product_id);
			} else {
				$this->cart_confirm_stock($product_id, intval($qty));
			}
		}
	}

	function cart_total_items () {
		$total = 0;
		foreach($this->cart as $key=>$value) {
			$total = $total + $value;
		}
		return $total;
	}
	
	function cart_total () {
		$total = 0;
		foreach($this->cart as $key=>$value) {
			$lens = new dbo('lens',$key);
			$colour = new dbo('colour',$lens->colour_id);
			$product = new dbo('product',$colour->product_id);
			$total += ($product->price)*$value;
		}

		return $total;
	}

	function cart_confirm_stock($pid, $qty) {
		/*
		$lens = new dbo('lens',$pid);
		if ($lens->quantity <= 0){
			html_message::add("Sorry there is currently no stock for ".$lens->name);
			$this->cart_remove($pid);
		}
		else if ($qty > $lens->quantity) {
			html_message::add('There '.($lens->quantity == 1 ? 'is' : 'are').' currently only '.$lens->quantity.' available in stock for '.$lens->name.'. The quantity has been updated to '.$lens->quantity.'.','warning');
			$this->cart[$pid] = $lens->quantity;
		} else {
			$this->cart[$pid] = $qty;
		}
		*/
		$this->cart[$pid] = $qty;
	}

	function confirm_cart_stock() {
		/*
		foreach($this->cart as $key=>$value) {
			$this->cart_confirm_stock($key, $value);
		}
		*/
	}

	// -------------- ORDER HANDLERS -------------- 
	//Usually this in the form of 'Credit Card','Money Order','Direct Deposit'
	function set_payment_method($payment_method) {
		$this->payment_method = $payment_method;
	}

	function set_comment($comment) {
		$this->comment = $comment;
	}

	function create_order($status='unconfirmed') {
		global $_CUSTOMER;
		$delivery_details = $this->delivery_details;
		$billing_details = $this->billing_details;

		$order = new dbo('order');

		$order->customer_id = $_CUSTOMER->customer_id;

		$order->billing_fullname = $billing_details['billing_name'];
		$order->billing_phone = $billing_details['billing_phone'];
		$order->billing_address = $billing_details['billing_address'];
		$order->billing_suburb = $billing_details['billing_suburb'];
		$order->billing_postcode = $billing_details['billing_postcode'];
		$order->billing_state = $billing_details['billing_state'];
		$order->billing_country = $billing_details['billing_country'];
		$order->billing_email = $billing_details['billing_email'];
		$order->billing_mobile = ''; //$billing_details['billing_mobile'];
		
		$order->payment_method = $this->payment_method;
		$order->payment_reference = $this->payment_reference;
		$order->comment = $this->comment;

		$order->delivery_fullname = $delivery_details['delivery_name'];
		$order->delivery_phone = $delivery_details['delivery_phone'];
		$order->delivery_address = $delivery_details['delivery_address'];
		$order->delivery_suburb = $delivery_details['delivery_suburb'];
		$order->delivery_postcode = $delivery_details['delivery_postcode'];
		$order->delivery_state = $delivery_details['delivery_state'];
		$order->delivery_country = $delivery_details['delivery_country'];

		$order->total = $this->total();
		$order->delivery_cost = $this->delivery();

		$order->status = $status;
		
		// victor
		$order->date_delivered = '0000-00-00 00:00:00';
		
				
		$order->date_created = utils_time::db_datetime();
		//Credit card payment will go as a confirmed purchased
		if ($status == 'confirmed') {
			$order->date_processed = utils_time::db_datetime();
		}
		else {
			$order->date_processed = '0000-00-00 00:00:00';
		}

		if ($order->insert()) {
			$this->create_order_items($order);
			return $order;
		} else {
			echo mysql_error();exit;
			return false;
		}
	}
	
	/// update_order by victor
	
	function update_order($data, $order_id) {
		global $_CUSTOMER;

		$order = new dbo('order');
		if ($order->load($order_id)) {
			$order->status = $data['status'];
			$order->payment_reference = $data['payment_reference'];
			
			if ($data['status'] == 'confirmed') {
				$order->date_processed = utils_time::db_datetime();
			} else {
				$order->date_processed = '0000-00-00 00:00:00';
			}
			
			if ($order->update()) {
				return $order;
			} else {
				return false;
			}
		}
		return false;
	}
	
	/// end update_order by victor

	function create_order_items($order) {
		foreach($this->cart as $pid=>$qty) {
			$lens = new dbo('lens',$pid);
			$colour = new dbo('colour',$lens->colour_id);
			$product = new dbo('product',$colour->product_id);
			$lens->quantity -= $qty;
			$lens->update();

			$order_item = new dbo('order_item');
			$order_item->order_id = $order->order_id;
			$order_item->product_id = $product->product_id;
			$order_item->unit_price = $product->price;
			$order_item->quantity = $qty;
			$order_item->lens_name = $lens->name;
			$order_item->colour_name = $colour->name;
			$order_item->code = $lens->code;
			$order_item->insert();
		}
	}

	// -------------- CHECKOUT HANDLERS -------------- 
	function delivery() {
		if (count($this->delivery_details) > 0){ 
			if ($this->delivery_details['delivery_country'] == 'AU') 
				$zone_id = checkout_delivery::postcode_to_zone($this->delivery_details['delivery_postcode']);
			else
				$zone_id = checkout_delivery::country_to_zone($this->delivery_details['delivery_country']);
			
			$total_weight = 0;
			foreach($this->cart as $pid=>$quantity) {
				$lens = new dbo('lens',$pid);
				$colour = new dbo('colour',$lens->colour_id);
				$product = new dbo('product',$colour->product_id);
				for ($i=0; $i<$quantity; $i++) {
					$total_weight = $total_weight + $product->weight;
				}
			}

			return checkout_delivery::calculate($zone_id, $total_weight);
		}
		
	}

	function gst() {
		return ($this->total()/11);
	}

	function subtotal() {
		return $this->cart_total();
	}

	function total() {
		return ($this->subtotal() + $this->delivery());
	}

	function checkout_reset() {
		$this->cart = array();
		$this->delivery_details = array();
		$this->billing_details = array();
		$this->same_to_billing = false;
		$this->active = false;
		$this->comment = "";
		$this->payment_reference = "";
		$this->payment_method = "";
		$this->redirectedtopaypal = FALSE;
	}
	/* CHECKOUT EMAIL HANDLERS */

	//generate body: order, type of body can be invoice or order confirmation, html (true/false)
	function generate_body($order,$type,$html,$show_print,$admin){
		global $_CONFIG;

		$country = new dbo('country');
		$billing_country = $country->load($order->billing_country) ? $country->name : '';
		$country = new dbo('country');
		$delivery_country = $country->load($order->delivery_country) ? $country->name : '';

		$tpl = new html_template();
		$tpl->set('abn',$_CONFIG['company']['abn']);
		$tpl->set('image_path',$_CONFIG['company']['email_image_path']);
		$tpl->set('address_1', $_CONFIG['company']['mail_address1']);
		$tpl->set('address_2', $_CONFIG['company']['mail_address2']);
		$tpl->set('address_3', $_CONFIG['company']['mail_address3']);
		$tpl->set('site',$_CONFIG['site']['url']);
		$tpl->set('phone', $_CONFIG['company']['phone']);
		$tpl->set('email', $_CONFIG['company']['contact_email']);
		$tpl->set('order_id',$order->order_id);
		$tpl->set('order_date', date('j M Y', utils_time::timestamp($order->date_created)) );
		$tpl->set('order_status', $order->status);
		$tpl->set('billing_name', $order->billing_fullname);
		$tpl->set('billing_address', $order->billing_address);
		$tpl->set('billing_suburb', $order->billing_suburb);
		$tpl->set('billing_state',$order->billing_state);
		$tpl->set('billing_country',$billing_country);
		$tpl->set('billing_postcode',$order->billing_postcode);
		$tpl->set('billing_phone',$order->billing_phone);
		$tpl->set('billing_email',$order->billing_email);
		$tpl->set('delivery_name',$order->delivery_fullname);
		$tpl->set('delivery_address',$order->delivery_address);
		$tpl->set('delivery_suburb',$order->delivery_suburb);
		$tpl->set('delivery_state',$order->delivery_state);
		$tpl->set('delivery_postcode',$order->delivery_postcode);
		$tpl->set('delivery_phone',$order->delivery_phone);
		$tpl->set('delivery_country',$delivery_country);
		$tpl->set('delivery',html_text::currency($order->delivery_cost));
		$tpl->set('delivery_gst',html_text::currency($order->delivery_cost/11));
		$tpl->set('subtotal',html_text::currency($order->total - $order->delivery_cost));
		$tpl->set('total_gst',html_text::currency($order->total/11));
		$tpl->set('total',html_text::currency($order->total));

		$consignment_info = "";
		$status_info = "";
		if ($order->status == "delivered"){
			$courier_list = new dbo_list('courier','WHERE `name` = "'.$order->courier_name.'"');
			$courier = $courier_list->get_first();
			$status_info = "Your order (invoice no: ".$order->order_id.") has been dispatched. <br />";
			$status_info .= "Please see the following delivery details for your reference. <br /><br />";
			$status_info .= "Courier Company:&nbsp;&nbsp;&nbsp;".$order->courier_name." (contact no: ".$courier->contact.") <br />";
			$status_info .= "Tracking number:&nbsp;&nbsp;&nbsp;".$order->tracking_no." <br />";
		}elseif($order->status == "processing"){
			$status_info = "Your order (invoice no: ".$order->order_id.")  is now being processed. Another email notification will be sent to you once your purchase has been dispatched.<br />";
		}
		$tpl->set('status_info',$status_info);
		
		//payment_info is only used for DD and MO confirmation email
		$payment_info = "";
		if ($order->payment_method == 'Direct Deposit'){
			$payment_info = "<b>Payment Information: </b><br />";
			$payment_info .= "Please direct deposit into the bank account below:<br />";
			$payment_info .= "Account Name: EYESTYLE.COM.AU<br />";
			$payment_info .= "Bank Name: National Australia Bank<br />";
			$payment_info .= "BSB: 082842 Account Number: 790955564<br /><br />";
			$payment_info .= 'For deposit via internet banking, please put your invoice number (No: '.$order->order_id.') as the payment reference.';
		}
		else if ($order->payment_method == 'Money Order'){
			$payment_info = "<b>Payment Information: </b><br />";
			$payment_info .= "Please send your bank cheque/money order to our address below: <br /> ";
			$payment_info .= $_CONFIG['company']['mail_address1']."<br />";
			$payment_info .= $_CONFIG['company']['mail_address2']."<br />";
			$payment_info .= $_CONFIG['company']['mail_address3']."<br /><br />";
			$payment_info .= 'Please write your invoice number <b>(No: '.$order->order_id.')</b> with your name and contact number on the back of the cheque or money order.';
		}
		$tpl->set('payment_information',$payment_info);

		if ($show_print) {
			$text = '<br/><div id="print_btn" style="text-align:center;"><input type="button" value="Print" onclick="javascript:window.print();" /></div>';
			$tpl->set('print',$text);
		} else {
			$tpl->set('print','');
		}

		$html = true;
		$prod_str = '';
		$order_item_list = $order->load_children('order_item');
		if ($html) {
			foreach($order_item_list as $order_item) {
				$product = new dbo('product',$order_item->product_id);
				$brand = new dbo('brand',$product->brand_id);
				$unit_price = $order_item->unit_price;
				$prod_str .= '<tr>';
				
				if ($type == 'invoice') {
					$prod_str .= '<td style="border-bottom:1px solid #999;padding:10px;">Code: '.(!empty($order_item->code) ? $order_item->code : 'N/A').'<br />'.$brand->name.' - '.$product->name.' (Frame:'.$order_item->colour_name.' / Lens:'.$order_item->lens_name.')</td>';
					$prod_str .= '<td style="border-bottom:1px solid #999;padding:10px;text-align:center">'.$order_item->quantity.'</td>';
					$prod_str .= '<td style="border-bottom:1px solid #999;padding:10px;text-align:right">'.html_text::currency($unit_price).'</td>';
					$prod_str .= '<td style="border-bottom:1px solid #999;padding:10px;text-align:right">'.html_text::currency( ($unit_price) * $order_item->quantity).'</td>';
				} else {
					$prod_str .= '<td style="border:1px solid #000;border-top:0px;">Code: '.(!empty($order_item->code) ? $order_item->code : 'N/A').'<br />'.$brand->name.' - '.$product->name.' (Frame:'.$order_item->colour_name.' / Lens:'.$order_item->lens_name.')</td>';
					$prod_str .= '<td style="border:1px solid #000;border-left:0px;border-top:0px;text-align:center">'.$order_item->quantity.'</td>';
					$prod_str .= '<td style="border:1px solid #000;border-left:0px;border-top:0px;text-align:right">'.html_text::currency($unit_price).'</td>';
					$prod_str .= '<td style="border:1px solid #000;border-left:0px;border-top:0px;text-align:right">'.html_text::currency( ($unit_price) * $order_item->quantity).'</td>';
				}
				
				$prod_str .= '</tr>';
			}
		} else {
			foreach($order_item_list as $order_item) {
				$product = new dbo('product',$order_item->product_id);
				$brand = new dbo('brand',$product->brand_id);
				$prod_str .= $order_item->quantity." X ".$brand->name.' - '.$product->name." [".html_text::currency($product->price)."]\n";
			}
		}

		$tpl->set('purchased_products',$prod_str);		
		
		//accessor
		if ($admin == 'admin') $location = "../";
		else $location = "";


		if ($type == 'invoice'){
			$text = $tpl->fetch($location."templates/email_invoice.html");
			$html_result = $tpl->fetch($location."templates/email_invoice.html");
		}else if ($type == 'confirmation'){
			$text = $tpl->fetch($location."templates/email_confirmation.html");
			$html_result = $tpl->fetch($location."templates/email_confirmation.html");
		}else if ($type == 'notification'){
			$text = $tpl->fetch($location."templates/email_notification.html");
			$html_result = $tpl->fetch($location."templates/email_notification.html");
		}else if ($type == 'status_change'){
			$text = $tpl->fetch($location."templates/email_status_change.html");
			$html_result = $tpl->fetch($location."templates/email_status_change.html");
		}
		if ($html) return $html_result;
	}

	function send_checkout_email($order){
		global $_CONFIG;
		$mail = new utils_email();
		$from = array($_CONFIG['company']['contact_email'], $_CONFIG['company']['contact_name']);
		$to = $order->billing_email;
		if ($this->payment_method == 'Credit Card') $type = 'invoice';
			else $type = 'confirmation';
		$html = $this->generate_body($order,$type,true,false,'');
		$subject = "EYESTYLE.COM.AU Order Confirmation (Invoice: ".$order->order_id.")";
		$mail->new_mail($from, $to, $subject, $html, $html);
		return $mail->send();
	}

	function send_notification_email($order){
		global $_CONFIG;
		$from = array($_CONFIG['company']['contact_email'], $_CONFIG['company']['contact_name']);
		$preference = new dbo('preference','email_notification_to');
		$mail = new utils_email();
		$from = array($_CONFIG['company']['contact_email'], $_CONFIG['company']['contact_name']);
		$to = $preference->value;
		$html = $this->generate_body($order,'notification',true,false,'');
		$subject = "EYESTYLE.COM.AU Order Notification (Invoice: ".$order->order_id.")";
		$mail->new_mail($from, $to, $subject, $html, $html);
		return $mail->send();
	}
	
	//sending invoice email to customer, used in admin after a change from unconfirmed to confirmed order
	function send_invoice($order){
		global $_CONFIG;
		$mail = new utils_email();
		$from = array($_CONFIG['company']['contact_email'], $_CONFIG['company']['contact_name']);
		$to = $order->billing_email;
		$html = $this->generate_body($order,'invoice',true,false,'admin');
		$subject = "EYESTYLE.COM.AU Invoice (Invoice: ".$order->order_id.")";
		$mail->new_mail($from, $to, $subject, $html, $html);
		return $mail->send();
	}
	
	//send email, type = "status_change", accessed from ("frontend", "admin"), $object most likely of the type order
	function send_email($type = "status_change",$accessor='admin', $order){
		global $_CONFIG;
		$mail = new utils_email();
		$from = array($_CONFIG['company']['contact_email'], $_CONFIG['company']['contact_name']);
		$to = $order->billing_email;
		$html = $this->generate_body($order,'status_change',true,false,$accessor);
		$subject = "EYESTYLE.COM.AU Order Status Update (Invoice: ".$order->order_id.")";
		$mail->new_mail($from, $to, $subject, $html, $html);
		return $mail->send();
	}
}