<?php

class email_checkout {

	function confirmation($user, $order) {
		global $_CONFIG;

		$mail = new utils_email();
		$from = array($_CONFIG['checkout']['confirmation_email_from_address'], $_CONFIG['checkout']['confirmation_email_from_name']);
		$to = $user->email;

		$p = new dbo('preference', 'email_confirmation_subject');
		$subject = email_checkout::template($p->value, $user, $order);

		$p = new dbo('preference', 'email_confirmation_text');
		$text = str_replace("\n", "\r\n", email_checkout::template($p->value, $user, $order));

		$p = new dbo('preference', 'email_confirmation_html');
		$html = email_checkout::template($p->value, $user, $order, true);

		$mail->new_mail($from, $to, $subject, $text, $html);

		return $mail->send();
	}

	function invoice($user, $order) {
		global $_CONFIG;

		$mail = new utils_email();
		$from = array($_CONFIG['checkout']['invoice_email_from_address'], $_CONFIG['checkout']['invoice_email_from_name']);
		$to = $user->email;

		$p = new dbo('preference', 'email_invoice_subject');
		$subject = email_checkout::template($p->value, $user, $order);

		$p = new dbo('preference', 'email_invoice_text');
		$text = str_replace("\n", "\r\n", email_checkout::template($p->value, $user, $order));

		$p = new dbo('preference', 'email_invoice_html');
		$html = email_checkout::template($p->value, $user, $order, true);

		$mail->new_mail($from, $to, $subject, $text, $html);

		return $mail->send();
	}

	function notification($user, $order) {
		global $_CONFIG;

		$email = new dbo('preference', 'email_notification_to');

		$mail = new utils_email();
		$from = array($email->value, 'Techmate Order Notification');
		$to = $email->value;

		$p = new dbo('preference', 'email_notification_html');
		$html = email_checkout::template($p->value, $user, $order, true);

		$mail->new_mail($from, $to, 'Techmate Order Notification', '', $html);

		return $mail->send();
	}

