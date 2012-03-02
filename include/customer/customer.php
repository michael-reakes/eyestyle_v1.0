<?php

class customer {
	var $customer_id;
	var $email;
	var $password;
	var $fullname;
	var $company;
	var $address;
	var $suburb;
	var $state;
	var $postcode;
	var $phone;
	var $mobile;
	var $country;

	function load_customer($customer){
		$this->customer_id = $customer->customer_id;
		$this->email = $customer->email;
		$this->password = $customer->password;
		$this->fullname = $customer->fullname;
		$this->company = $customer->company;
		$this->address = $customer->address;
		$this->suburb = $customer->suburb;
		$this->country = $customer->country;
		$this->state = $customer->state;
		$this->postcode = $customer->postcode;
		$this->phone = $customer->phone;
		$this->mobile = $customer->mobile;
		$customer->last_login = utils_time::db_datetime();
		$customer->update();
	}

	function update(){

		$customer_list = new dbo_list('customer','WHERE customer_id = '.$this->customer_id);
		if ($customer = $customer_list->get_first()) {
			$customer->email = $this->email;
			$customer->password = $this->password;
			$customer->fullname = $this->fullname;
			$customer->company = $this->company;
			$customer->address = $this->address;
			$customer->suburb = $this->suburb;
			$customer->state = $this->state;
			$customer->country = $this->country;
			$customer->postcode = $this->postcode;
			$customer->phone = $this->phone;
			$customer->mobile = $this->mobile;
			if ($customer->update()){	return true; }
			else {
				return false;
			}
		}
		else return false;
	}

	function insert(){
		$customer = new dbo('customer');
		$customer->fullname = $this->fullname;
		$customer->email = $this->email;
		$customer->password = md5($this->password);
		$customer->company = $this->company;
		$customer->address = $this->address;
		$customer->suburb = $this->suburb;
		$customer->state = $this->state;
		$customer->postcode = $this->postcode;
		$customer->country = $this->country;
		$customer->phone = $this->phone;
		$customer->mobile = $this->mobile;
		$customer->date_created = utils_time::db_datetime();
		$customer->last_login = utils_time::db_datetime();
		if ($customer->insert()){
			$this->customer_id = $customer->customer_id;
			return true;
		}
		else {
			return false;
		}
	}


	function logout() {
		global $_CUSTOMER;
		global $_CHECKOUT;

		header("P3P: CP=\"ALL DSP COR NID CURa OUR STP PUR\""); 
		unset($_SESSION["customer"]);
		$_CHECKOUT->checkout_reset();
	}

	function gen_activation_code() {
		$unique = false;

		while (!$unique) {
			$code = md5(microtime());

			$user_list = new dbo_list('user', 'WHERE `activation_code` = "'.$code.'"');
			if ($user_list->get_first() === false) {
				return $code;
			}
		}
	}

	function check_login($msg='') {
		global $_CUSTOMER;
		global $_CONFIG;
		if ( !isset($_CUSTOMER->email) || !isset($_CUSTOMER->password)) {
			if (!empty($msg)) {
				html_message::add($msg);
			}
			//http::redirect($_CONFIG['site']['login_page']);
			http::redirect('login.php');
		}
	}

	function gen_password($length=7) {
		$salt = "abchefghjkmnpqrstuvwxyz0123456789";
		$pass = "";
		$i = 0;
		while ($i <= $length) {
			$num = rand() % 33;
			$tmp = substr($salt, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}

	function country_list() {
		$country_options = array('AU'=>'Australia');
		$country_list = new dbo_list('country', 'WHERE `zone_id` != 0', 'name');
		foreach ($country_list->get_all() as $country) {
			$country_options[$country->code] = html_text::digest($country->name,30);
		}

		return $country_options;
	}

	function state_list() {
		$state_options = array();
		$state_list = array('NSW','VIC','QLD','ACT','SA','WA','NT','TAS');
		foreach($state_list as $state) {
			$state_options[$state] = $state;
		}

		return $state_options;
	}

	function cc_type_list() {
		global $_CONFIG;
		$cc_type_options = array();
		$cc_type_list = array('visa'=>'VISA','master'=>'Mastercard','bankcard'=>'Bankcard');
		foreach($cc_type_list as $key=>$value) {
			$cc_type_options[$key] = $value;
		}

		return $cc_type_options;
	}

	function month_list() {
		$month_options = array();
		$month_list = array('01'=>'Jan',
			'02'=>'Feb',
			'03'=>'Mar',
			'04'=>'Apr',
			'05'=>'May',
			'06'=>'Jun',
			'07'=>'Jul',
			'08'=>'Aug',
			'09'=>'Sep',
			'10'=>'Oct',
			'11'=>'Nov',
			'12'=>'Dec'
		);

		foreach($month_list as $key=>$value) {
			$month_options[$key] = $value;
		}

		return $month_options;
	}

	function year_list() {
		 $year_options = array();
		 $year_list = array();
		 $current_year = date('Y');
		 for ($i = $current_year; $i < $current_year + 10; $i++) $year_list[] = $i;
		 foreach($year_list as $year) $year_options[$year] = $year;
		 return $year_options;
	}

	function check_existing_username($username) {
		$user_list = new dbo_list('user','WHERE `username` = "'.$username.'"');
		if ($user_list->count() > 0) {
			return true;
		} else {
			return false;
		}
	}

	function check_existing_email($email) {
		$user_list = new dbo_list('customer','WHERE `email` = "'.$email.'"');
		if ($user_list->count() > 0) {
			return true;
		} else {
			return false;
		}
	}

	function forgot_password($customer, $password) {
		global $_CONFIG;

		$mail = new utils_email();

		$from = array($_CONFIG['company']['contact_email'], $_CONFIG['company']['contact_name']);
		
		$to = $customer->email;

		$subject = "Eyestyle Password Information";

		$tpl = new html_template();
		$tpl->set('site', $_CONFIG['site']['url']);
		$tpl->set('image_path', $_CONFIG['company']['email_image_path']);
		$tpl->set('abn', $_CONFIG['company']['abn']);
		$tpl->set('name', $customer->fullname);
		$tpl->set('email', $customer->email);
		$tpl->set('password', $password);
		$tpl->set('login_link', $_CONFIG['site']['url'].'login.php');

		$text = $tpl->fetch("templates/email_forgot_password.html");
		$html = $tpl->fetch("templates/email_forgot_password.html");
		
		$mail->new_mail($from, $to, $subject, $text, $html);

		return $mail->send();
	}

	function send_signup_email($customer, $password){
		global $_CONFIG;
		$mail = new utils_email();

		$from = array($_CONFIG['company']['contact_email'], $_CONFIG['company']['contact_name']);
		
		$to = $customer->email;

		$subject = "Eyestyle Signup Information";

		$tpl = new html_template();

		$tpl->set('site', $_CONFIG['site']['url']);
		$tpl->set('image_path', $_CONFIG['company']['email_image_path']);
		$tpl->set('abn', $_CONFIG['company']['abn']);
		$tpl->set('name', $customer->fullname);
		$tpl->set('email', $customer->email);
		$tpl->set('username', $customer->email);
		$tpl->set('password', $password);
		$tpl->set('login_link', $_CONFIG['site']['url'].'login.php');

		$text = $tpl->fetch("templates/email_signup.html");
		$html = $tpl->fetch("templates/email_signup.html");
		
		$mail->new_mail($from, $to, $subject, $text, $html);

		return $mail->send();

	}
}
?>