<?php
	$_PAGE = 'forgot password';
	require_once('inc.php');

	http::register_path();

	$form_password = new html_form('form_forgot_password',http::url('action_forgot_password.php'));
	$form_password->add(new html_form_text('email',true,'','full',false,60,200));
	$form_password->add(new html_form_image_button('btn_submit','images/btn/submit.gif','Submit','no_border'));
	$form_password->set_validator('email',array('utils_validation','email'),'Please enter a valid email');
	$form_password->register();

	$breadcrumb = array('home'=>'./',$_PAGE=>'');
	require_once('inc_header.php');
?>
		<div id="content">
			<?=html::breadcrumb($breadcrumb)?>
				
			<fieldset>
			<?=html_message::show()?>
			<?=$form_password->output_open()?>
			<p>If you have forgotten your login password, please enter the email that you used to register at eyestyle.com.au below, and we will send you a new password to your email.</p>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<label for="email">Email:</label>
						<?=$form_password->output('email')?>
					</td>
				</tr>
			</table>
			<button type="submit" value="Submit">Submit</button>
			<?=$form_password->output_close()?>
			</fieldset>
			</div>
		</div>
<?php require_once('inc_footer.php'); ?>
