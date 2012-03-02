<?
	require_once('inc.php');

	$form = new html_form('form_subscribe','action_newsletter_subscribe.php');
	$form->add(new html_form_text('email',true,'','','',50));
	$form->add(new html_form_image_button('btn_signup','images/btn/signup.gif','Sign Up Now','no_border'));
	$form->register();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="Felix Tjandrawibawa (S3Group.com.au)" />
<meta name="description" content="Eyestyle, an Australian based online eyewears and sunglasses store."></meta>
<title>Subscribe to newsletter</title>
<link href="css/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="css/index.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="js/main.js"></script>
<script language="JavaScript" type="text/javascript" src="js/mm_menu.js"></script>

<body style="background:#fff none">
	<div style="height:20px;background-color:#808080;color:#fff;vertical-align:middle;padding-left:20px;padding-top:10px;padding-bottom:5px"><img src="images/layout/title_subscribe.gif" alt="subscribe" /></div>
	<div style="padding-top:30px;padding-left:20px">
		<?=html_message::show()?>
		<?=$form->output_open()?>
		Please enter your email address:<br />
		<?=$form->output('email')?><br /><br />
		<div style="margin-left:-2px"><?=$form->output('btn_signup')?></div>
		<?=$form->output_close()?><br />
		Click <a href="javascript:window.close()">here</a> to close this window.
	</div>

</body>
</html>