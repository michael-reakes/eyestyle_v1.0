<?php
$_ACCESS = 'newsletter.newsletter';

require_once('inc.php');

$form = html_form::get_form('form_newsletter_newsletter_add');

if (!$form->validate()) {
	$form->set_failure();
}

$newsletter = new dbo('newsletter');
$newsletter->name = $form->get('name');
$newsletter->from_address = $form->get('from_address');
$newsletter->from_name = $form->get('from_name');
$newsletter->subject = $form->get('subject');
$newsletter->body = $form->get('body');
$newsletter->date_created = utils_time::db_datetime();
$newsletter->insert();

//save newsletter to the directory ../newsletter/newsletterid_date.html
/*
$filename = '../newsletter/'.$newsletter->newsletter_id.'.html';
$handle = fopen($filename, 'w+');
if (fwrite($handle, $newsletter->body) === FALSE) {
	echo "Cannot write to file ($filename)";
	html_message::add('Newsletter cannot be saved to '.$filename);
}else{
	$filename = 'newsletter/'.$newsletter->newsletter_id.'.html';
	$newsletter->path = $filename;
	$newsletter->update();
	html_message::add('Newsletter saved successfully.', 'info');
}
fclose($handle);
*/

html_message::add('Newsletter added successfully.', 'info');
http::redirect(http::get_path());
?>