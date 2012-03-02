<?php
$_ACCESS = 'newsletter.newsletter';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));
$newsletter = new dbo('newsletter', $_GET['id']);

$form = html_form::get_form('form_newsletter_newsletter_preview');

if (!$form->validate()) {
	$form->set_failure();
}

$newsletter->name = $form->get('name');
$newsletter->from_address = $form->get('from_address');
$newsletter->from_name = $form->get('from_name');
$newsletter->subject = $form->get('subject');
$newsletter->body = $form->get('body');
$newsletter->update();

html_message::add('Newsletter saved successfully.', 'info');

if ($form->clicked('submit_send')) {
	http::redirect('newsletter_newsletter_preview.php?id='.$newsletter->newsletter_id);
} else {
	http::redirect(http::get_path());
}
?>