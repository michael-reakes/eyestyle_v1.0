<?php
$_ACCESS = 'public';

require_once('inc.php');

$status = 'Success';
$error = '';

if(!isset($_GET['id']) || !isset($_GET['fullname']) || !isset($_GET['email'])) {
	$status = 'Failure';
	$error = 'Invalid request';
} else {
	$newsletter = new dbo('newsletter', $_GET['id']);
	$mail = new utils_email();
	$from = array($newsletter->from_address, $newsletter->from_name);
	if ($_GET['fullname'] == $_GET['email']) {
		$to = $_GET['email'];
	} else {
		//$to = array($_GET['email'], $_GET['fullname']);
		$to = ($_GET['email']);
	}
	$subject = str_replace('{CUSTOMER}', $_GET['fullname'], $newsletter->subject);

	$body = str_replace('{CUSTOMER}', $_GET['fullname'], $newsletter->body);
	$body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html><meta name="Keywords" content="" /><meta name="Description" content="" /><body>'.$body;

	$body = $body.'</body></html>';

	//put link here
	$body = str_replace('{LINK}','<a href="http://www.eyestyle.com.au/newsletter/index.php?id='.$newsletter->newsletter_id.'">click here</a>',$body);

	$mail->new_mail($from, $to, $subject, '', $body);

	/* For testing uncomment the if below and comment the real if */
	//failing email starting with letter f
	//if ($to{0} == 'f'){
	if (!$mail->send()) {
		$status = 'Failure';
		$error = 'Error';
		//saving the error email on the session
		$email_array = array();
		if (isset($_SESSION['resend_emails'])){
			$email_array = $_SESSION['resend_emails'];
		}
		$email_array[] = $to;
		$_SESSION['resend_emails'] = $email_array;
	}

}
header('Content-Type: text/xml');
print "<?xml version=\"1.0\" ?>\r\n";
print "<response>\r\n";
print '<status>'.$status."</status>\r\n";
if ($status=='Failure') {
	print '<error>'.$error."</error>\r\n";
}
print '</response>';
?>