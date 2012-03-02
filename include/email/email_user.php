<?php

class email_user {

	function __activation_template($text, $user) {
		global $_CONFIG;
		$text = str_replace('{EMAIL_IMAGE_PATH}', $_CONFIG['site']['email_image_path'], $text);
		$text = str_replace('{EMAIL_LINK}', $_CONFIG['site']['email_link'], $text);
		$text = str_replace('{ABN}', $_CONFIG['company']['abn'], $text);
		$text = str_replace('{USER_FULLNAME}', $user->fullname, $text);
		$text = str_replace('{ACTIVATION_LINK}', $_CONFIG['site']['email_link'].$_CONFIG['user']['activation_link'].'?code='.$user->activation_code.'&PHPSESSID='.session_id(), $text);
		$text = str_replace('{ACTIVATION_PAGE}', $_CONFIG['site']['email_link'].$_CONFIG['user']['activation_page'], $text);
		$text = str_replace('{ACTIVATION_CODE}', $user->activation_code, $text);

		return $text;
	}

	function activation($user) {
		global $_CONFIG;

		$mail = new utils_email();
		$from = array($_CONFIG['user']['activation_email_from_address'], $_CONFIG['user']['activation_email_from_name']);
		$to = $user->email;

		$p = new dbo('preference', 'email_activation_subject');
		$subject = email_user::__activation_template($p->value, $user);

		$p = new dbo('preference', 'email_activation_text');
		$text = str_replace("\n", "\r\n", email_user::__activation_template($p->value, $user));

		$p = new dbo('preference', 'email_activation_html');
		$html = email_user::__activation_template($p->value, $user);

		$mail->new_mail($from, $to, $subject, $text, $html);

		return $mail->send();
	}

	function __forgot_password_template($text, $user, $password) {
		global $_CONFIG;
		$text = str_replace('{EMAIL_IMAGE_PATH}', $_CONFIG['site']['email_image_path'], $text);
		$text = str_replace('{EMAIL_LINK}', $_CONFIG['site']['email_link'], $text);
		$text = str_replace('{ABN}', $_CONFIG['company']['abn'], $text);
		$text = str_replace('{USER_FULLNAME}', $user->fullname, $text);
		$text = str_replace('{USER_USERNAME}', $user->username, $text);
		$text = str_replace('{USER_PASSWORD}', $password, $text);
		$text = str_replace('{LOGIN_LINK}', $_CONFIG['site']['email_link'].'login.php', $text);
		
		return $text;
	}

	function forgot_password($user, $password) {
		global $_CONFIG;

		$mail = new utils_email();
		$from = array($_CONFIG['user']['forgot_password_email_from_address'], $_CONFIG['user']['forgot_password_email_from_name']);
		$to = $user->email;

		$p = new dbo('preference', 'email_forgot_password_subject');
		$subject = email_user::__forgot_password_template($p->value, $user, $password);

		$p = new dbo('preference', 'email_forgot_password_text');
		$text = str_replace("\n", "\r\n", email_user::__forgot_password_template($p->value, $user, $password));

		$p = new dbo('preference', 'email_forgot_password_html');
		$html = email_user::__forgot_password_template($p->value, $user, $password);

		$mail->new_mail($from, $to, $subject, $text, $html);

		return $mail->send();
	}

}
?>