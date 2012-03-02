<?php
$_ACCESS = 'order';
$_SECTION = 'Order Management';
$_PAGE = 'View Order';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$order = new dbo('order', $_GET['id']);
$this_customer = new dbo('customer',$order->customer_id);

$order_item_list = $order->load_children('order_item');
$status = $order->status;

$status_options = array();
if ($status == 'confirmed' || $status == 'processing' || $status == 'delivered') {
	$status_options['unconfirmed'] = 'Unconfirmed';
} 
if ($status == 'processing' || $status == 'delivered') {
	$status_options['confirmed'] = 'Confirmed';
}
if ($status == 'delivered') {
	$status_options['processing'] = 'Processing';
}

switch($status) {
	case 'unconfirmed':
		$new_status = 'confirmed';
		break;
	case 'confirmed':
		$new_status = 'processing';
		break;
	case 'processing':
		$new_status = 'delivered';
		break;
	default:
		$new_status = '';
		break;
}


//////// dispatch address editing function
//////// only work if the status is unconfirmed or confirmed

if ($status == 'confirmed' || $status == 'unconfirmed') {
	$dispatchedit = 1;
}
else {
	$dispatchedit = 0;
}

//////////////////////////////////



$payment_options = array('N/A'=>'N/A', 'Credit Card'=>'Credit Card', 'Direct Deposit'=>'Direct Deposit', 'Money Order'=>'Money Order');

