<?php
$_REQUIRE_SSL = true;
require_once('inc.php');

http::register_path();

customer::check_login();

$_PAGE = 'my details';

$state_options = customer::state_list();

$country_list = new dbo_list('country','','zone_id','ASC');
$country_options = array();
foreach($country_list->get_all() as $country){
	$country_options[$country->code] = $country->name;
}

if ($_CUSTOMER->country == "") {
	$customer_country = "AU";
}
else {
	$customer_country = $_CUSTOMER->country;
}


$form = new html_form('form_customer_account_details','action_customer_account_details.php');
$form->add(new html_form_text('fullname',true,$_CUSTOMER->fullname,'full',false,48,200));
$form->add(new html_form_text('company_name',false,$_CUSTOMER->company,'full',false,48,200));
$form->add(new html_form_text('phone',true,$_CUSTOMER->phone,'full',false,48,200));
$form->add(new html_form_text('email',true,$_CUSTOMER->email,'full',false,48,200));
$form->add(new html_form_text('mobile',false,$_CUSTOMER->mobile,'full',false,48,200));
$form->add(new html_form_text('address',true,$_CUSTOMER->address,'full',false,115,200));
$form->add(new html_form_text('suburb',true,$_CUSTOMER->suburb,'full',false,48,200));
$form->add(new html_form_text('postcode',true,$_CUSTOMER->postcode,'full',false,10,8));
$form->add(new html_form_text('state',true,$_CUSTOMER->state,'full',false,48,200));
$form->add(new html_form_select('country',$country_options,'',true,false,'',$customer_country));


$form->add(new html_form_checkbox('subscribe','true','checkbox'));

$form->add(new html_form_image_button('btn_submit','images/btn/submit.gif','Update Changes','no_border'));
$form->register();

$fields = array('Customer Name'=>'fullname',
				'Company'=>'company_name',
				'Phone'=>'phone',
				'Mobile'=>'mobile',
				'Email'=>'email',
				'Address'=>'address',
				'Suburb'=>'suburb',
				'State'=>'state',
				'Country'=>'country',
				'Postcode'=>'postcode');

$breadcrumb = array('home'=>'./','my account'=>'customer_account_details',$_PAGE=>'');

require_once('inc_header.php');

?>
		<div id="content" class="clearfix">
			<?php require_once('inc_customer_lcolumn.php'); ?>
			<div class="rcolumn">
				<?=html::breadcrumb($breadcrumb)?>
				<h5>Customer Account Details</h5>
				<?=html_message::show()?>
				<p><span class="required">*</span> indicates mandatory fields</p>
				<fieldset>
				<?=$form->output_open()?>
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td width="300">
							<label for="name">Full Name: <span class="required">*</span></label>
							<?=$form->output('fullname')?>
						</td>
						<td width="300">
							<label for="company">Company Name <span class="required">*</span></label>
							<?=$form->output('company_name')?>
						</td>
					</tr>
					<tr>
						<td>
							<label for="phone">Phone: <span class="required">*</span></label>
							<?=$form->output('phone')?>
						</td>
						<td>
							<label for="mobile">Mobile: <span class="required">*</span></label>
							<?=$form->output('mobile')?>
						</td>
					</tr>
					<tr>
						<td>
							<label for="email">Email: <span class="required">*</span></label>
							<?=$form->output('email')?>
						</td>
						<td>&nbsp;</td>
					</tr>				
					<tr>
						<td colspan="2">
							<label for="address">Address: <span class="required">*</span></label>
							<?=$form->output('address')?>
						</td>
					</tr>
					<tr>
						<td>
							<label for="suburb">Suburb: <span class="required">*</span></label>
							<?=$form->output('suburb')?>
						</td>
						<td>
							<label for="postcode">Postcode: <span class="required">*</span></label>
							<?=$form->output('postcode')?>
						</td>
					</tr>
					<tr>
						<td>
							<label for="state">State: <span class="required">*</span></label>
							<?=$form->output('state')?>
						</td>
						<td>
							<label for="country">Country: <span class="required">*</span></label>
							<?=$form->output('country')?>
						</td>					
					</tr>
				</table>
				<p class="optiongroup"><?=$form->output('subscribe')?> <label for="subscribe">I would like to receive newsletter from EYESTYLE.COM.AU</label></p>
				<button type="submit" value="Update my details">Update my details</button>
				<?=$form->output_close()?>	
				</fieldset>
			</div>
		</div>
	
<?php require_once('inc_footer.php') ?>