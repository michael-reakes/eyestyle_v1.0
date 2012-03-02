<?
$_REQUIRE_SSL = true;

require_once('inc.php');
$_PAGE = 'checkout';

if (!isset($_GET['step'])) {
	$_CHECKOUT->active = true;
	$step = '1';
} else {
	$step = $_GET['step'];
}

customer::check_login();

http::halt_if($step != '1' && $step != '2' && $step != '3' && $step != '4');

if (!count($_CHECKOUT->cart)) {
	http::redirect('cart.php');
}

$state_options = customer::state_list();
$country_list = new dbo_list('country','','zone_id','ASC');
$country_options = array();
foreach($country_list->get_all() as $country){
	$country_options[$country->code] = $country->name;
}

$secure_site = true;
$form_action = http::url('action_checkout.php?step='.$step.'&'.http::build_query($_GET, 'step'), $secure_site);
$form = new html_form('form_checkout',$form_action);

switch ($step) {
	case '1':
		if (!$_CHECKOUT->is_set_billing_details()){
			//this is the first time the customer comes to the checkout
			$_CHECKOUT->set_billing_from_db();
		}
		//fill the fields with the session's stored values
		$details = array();
		foreach($_CHECKOUT->billing_details as $key=>$value){
			$details[$key] = $value != '' ? $value : (isset($_CUSTOMER->$key) ? $_CUSTOMER->$key : '');
		}
		
		$form->add(new html_form_text('Bfullname',true,$details['billing_name'],'full',false,60,200));
		$form->add(new html_form_text('Baddress',true,$details['billing_address'],'full',false,120,200));
		$form->add(new html_form_text('Bsuburb',true,$details['billing_suburb'],'full',false,60,200));
		$form->add(new html_form_text('Bpostcode',true,$details['billing_postcode'],'',false,10,8));
		$form->add(new html_form_text('Bstate',true,$details['billing_state'],'full',false,60,200));
		$form->add(new html_form_select('Bcountry',$country_options,'',true,false,'',$details['billing_country']));
		$form->add(new html_form_text('Bphone',true,$details['billing_phone'],'full',false,60,200));
		$form->add(new html_form_text('Bemail',true,$details['billing_email'],'full',false,60,200));
		$form->set_validator('Bemail',array('utils_validation','email'), 'Please enter a valid email');
		$form->add(new html_form_image_button('btn_submit','images/btn/next.gif','Next','no_border'));
		break;
	case '2':
		//////// aus only check by victor
		$ausOnly = false;
		foreach ($_CHECKOUT->cart as $key=>$value){ 
			
			$lens = new dbo('lens',$key);
			$colour = new dbo('colour',$lens->colour_id);
			$product = new dbo('product',$colour->product_id);
			
			if ($product->aus_only) {
				$country_options = array('AU' => 'Australia');
				$ausOnly = true;
				break;
			}
		}		
		////////////////////////////

		//fill the fields with the session's stored values
		$details = array();
		foreach(array_keys($_CHECKOUT->billing_details) as $key){
			$key = str_replace('billing_', 'delivery_', $key);
			$details[$key] = isset($_CHECKOUT->delivery_details[$key]) ? $_CHECKOUT->delivery_details[$key] : '';
		}

		$form->add(new html_form_select('Dsame_as_billing',array('No','Yes'),'',!$ausOnly,false,'',$_CHECKOUT->same_to_billing ? 1 : 0 ,'javascript:toggleVisibility(\'deliveryDetails\');'));
		$form->add(new html_form_text('Dfullname',true,$details['delivery_name'],'full',false,60,200));
		$form->add(new html_form_text('Daddress',true,$details['delivery_address'],'full',false,120,200));
		$form->add(new html_form_text('Dsuburb',true,$details['delivery_suburb'],'full',false,60,200));
		$form->add(new html_form_text('Dpostcode',true,$details['delivery_postcode'],'',false,10,8));
		$form->add(new html_form_text('Dstate',true,$details['delivery_state'],'full',false,60,200));
		$form->add(new html_form_select('Dcountry',$country_options,'',true,false,'',$details['delivery_country']));
		$form->add(new html_form_text('Dphone',false,$details['delivery_phone'],'full',false,60,200));
		$form->add(new html_form_text('Demail',true,$details['delivery_email'],'full',false,60,200));
		$form->set_validator('Demail',array('utils_validation','email'), 'Please enter a valid email');
		$form->add(new html_form_image_button('btn_submit','images/btn/next.gif','Next','no_border'));
		break;
	case '3':
		$form->add(new html_form_textarea('comment',true,$_CHECKOUT->comment,'full',40,4));
		$form->add(new html_form_image_button('btn_submit','images/btn/next.gif','Next','no_border'));
		break;
	case '4':
		$cc_type_options = customer::cc_type_list($_CHECKOUT->total());
		$month_options = customer::month_list();
		$year_options = customer::year_list();

		$australiaOnly = $_CHECKOUT->delivery_details['delivery_country'] == 'AU';
			
		$form->add(new html_form_radio('payment_method','cc','radio',$australiaOnly,"javascript:showCCDetails(true);"));
		$form->add(new html_form_radio('payment_method','dd','radio',false,"javascript:showCCDetails(false);"));
		$form->add(new html_form_radio('payment_method','mo','radio',false,"javascript:showCCDetails(false);"));
		$form->add(new html_form_radio('payment_method','pp','radio',!$australiaOnly,"javascript:showPayPal(true);"));
		
		$form->add(new html_form_select("cc_type", $cc_type_options));
		$form->add(new html_form_text('cc_no',false,'','full'));
		$form->add(new html_form_text('cc_name',false,'','full'));
		$form->add(new html_form_text('ccv_no',false,'','',false, 4,4));
		$form->add(new html_form_select('cc_exp_year',$year_options,'-- Year --'));
		$form->add(new html_form_select('cc_exp_month',$month_options,'-- Month --'));
		$form->add(new html_form_image_button('btn_submit','images/btn/place_order_now.gif','Place Order Now','no_border'));
		$form->add(new html_form_image_button('btn_paypal','images/btn/paypal_checkout.gif','Place Order with PayPal Now','no_border'));

		$fields = array('Card Type'=>'cc_type',
				'Card Number'=>'cc_no',
				'Card Holder Name'=>'cc_name',
				'CCV'=>'ccv_no');
		break;
	default:
		break;
}

