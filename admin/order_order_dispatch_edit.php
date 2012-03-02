<?php

$_ACCESS = 'order';
$_SECTION = 'Order Management';
$_PAGE = 'View Order';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));


$country_list = new dbo_list('country');
$country_options = array();
foreach($country_list->get_all() as $country){
	$country_options[$country->code] = $country->name;
}

$order = new dbo('order', $_GET['id']);

// no GET parameter in html_form, only POST is allow...
$form = new html_form('form_order_order_dispatch_edit','action_order_order_dispatch_edit.php');
$form->add(new html_form_hidden('id',$order->order_id));
$form->add(new html_form_text('Dfullname',true,$order->delivery_fullname,'full'));
$form->add(new html_form_textarea('Daddress',true,$order->delivery_address,'full'));
$form->add(new html_form_text('Dsuburb',true,$order->delivery_suburb,'full'));
$form->add(new html_form_text('Dpostcode',true,$order->delivery_postcode,'full'));
$form->add(new html_form_text('Dstate',true,$order->delivery_state,'full'));
$form->add(new html_form_select('Dcountry',$country_options,'',true,false,'',$order->delivery_country));
$form->add(new html_form_text('Dphone',true,$order->delivery_phone,'full'));
$form->add(new html_form_button('submit_update','Update'));
$form->register();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'order_order.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_order.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$form->output_open()?>
		<div class="page_subtitle">
			Edit Dispatch Address
		</div>
		<div class="info">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr class="valign_top">
					<td width="65%">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="attribute_label" width="170px">Full Name:</td>
								<td class="attribute_value"><?=$form->output('Dfullname')?></td>
							</tr>
							<tr>
								<td class="attribute_label">Address:</td>
								<td class="attribute_value"><?=$form->output('Daddress')?></td>
							</tr>
							<tr>
								<td class="attribute_label">Suburb:</td>
								<td class="attribute_value"><?=$form->output('Dsuburb')?></td>
							</tr>
							<tr>
								<td class="attribute_label">State:</td>
								<td class="attribute_value"><?=$form->output('Dstate')?></td>
							</tr>
							<tr>
								<td class="attribute_label">Postcode:</td>
								<td class="attribute_value"><?=$form->output('Dpostcode')?></td>
							</tr>
							<tr>
								<td class="attribute_label">Country:</td>
								<td class="attribute_value"><?=$form->output('Dcountry')?></td>
							</tr>
							<tr>
								<td class="attribute_label">Phone:</td>
								<td class="attribute_value"><?=$form->output('Dphone')?></td>
							</tr>
							<tr>
								<td><?=$form->output('submit_update')?></td>
								<td>&nbsp;</td>
							</tr>
						</table>
					</td>
					<td width="35%" align="right">
						&nbsp;
					</td>
				</tr>
			</table>
		</div>
		<?=$form->output_close()?>
		<div class="clear_both"></div>
	
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>