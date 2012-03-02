<?
$_REQUIRE_SSL = true;
require_once('inc.php');
$_PAGE = 'sign up';

$country_options = customer::country_list();

$form = new html_form('form_customer_account_details','action_signup.php');
$form->add(new html_form_text('fullname',true,'','',false,60,200));
$form->add(new html_form_text('company_name',false,'','',false,60,200));
$form->add(new html_form_password('password',true,'','',false,60,200));
$form->add(new html_form_password('password_confirm',true,'','',false,60,200));
$form->add(new html_form_text('phone',true,'','',false,60,200));
$form->add(new html_form_text('email',true,'','',false,60,200));
$form->add(new html_form_text('mobile',false,'','',false,60,200));
$form->add(new html_form_text('address',true,'','',false,120,200));
$form->add(new html_form_text('suburb',true,'','',false,60,200));
$form->add(new html_form_text('postcode',true,'','',false,10,8));
$form->add(new html_form_text('state',true,'','',false,false,60,200));
$form->add(new html_form_select('country',$country_options,'',true,false,'',''));
$form->add(new html_form_checkbox('subscribe','true','checkbox'));
$form->add(new html_form_image_button('btn_submit','images/btn/signup_2.gif','Sign Up Now','no_border'));
$form->register();

$fields = array(
				'Full Name'=>'fullname',
				'Company Name'=>'company_name',
				'Password'=>'password',
				'Confirm Password'=>'password_confirm',
				'Phone'=>'phone',
				'Mobile'=>'mobile',
				'Email'=>'email',
				'Address'=>'address',
				'Suburb'=>'suburb',
				'State'=>'state',
				'Country'=>'country',
				'Postcode'=>'postcode'
				);

$breadcrumb = array('home'=>'./',$_PAGE=>'');

require_once('inc_header.php')

?>

		<div id="content">
			<?=html::breadcrumb($breadcrumb)?>
			<h5>New Customer Details</h5>
			<?=html_message::show()?>
			<p><span class="required">*</span> indicates mandatory fields</p>
			<fieldset>
			<?=$form->output_open()?>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="350">
						<label for="name">Full Name: <span class="required">*</span></label>
						<?=$form->output('fullname');?>
					</td>
					<td width="350">
						<label for="company">Company Name <span class="required">*</span></label>
						<?=$form->output('company_name');?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="password">Password: <span class="required">*</span></label>
						<?=$form->output('password');?>
					</td>
					<td>
						<label for="password_confirm">Confirm Password: <span class="required">*</span></label>
						<?=$form->output('password_confirm');?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="phone">Phone: <span class="required">*</span></label>
						<?=$form->output('phone');?>
					</td>
					<td>
						<label for="mobile">Mobile: <span class="required">*</span></label>
						<?=$form->output('mobile');?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="email">Email: <span class="required">*</span></label>
						<?=$form->output('email');?>
					</td>
					<td>&nbsp;</td>
				</tr>				
				<tr>
					<td colspan="2">
						<label for="address">Address: <span class="required">*</span></label>
						<?=$form->output('address');?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="suburb">Suburb: <span class="required">*</span></label>
						<?=$form->output('suburb');?>
					</td>
					<td>
						<label for="postcode">Postcode: <span class="required">*</span></label>
						<?=$form->output('postcode');?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="state">State: <span class="required">*</span></label>
						<?=$form->output('state');?>
					</td>
					<td>
						<label for="country">Country: <span class="required">*</span></label>
						<?=$form->output('country');?>
					</td>					
				</tr>
			</table>
			<p class="optiongroup"><?=$form->output('subscribe')?> <label for="subscribe">I would like to receive newsletter from EYESTYLE.COM.AU</label></p>
			<button type="submit" value="Sign up now">Sign up now</button>
			
			<?=$form->output_close()?>
			</fieldset>
		</div>
	<?php require_once('inc_footer.php'); ?>