$form->register();

$breadcrumb = array('home'=>'./',$_PAGE=>'');

?>
<?php require_once('inc_header.php') ?>
	<div id="content">
		<?=html::breadcrumb($breadcrumb)?>
		<?=html_message::show()?>
		<?=$form->output_open()?>
		<ul class="checkoutsteps">
			<?php if ($step == '1') { ?>
			<li class="selected">1. Billing Details</li>
			<?php } elseif ($step == '2' || $step == '3' || $step == '4') { ?>
			<li><a href="<?=http::url('checkout.php?step=1',true)?>">1. Billing Details</a></li>
			<?php } elseif ($step == '5') { ?>
			<li>1. Billing Details</li>
			<?php } ?>
			<li>&gt;</li>
			<?php if ($step == '1' || $step == '5') { ?>
			<li>2. Delivery Details</li>
			<?php } elseif ($step == '2') { ?>
			<li class="selected">2. Delivery Details</li>
			<?php } else { ?>
			<li><a href="<?=http::url('checkout.php?step=2',true)?>">2. Delivery Details</a></li>
			<?php } ?>
			<li>&gt;</li>
			<?php if ($step == '3') { ?>
			<li class="selected">3. Order Summary</li>
			<?php } elseif ($step == '1' || $step == '2' || $step == '5') { ?>
			<li>3. Order Summary</li>
			<?php } else { ?>
			<li><a href="<?=http::url('checkout.php?step=3',true)?>">3. Order Summary</a></li>
			<?php } ?>
			<li>&gt;</li>
			<?php if ($step == '4') { ?>
			<li class="selected">4. Payment</li>
			<?php } else { ?>
			<li>4. Payment</li>
			<?php } ?>
			<li>&gt;</li>
			<?php if ($step == '5') { ?>
			<li class="selected">5. Complete!</li>
			<?php } else { ?>
			<li>5. Complete!</li>
			<?php } ?>
		</ul>
		<?php switch($step) {
			case '1':
		?>

		
		<h5>Billing Details</h5>
		<p>All fields are mandatory</p>
		<fieldset>
		<table border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td width="350">
					<label for="name">Full Name: <span class="required">*</span></label>
					<?=$form->output('Bfullname')?>
				</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">
					<label for="address">Address: <span class="required">*</span></label>
					<?=$form->output('Baddress')?>
				</td>
			</tr>
			<tr>
				<td>
					<label for="suburb">Suburb: <span class="required">*</span></label>
					<?=$form->output('Bsuburb')?>
				</td>
				<td>
					<label for="postcode">Postcode: <span class="required">*</span></label>
					<?=$form->output('Bpostcode')?>
				</td>
			</tr>
			<tr>
				<td>
					<label for="state">State: <span class="required">*</span></label>
					<?=$form->output('Bstate')?>
				</td>
				<td>
					<label for="country">Country: <span class="required">*</span></label>
					<?=$form->output('Bcountry')?>
				</td>					
			</tr>
			<tr>
				<td>
					<label for="phone">Phone: <span class="required">*</span></label>
					<?=$form->output('Bphone')?>
				</td>
				<td>
					<label for="email">Email: <span class="required">*</span></label>
					<?=$form->output('Bemail')?>
				</td>
			</tr>
		</table>
		<div class="actionbuttons clearfix">
			<button type="submit" value="Proceed to delivery details" class="fright">Proceed to delivery details</button>
		</div>
		</fieldset>
		
		
		
		<?php
				break;
			case '2':
		?>
		
		
		<h5>Delivery Details</h5>
		<p>All fields are mandatory</p>
		<fieldset>
		
		<?php
		///////  aus billing address check, 
		/////// check case:
		///////  if the product is aus only
		///////     if the billing country is aus, show same as billing details
		///////     else hidden and force user to input
		///////  else show same as billing details
		
		
			if (($_CHECKOUT->billing_details['billing_country'] != "AU") && ($product->aus_only)) {
		?>
			<p>Please provide the Aussie delivery address.</p>
		<?php
			}
			else {
		?>
		<p>Same as billing details? <?=$form->output('Dsame_as_billing')?></p>

		<?php
		
			}
		
		///////  end aus billing address check
		?>
		<div id="deliveryDetails" name="deliveryDetails" style="display:<?=$_CHECKOUT->same_to_billing ? 'none' : 'block'?>">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="350">		
						<label for="name">Full Name: <span class="required">*</span></label>
						<?=$form->output('Dfullname')?>
					</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<label for="address">Address: <span class="required">*</span></label>
						<?=$form->output('Daddress')?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="suburb">Suburb: <span class="required">*</span></label>
						<?=$form->output('Dsuburb')?>
					</td>
					<td>
						<label for="postcode">Postcode: <span class="required">*</span></label>
						<?=$form->output('Dpostcode')?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="state">State: <span class="required">*</span></label>
						<?=$form->output('Dstate')?>
					</td>
					<td>
						<label for="country">Country: <span class="required">*</span></label>
						<?=$form->output('Dcountry')?>
					</td>					
				</tr>
				<tr>
					<td>
						<label for="phone">Phone: <span class="required">*</span></label>
						<?=$form->output('Dphone')?>
					</td>
					<td>
						<label for="email">Email: <span class="required">*</span></label>
						<?=$form->output('Demail')?>
					</td>
				</tr>
			</table>
		</div>
		<div class="actionbuttons clearfix">
			<button type="submit" value="View Order Summary" class="fright">View Order Summary</button>
			<button type="button" value="Back" onclick="javascript:history.back();">Back</button>
		</div>
		</fieldset>
		
		
		
		<?php
				break;
			case '3':
		?>
		
		
		<h5>Order Summary</h5>
			<fieldset class="ordersummary clearfix">
			<div class="addresscol">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="panel grid">
					<tr>
						<th>Billing Details</th>
					</tr>
					<tr>
						<td>
							<? $country = new dbo('country');
								$billing_country = $country->load($_CHECKOUT->billing_details['billing_country']) ? $country->name : '';
							?>
							<p><?=$_CHECKOUT->billing_details['billing_name']?></p>
							<p>
								<?=$_CHECKOUT->billing_details['billing_address']?><br />
								<?=$_CHECKOUT->billing_details['billing_suburb']?>, <?=$_CHECKOUT->billing_details['billing_state']?> <?=$_CHECKOUT->billing_details['billing_postcode']?><br />
								<?=$billing_country?><br />
								Phone: <?=$_CHECKOUT->billing_details['billing_phone']?><br />
								Email: <?=$_CHECKOUT->billing_details['billing_email']?>
							</p>
							<a href="checkout.php?step=1">Edit</a>
						</td>
					</tr>
				</table>
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="panel grid">
					<tr>
						<th>Delivery Details</th>
					</tr>
					<tr>
						<td>
							<? $country = new dbo('country');
								$delivery_country = $country->load($_CHECKOUT->delivery_details['delivery_country']) ? $country->name : '';
							?>
							<p><?=$_CHECKOUT->delivery_details['delivery_name']?></p>
							<p>
								<?=$_CHECKOUT->delivery_details['delivery_address']?><br />
								<?=$_CHECKOUT->delivery_details['delivery_suburb']?>, <?=$_CHECKOUT->delivery_details['delivery_state']?> <?=$_CHECKOUT->delivery_details['delivery_postcode']?><br />
								<?=$delivery_country?><br />
								Phone: <?=$_CHECKOUT->delivery_details['delivery_phone']?><br />
								Email: <?=$_CHECKOUT->delivery_details['delivery_email']?>
							</p>
							<a href="checkout.php?step=2">Edit</a>
						</td>
					</tr>
				</table>
			</div>
			<div class="ordercol">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="grid marginbottom">
					<tr>
						<th colspan="2">Product Name</th>
						<th width="50">Qty</th>
						<th width="100">Subtotal</th>
					</tr>
					<?	$i = 0;
						foreach ($_CHECKOUT->cart as $key=>$value){ 
							if ($i % 2 == 0) $class = "row_ab_a";
							else $class = "row_ab_b";
							$lens = new dbo('lens',$key);
							$colour = new dbo('colour',$lens->colour_id);
							$product = new dbo('product',$colour->product_id);

					?>
					<tr class="<?=$class?>">
						<td width="99"><img src="<?=$_ROOT.getSmallThumbnail($product->image_1)?>" alt="<?=$product->name?>" width="99" height="49" /></td>
						<td>
							<h6><?=$product->name?></h6>
							<?=$colour->name?>/<?=$lens->name?><br />
							Unit Price: <?=html_text::currency($product->price)?>
						</td>
						<td class="acenter"><?=$value?></td>
						<td class="aright"><?=html_text::currency(($product->price)*$value)?></td>
					</tr>
					<? $i++;} ?>
					<tr class="summary">
						<td colspan="3">
							<strong>Subtotal:</strong><br />
							Delivery Cost
						</td>
						<td>
							<strong><?=html_text::currency($_CHECKOUT->cart_total())?></strong><br />
							<?=html_text::currency($_CHECKOUT->delivery())?>
						</td>
					</tr>
					<tr class="summary-final">
						<td colspan="3">Total</td>
						<td><?=html_text::currency($_CHECKOUT->total())?></td>
					</tr>
				</table>
				<p>Additional comments you would like to make to Eyestyle about your order (optional):</p>
				<p><?=$form->output('comment')?></p>
			</div>
			</fieldset>
			<div class="actionbuttons clearfix">
				<button type="submit" value="Confirm Payment Details" class="fright">Confirm Payment Details</button>
			<button type="button" value="Back" onclick="javascript:history.back();">Back</button>
			</div>
		
		
		<?php
				break;
			case '4':
		?>	
		<h5>Payment Details</h5>
		<fieldset>
			<table border="0" cellpadding="0" cellspacing="1" width="100%" class="grid marginbottom">
				<tr>
					<th colspan="3">Payment Method</th>
				</tr>
				<? if ($australiaOnly ) { ?>
				<tr>
					<td class="optiongroup"><?=$form->output('payment_method','cc')?><label for="payment_method_cc">Credit Card</label></td>
					<td>
						<h6>Secure online credit card payment is recommended.</h6>
						We accept Visa, Mastercard.<br />
						This secure transaction processing is supported by eWay
					</td>
					<td width="120" class="aright"><img src="images/logo_eway.gif" alt="Eway" width="120" height="60" /></td>
				</tr>
				<tr>
					<td class="optiongroup" width="105"><?=$form->output('payment_method','dd')?><label for="payment_method_dd">Direct Deposit</label></td>
					<td colspan="2">
						You can direct deposit or bank transfer the order amount to our bank account.<br />
						Once we confirm your payment, we will deliver to you as soon as possible.
					</td>
				</tr>
				<tr>
					<td class="optiongroup"><?=$form->output('payment_method','mo')?><label for="payment_method_mo">Money Order</label></td>
					<td colspan="2">
						You can mail us your bank cheque or money order. Once we confirm your payment,<br />
						we will deliver to you as soon as possible. Sorry we do not accept personal cheques. 
					</td>
				</tr>
				<? } ?>
				<tr>
					<td class="optiongroup" width="100"><?=$form->output('payment_method','pp')?><label for="payment_method_pp">PayPal</label></td>
					<td>
						We accept <a href="http://www.paypal.com.au/" target="_blank">PayPal</a>.<br />
						Easy, quick, secure payment.
					</td>
					<td width="120" class="aright"><img src="images/logo_paypal.jpg" alt="PayPal" width="60" height="38" /></td>
				</tr>
			</table>
			<div id="payment-cc" name="payment-cc" class="hidden">
				<h5>Please enter your credit card details:</h5>
				<table border="0" cellpadding="0" cellspacing="0">
					<?php
						$row = 0;
						foreach($fields as $title=>$field) {
					?>
					
					<tr>
						<td width="160"><?=$title?>:</td>
						<td><?=$form->output($field)?></td>
					</tr>
					<?php } ?>
					<tr>
						<td>Expiry Date:</td>
						<td><?=$form->output('cc_exp_month')?> <?=$form->output('cc_exp_year')?></td>
					</tr>
				</table>
			</div>
			</fieldset>
			<div class="actionbuttons clearfix">
				<?php if ($australiaOnly) { ?>
				<div class="table_btn_submit fright" id="generalButton"><span id="please_wait" style="display:none;font-weight:bold;font-size:1.1em;">Please wait...</span> &nbsp; <button id="btn_submit" type="submit" value="Place Order" class="fright"  onclick="document.getElementById('please_wait').style.display='inline';document.getElementById('form_checkout').submit();this.disabled = true;">Place Order</button></div>
				<?php } ?>
						
				<div class="table_btn_submit fright" id="paypalButton" style="display: <?=$australiaOnly ? 'none' : 'block'?>"><span id="please_wait2" style="display:none;font-weight:bold;font-size:1.1em;">Please wait...</span> &nbsp; <button id="btn_paypal" type="submit" value="Place Order with PayPal" class="fright"   onclick="document.getElementById('please_wait2').style.display='inline';document.getElementById('form_checkout').submit();this.disabled = true;"/>Place Order with PayPal</button></div>
		
				<button type="button" value="Back" onclick="javascript:history.back();">Back</button>
			</div>
		
		<?php
				break;
			default:
				break;
			}
		?>
		<?=$form->output_close()?>
	</div>
	<?php require_once('inc_footer.php'); ?>