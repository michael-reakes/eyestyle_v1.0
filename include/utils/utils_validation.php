<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Validation functions
 *
 * @package utils
 */
class utils_validation {

	/**
	 * Validate an email address
	 *
	 * @static
	 *
	 * @param string $email Email address
	 * @return boolean
	 */
	function email($email){
		$email = strtolower($email);
		if(ereg("^([^[:space:]]+)@(.+)\.(ad|ae|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|cr|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|fi|fj|fk|fm|fo|fr|fx|ga|gb|gov|gd|ge|gf|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nato|nc|ne|net|nf|ng|ni|nl|no|np|nr|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$",$email)){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Validate a credit card number
	 *
	 * @static
	 *
	 * @param string $number Credit card number
	 * @param string $type Credit card type. visa|master|amex|diners|discover|jcb|bankcard
	 * @param int $expiry_m Expiry month. 1-12
	 * @param int $expiry_y Expiry year. In YYYY format.
	 * @return mixed
	 */
	function cc($number, $type, $expiry_m, $expiry_y) {
		$cc_number = ereg_replace('[^0-9]', '', $number);

		if (ereg('^4[0-9]{12}([0-9]{3})?$', $cc_number)) {
			$cc_type = 'visa';
		} elseif (ereg('^5[1-5][0-9]{14}$', $cc_number)) {
			$cc_type = 'master';
		} elseif (ereg('^3[47][0-9]{13}$', $cc_number)) {
			$cc_type = 'amex';
		} elseif (ereg('^3(0[0-5]|[68][0-9])[0-9]{11}$', $cc_number)) {
			$cc_type = 'diners';
		} elseif (ereg('^6011[0-9]{12}$', $cc_number)) {
			$cc_type = 'discover';
		} elseif (ereg('^(3[0-9]{4}|2131|1800)[0-9]{11}$', $cc_number)) {
			$cc_type = 'jcb';
		} elseif (ereg('^5610[0-9]{12}$', $cc_number)) {
			$cc_type = 'bankcard';
		} else {
			return -1; // invalid card number
		}

		if ($cc_type != $type) {
			return -2; // incorrect card type
		}

		if (is_numeric($expiry_m) && ($expiry_m > 0) && ($expiry_m < 13)) {
			$cc_expiry_month = $expiry_m;
		} else {
			return -3; // incorrect expiry date
		}

		$current_year = date('Y');
		if (is_numeric($expiry_y) && ($expiry_y >= $current_year) && ($expiry_y <= ($current_year + 10))) {
			$cc_expiry_year = $expiry_y;
		} else {
			return -3; // incorrect expiry date
		}

		if ($expiry_y == $current_year) {
			if ($expiry_m < date('n')) {
				return -3; // incorrect expiry date
			}
		}

		$card_number = strrev($cc_number);
		$num_sum = 0;

		for ($i=0; $i<strlen($card_number); $i++) {
			$current_num = substr($card_number, $i, 1);

			if ($i % 2 == 1) {
				$current_num *= 2;
			}

			if ($current_num > 9) {
				$first_num = $current_num % 10;
				$second_num = ($current_num - $first_num) / 10;
				$current_num = $first_num + $second_num;
			}

			$num_sum += $current_num;
		}

		if ($num_sum % 10 == 0) {
			return true; // valid cc information
		} else {
			return -1; // invalid card number;
		}
	}

	/**
	 * Return the error message for an error code returned from cc() function
	 *
	 * @static
	 *
	 * @param int $error_code Error code
	 * @return string
	 */
	function cc_error($error_code) {
		switch ($error_code) {
			case -1:
				return 'Invalid credit card number.';
			case -2:
				return 'Incorrect credit card type.';
			case -3:
				return 'Incorrect expiry month / year.';
			default:
				return '';
		}
	}
}
?>