<?
	require_once('inc.php');

	$form = html_form::get_form('form_subscribe');
	$email = $form->get('email');

	$subscriber_list = new dbo_list('subscriber','WHERE email = "'.$email.'"');

	if (!utils_validation::email($email)){
		html_message::add('Please enter a valid email address.');
	}else if ($subscriber_list->count() > 0){
		html_message::add('The email address is already registered to EYESTYLE.');
	}else{
		$member = new dbo('subscriber');
		$member->email = $email;	
		$member->date_created = utils_time::db_datetime();
		$member->status = "active";
		$member->insert();
		html_message::add('You are now subscribed to EYESTYLE newsletter.','info');
	}

	http::redirect('newsletter_subscribe.php');
?>
