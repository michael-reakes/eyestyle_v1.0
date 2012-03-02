<?
	require_once('inc.php');

	$form = new html_form('form_subscribe','action_newsletter_subscribe.php');
	$form->add(new html_form_text('email',true,'','','',50));
	$form->add(new html_form_image_button('btn_signup','images/btn/signup.gif','Sign Up Now','no_border'));
	$form->register();

	require('inc_header.php');
?>
	
	
		<div id="content">
		<div class="breadcrumb"><ul><li><a href="./">home</a></li> <li>/</li> <li class="selected">subscribe to newsletter</li></ul></div>
			<!--<div class="breadcrumb">
				<ul>
					<li><a href="./">Home</a></li>
					<li>/</li>
					<li class="selected"><a href="./">Newsletter Subscription</a></li>
				</ul>
			</div> -->
			<fieldset>
			
			<?=html_message::show()?>
			<?=$form->output_open()?>

			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<label for="email">Please enter your email address:</label>
						<?=$form->output('email')?>
					</td>
				</tr>
			</table>
			<button type="submit" value="Sign up now">Sign up now</button>
			
			<?=$form->output_close()?>
			
			</fieldset>
		</div>
	
	
	
	
	
	
	<?php require('inc_footer.php'); ?>