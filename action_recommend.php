<?php
require_once('inc.php');

$form = html_form::get_form('form_recommend');

if (!$form->validate()) {
	$form->set_failure();
}

$mail = new utils_email();
$from = $form->get('email');
$subject = $form->get('name').' has invited you to learn more about EYESTYLE';

for ($i=1; $i<=3; $i++) {
	$email = 'email'.$i;
	$fail_arr = array();
	if ($form->get($email) != '') {
		$to = $form->get($email);
		
		$tpl = new html_template();
		$tpl->set('image_path', $_CONFIG['company']['email_image_path'].'email/');
		$tpl->set('name', $form->get('name'));
		$tpl->set('from', $from);
		$tpl->set('email', $to);
		$tpl->set('message', str_replace("\r\n", "<br />", $form->get('message')));
		
		$text = $tpl->fetch("email_recommend.txt");
		$html = $tpl->fetch("email_recommend.html");
		
		$mail->new_mail($from, $to, $subject, $text, $html);

		if (!$mail->send()) {
			$fail_arr[] = $to;
		}
	}
}

if (count($fail_arr) == 0) {
	html_message::add("Thank you for your recommendation. Your recipients will receive your invitation shortly.",'info');
	http::redirect(http::get_path());
} else {
	$err_msg = "Sorry, the invitations could not be sent to the following recipients:<ul>";
	foreach($fail_arr as $email) {
		$err_msg .= '<li>'.$email.'</li>';
	}
	$err_msg .= '</ul>The server might be down or busy at the moment. Please try again later.';
	$form->set_failure($err_msg);
}


?>