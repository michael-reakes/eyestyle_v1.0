<?php
$_REQUIRE_SSL = true;
require_once('inc.php');

http::register_path();

//what is check_login?
customer::check_login();

$_PAGE = 'change password';

$form = new html_form('form_customer_password','action_customer_password.php');
$form->add(new html_form_password('old_password',true,'','full',false,60,200));
$form->add(new html_form_password('new_password',true,'','full',false,60,200));
$form->add(new html_form_password('confirm_password',true,'','full',false,60,200));
$form->add(new html_form_image_button('btn_submit','images/btn/submit.gif','Update Changes','no_border'));
$form->register();

$breadcrumb = array('home'=>'./','my account'=>'customer_account_details.php',$_PAGE=>'');

require_once('inc_header.php');

?>	
		<div id="content" class="clearfix">
			<?php require_once('inc_customer_lcolumn.php'); ?>
			<div class="rcolumn">
				<?=html::breadcrumb($breadcrumb)?>
				<h5>Change your password</h5>
				<?=html_message::show()?>
				<p><span class="required">*</span> indicates mandatory fields</p>
				<fieldset>
				<?=$form->output_open()?>
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td>
							<label for="password">Current password<span class="required">*</span></label>
							<?=$form->output('old_password')?>
						</td>
					</tr>
					<tr>
						<td>
							<label for="password_new">New password<span class="required">*</span></label>
							<?=$form->output('new_password')?>
						</td>
					</tr>
					<tr>
						<td>
							<label for="password_confirm">Confirm your new password<span class="required">*</span></label>
							<?=$form->output('confirm_password')?>
						</td>
					</tr>
				</table>
				<button type="submit" value="Change my password">Change my password</button>
				<?=$form->output_close()?>
				</fieldset>
			</div>
		</div>
<?php require_once('inc_footer.php') ?>