	function template($text, $user, $order, $html=false, $show_print=false) {
		global $_CONFIG;
		$order_item_list = $order->load_children('order_item');

		$text = str_replace('{IMG_PATH}', $_CONFIG['site']['email_image_path'], $text);
		$text = str_replace('{ABN}', $_CONFIG['company']['abn'], $text);
		$text = str_replace('{ORDER_ID}', $order->order_id, $text);
		$text = str_replace('{DATE_CREATED}', date('j M Y', utils_time::timestamp($order->date_created)), $text);
		if ($order->date_paid != '0000-00-00') {
			$text = str_replace('{DATE_PAID}', date('j M Y', utils_time::timestamp($order->date_paid)), $text);
		}
		$text = str_replace('{USER_ID}', $user->user_id, $text);
		$text = str_replace('{EMAIL}', $user->email, $text);
		$text = str_replace('{BILLING_FULLNAME}', $order->billing_fullname, $text);
		$text = str_replace('{BILLING_ADDRESS}', $order->billing_address, $text);
		$text = str_replace('{BILLING_SUBURB}', ucwords(strtolower($order->billing_suburb)), $text);
		$text = str_replace('{BILLING_STATE}', $order->billing_state, $text);
		$text = str_replace('{BILLING_POSTCODE}', $order->billing_postcode, $text);
		$text = str_replace('{BILLING_PHONE}', $order->billing_phone, $text);
		$text = str_replace('{DELIVERY_FULLNAME}', $order->delivery_fullname, $text);
		$text = str_replace('{DELIVERY_ADDRESS}', $order->delivery_address, $text);
		$text = str_replace('{DELIVERY_SUBURB}', ucwords(strtolower($order->delivery_suburb)), $text);
		$text = str_replace('{DELIVERY_STATE}', $order->delivery_state, $text);
		$text = str_replace('{DELIVERY_POSTCODE}', $order->delivery_postcode, $text);
		$text = str_replace('{DELIVERY_PHONE}', $order->delivery_phone, $text);
		$text = str_replace('{DELIVERY}', html_text::currency($order->subtotal_delivery), $text);
		$text = str_replace('{DELIVERY_GST}', html_text::currency($order->subtotal_delivery/11), $text);
		$text = str_replace('{SURCHARGE}', html_text::currency($order->subtotal_surcharge), $text);
		$text = str_replace('{SURCHARGE_GST}', html_text::currency($order->subtotal_surcharge/11), $text);
		$text = str_replace('{SUBTOTAL}', html_text::currency($order->total*10/11), $text);
		$text = str_replace('{TOTAL}', html_text::currency($order->total), $text);
		$text = str_replace('{TOTAL_GST}', html_text::currency($order->total/11), $text);
		$text = str_replace('{COMPANY_DD_BANK}', $_CONFIG['company']['dd_bank'], $text);
		$text = str_replace('{COMPANY_DD_BSB}', $_CONFIG['company']['dd_bsb'], $text);
		$text = str_replace('{COMPANY_DD_AC_NAME}', $_CONFIG['company']['dd_ac_name'], $text);
		$text = str_replace('{COMPANY_DD_AC_NO}', $_CONFIG['company']['dd_ac_no'], $text);
		$text = str_replace('{COMPANY_FAX}', $_CONFIG['company']['fax'], $text);
		$text = str_replace('{COMPANY_EMAIL}', $_CONFIG['company']['email'], $text);
		$text = str_replace('{COMPANY_MAIL_ADDRESS1}', $_CONFIG['company']['mail_address1'], $text);
		$text = str_replace('{COMPANY_MAIL_ADDRESS2}', $_CONFIG['company']['mail_address2'], $text);
		$text = str_replace('{COMPANY_MAIL_ADDRESS3}', $_CONFIG['company']['mail_address3'], $text);
		$text = str_replace('{STATUS}', strtoupper($order->status), $text);

		$prod_str = '';

		if ($html) {
			foreach($order_item_list as $order_item) {
				$product = new dbo('product',$order_item->product_id);
				$prod_str .= '<tr style="background-color:#fff;">';
				$prod_str .= '<td>'.$product->name.'</td>';
				$prod_str .= '<td style="text-align:center">'.$order_item->qty.'</td>';
				$prod_str .= '<td style="text-align:right">'.html_text::currency($order_item->unit_price).'</td>';
				$prod_str .= '<td style="text-align:right">'.html_text::currency($order_item->unit_price * $order_item->qty/ 11).'</td>';
				$prod_str .= '<td style="text-align:right">'.html_text::currency($order_item->unit_price * $order_item->qty).'</td>';
				$prod_str .= '</tr>';
			}
		} else {
			foreach($order_item_list as $order_item) {
				$product = new dbo('product',$order_item->product_id);
				$prod_str .= $order_item->qty." X ".$product->name." [".html_text::currency($product->price)."]\n";
			}
		}

		$text = str_replace('{PURCHASED_PRODUCTS}', $prod_str, $text);

		$prod_codes_str = '';

		foreach($order_item_list as $order_item) {
			$product = new dbo('product',$order_item->product_id);
			$prod_codes_str .= '<tr style="background-color:#fff;">';
			$prod_codes_str .= '<td>'.$product->name.'</td>';
			$prod_codes_str .= '<td>'.$product->code.'</td>';
			$prod_codes_str .= '<td style="text-align:center">'.$order_item->qty.'</td>';
			$prod_codes_str .= '<td style="text-align:right">'.html_text::currency($order_item->unit_price).'</td>';
			$prod_codes_str .= '<td style="text-align:right">'.html_text::currency($order_item->unit_price * $order_item->qty).'</td>';
			$prod_codes_str .= '</tr>';
		}

		$text = str_replace('{PURCHASED_PRODUCTS_CODES}', $prod_codes_str, $text);

		if ($show_print) {
			$text = str_replace('{PRINT}', '<br/><div style="text-align:center;"><input type="button" value="Print" onclick="javascript:window.print();" /></div>', $text);
		} else {
			$text = str_replace('{PRINT}', '', $text);
		}

		return $text;
	}
}
?>