$form_status = new html_form('form_order_order_status','action_order_order_status.php','GET');
$form_status->add(new html_form_hidden('id',$order->order_id));
$form_status->add(new html_form_select('status',$status_options,'',true,false,'full',$status));
$form_status->add(new html_form_button('submit_update','Update'));
$form_status->register();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'order_order.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_order.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<div class="page_subtitle">
			<?php if($status != 'unconfirmed') {?>
			<div class="float_right">
				<table cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td>
							<a href="action_order_print.php?id=<?=$order->order_id?>" target="_blank"><img src="images/icon_print.gif"></a>
						</td>
						<td>
							&nbsp;<a href="action_order_print.php?id=<?=$order->order_id?>" target="_blank">Print Invoice</a>
						</td>
					</tr>
				</table>
			</div>
			<?php } ?>
			Order Details
		</div>
		<div class="info">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr class="valign_top">
					<td width="65%">
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td class="attribute_label" width="170px">Order Number:</td>
								<td class="attribute_value"><?=$order->order_id?></td>
							</tr>
							<tr>
								<td class="attribute_label">Order Status:</td>
								<td class="attribute_value"><?=$status == 'delivered' ? 'Dispatched' : ucfirst($status)?></td>
							</tr>

							<tr>
								<td class="attribute_label">Customer Details:</td>
								<td class="attribute_value"><b>Name:</b> <?=$this_customer->fullname?>&nbsp;&nbsp;&nbsp;&nbsp;<b>ID:</b> <?=$this_customer->customer_id?>&nbsp;&nbsp;&nbsp;&nbsp;<b>Email:</b> <?=$this_customer->email?></td>
							</tr>
						
							<tr>
								<td class="attribute_label">Payment Method:</td>
								<td class="attribute_value"><?=!empty($order->payment_method) ? $order->payment_method : 'N/A'?></td>
							</tr>
							<tr>
								<td class="attribute_label">Payment Reference No:</td>
								<td class="attribute_value"><p><?=!empty($order->payment_reference) ? $order->payment_reference : 'N/A'?></p></td>
							</tr>
							<tr>
								<td class="attribute_label">Checkout Date:</td>
								<td class="attribute_value"><?=utils_time::datetime($order->date_created)?></td>
							</tr>
							<tr>
								<td class="attribute_label">Processed Date:</td>
								<td class="attribute_value"><?=$order->date_processed != 0 ? utils_time::datetime($order->date_processed) : 'N/A'?></td>
							</tr>
							<tr>
								<td class="attribute_label">Dispatch Date:</td>
								<td class="attribute_value"><p><?=$order->date_delivered != 0 ? utils_time::datetime($order->date_delivered) : 'N/A'?></p></td>
							</tr>
							<tr>
								<td class="attribute_label">Courier Name:</td>
								<td class="attribute_value"><?=!empty($order->courier_name) ? $order->courier_name : 'N/A'?></td>
							</tr>
							<tr>
								<td class="attribute_label">Courier Tracking No:</td>
								<td class="attribute_value"><?=!empty($order->tracking_no) ? $order->tracking_no : 'N/A'?></td>
							</tr>
							
							<tr>
								<td class="attribute_label">Grand Total:</td>
								<td class="attribute_value"><?=html_text::currency($order->total)?></td>
							</tr>
							<tr>
								<td class="attribute_label">Customer Comment:</td>
								<td class="attribute_value"><?=!empty($order->comment) ? $order->comment : 'N/A'?></td>
							</tr>
						</table>
					</td>
					<td width="35%" align="right">
						<table cellpadding="0" cellspacing="0" border="0">
						<?php if ($status != 'delivered') { ?>
							<?php
								if ($status == 'unconfirmed' || $status == 'processing') {
									$url = 'order_order_comment.php';
								} else {
									$url = 'action_order_order_status.php';
								}
							?>
							<tr align="left">
								<td>
									<br /><a href="<?=$url?>?id=<?=$order->order_id?>&status=<?=$new_status?>" class="set_order_status">Set status to '<?=$new_status == 'delivered' ? 'Dispatched' : ucfirst($new_status)?>'</a><br /><br/>
								</td>
							</tr>
						<?php } ?>
						<?php if ($status != 'unconfirmed') { ?>
						<tr align="left">
							<td>
								<div class="float_panel_title">Reverse Order Status</div>
								<div class="float_panel_body" style="padding:5px;">
									<?=$form_status->output_open()?>
									<?=$form_status->output('id')?>
									<p>
										Status:<br />
										<?=$form_status->output('status')?>
									</p>
									<div class="align_right"><?=$form_status->output('submit_update')?></div>
									<?=$form_status->output_close()?>
								</div>
							</td>
						</tr>
						<?php } ?>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<div class="clear_both"></div>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="data_table">
			<tr>
				<td width="50%" valign="top">
					<div class="page_subtitle">Billing Address</div>
					<div class="info">
						<?=$order->billing_fullname?><br/>
						<?=$order->billing_address?><br/>
						<?=$order->billing_suburb?> <?=$order->billing_state?> <?=$order->billing_postcode?><br/>
						<?=$order->billing_country?><br />
						Ph: <?=$order->billing_phone?> &nbsp;&nbsp;Mobile: <?=$order->billing_mobile?>
					</div>
				</td>
				<td width="50%" valign="top">
					<div class="page_subtitle">Dispatch Address</div>
					<div class="info">
						<?=$order->delivery_fullname?><br/>
						<?=!empty($order->delivery_company) ? $order->delivery_company.'<br />' : ''?>
						<?=$order->delivery_address?><br/>
						<?=$order->delivery_suburb?> <?=$order->delivery_state?> <?=$order->delivery_postcode?><br/>
						<?=$order->delivery_country?><br />
						Ph: <?=$order->delivery_phone?>
						<?php
						////// dispatch edit link only appear when the status is confirmed / unconfirmed
						if ($dispatchedit) {
						?>
							<br /> <a href="order_order_dispatch_edit.php?id=<?=$order->order_id?>">Edit</a>
						<?php
						}
						?>
					</div>
				</td>
			</tr>
		</table>
		<div class="page_subtitle">Ordered Items</div>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="data_table">
			<tr class="table_heading">
				<th align="left">Product Code</th>
				<th align="left">Product Name</th>
				<th align="center">Lens / Colour</th>
				<th width="50px">Qty</th>
				<th width="100px" class="align_right">Unit Price</th>
				<th width="100px" class="align_right">Amount</th>
			</tr>
			<?php
				foreach($order_item_list as $order_item) {
					$product = new dbo('product',$order_item->product_id);
					$brand = new dbo('brand',$product->brand_id);
			?>
			<tr class="table_row">
				<td><?=!empty($order_item->code) ? $order_item->code : 'N/A'?></td>
				<td><?=$brand->name?> - <?=$product->name?></td>
				<td align="center">
					<b>Frame:</b>&nbsp;<?=$order_item->colour_name?>&nbsp;&nbsp;
					<b>Lens Type:</b>&nbsp;<?=$order_item->lens_name?>
				</td>
				<td align="center"><?=$order_item->quantity?></td>
				<td align="right"><?=html_text::currency($order_item->unit_price)?></td>
				<td align="right"><?=html_text::currency($order_item->quantity*$order_item->unit_price)?></td>
			</tr>
			<?php 
				}
				
			?>
			
			<tr>
				<td colspan="6" align="right"><hr/></td>
			</tr>
			<tr class="table_large">
				<td colspan="5" align="right">Delivery Charges:</td>
				<td align="right" style="padding-right:10px"><?=html_text::currency($order->delivery_cost)?></td>
			</tr>
			<tr class="table_large">
				<td colspan="5" align="right"><b>Total:</b></td>
				<td align="right" style="padding-right:10px"><b><?=html_text::currency($order->total)?></b></td>
			</tr>
		</table>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>