<?php
$_REQUIRE_SSL = true;

require_once('inc.php');

http::register_path();

customer::check_login();

$_PAGE = 'view order';

$order_id = isset($_GET['id']) ? $_GET['id'] : '';
$order_found = false;
$status = '';

$order_list = new dbo_list('order','WHERE `billing_email` = "'.$_CUSTOMER->email.'" AND `order_id` = "'.$order_id.'"');
if(($order = $order_list->get_first()) !== false) {
	$order_found = true;
	$order_item_list = $order->load_children('order_item');

	$status = $order->status;
}

$breadcrumb = array('home'=>'./','my account'=>'customer_account_details','my purchases'=>'customer_order.php', $_PAGE=>'');

require_once('inc_header.php');

?>

	<div id="content" class="clearfix">
			<?php require_once('inc_customer_lcolumn.php'); ?>
			<div class="rcolumn">
				<?=html::breadcrumb($breadcrumb)?>
				<div class="controls clearfix">
					<ul class="filters">
						<li<?=$status == 'all' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php')?>">All Orders</a></li>
						<li<?=$status == 'unconfirmed' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=unconfirmed')?>">Unconfirmed</a></li>
						<li<?=$status == 'confirmed' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=confirmed')?>">Confirmed</a></li>
						<li<?=$status == 'processing' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=processing')?>">Processing</a></li>
						<li<?=$status == 'delivered' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=delivered')?>">Dispatched</a></li>
					</ul>
				</div>
				<fieldset id="ordersummary">
				<h3>Order Number <?=$order->order_id?></h3>
				<div class="addresscol">
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="panel grid">
						<tr>
							<th>Billing Details</th>
						</tr>
						<tr>
							<td>
								<? $country = new dbo('country');
									$billing_country = $country->load($order->billing_country) ? $country->name : '';
								?>
								<p><?=$order->billing_fullname?></p>
								<p>
									<?=$order->billing_address?><br />
									<?=$order->billing_suburb?>,  <?=$order->billing_state?> <?=$order->billing_postcode?><br />
									<?=$billing_country?><br />
									Phone: <?=$order->billing_phone?><br />
									Email: <?=$order->billing_email?>
								</p>
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
									$delivery_country = $country->load($order->delivery_country) ? $country->name : '';
								?>
								<p><?=$order->delivery_fullname?></p>
								<p>
									<?=$order->delivery_address?><br />
									<?=$order->delivery_suburb?>,  <?=$order->delivery_state?> <?=$order->delivery_postcode?><br />
									<?=$delivery_country?><br />
									Phone: <?=$order->delivery_phone?><br />
								</p>
							</td>
						</tr>
					</table>
				</div>
				<div class="ordercol">
					<table border="0" cellpadding="0" cellspacing="1" width="100%" class="grid marginbottom">
						<tr>
							<th colspan="2">Order Details</th>
						</tr>
						<tr>
							<td width="200">Status:</td>
							<td>
							<?=$order->status == 'delivered' ? 'Dispatched' : ucwords($order->status)?> | 
							<?php if ($status != 'unconfirmed') { ?>
								<a href="action_order_print.php?id=<?=$order->order_id?>" target="_blank">
								Print Tax Invoice
								</a>
							<?php } else { ?>
							<a href="action_order_print.php?id=<?=$order->order_id?>" target="_blank">Print order confirmation</a>
							<?php } ?>
							</td>
						</tr>
						<tr>
							<td>Subtotal:</td>
							<td><?=html_text::currency( $order->total - $order->delivery_cost )?></td>
						</tr>
						<tr>
							<td>Delivery Cost:</td>
							<td><?=html_text::currency($order->delivery_cost)?></td>
						</tr>
						<tr>
							<td>Grand Total:</td>
							<td><?=html_text::currency($order->total)?></td>
						</tr>
						<tr>
							<td>Purchase Date:</td>
							<td><?=utils_time::date($order->date_created)?></td>
						</tr>
						<tr>
							<td>Order Processing Date:</td>
							<td>
								<? if (!empty($order->date_processed)) { ?>
									<?=utils_time::date($order->date_processed)?>
								<? } else {?>
									'N/A'
								<? } ?>
							</td>
						</tr>
						<tr>
							<td>Delivery Date</td>
							<td>
								<? if (!empty($order->date_delivered)) { ?>
									<?=utils_time::date($order->date_delivered)?>
								<? } else {?>
									'N/A'
								<? } ?>
							</td>
						</tr>
						<tr>
							<td>Additional Comment</td>
							<td><?=!empty($order->comment) ? $order->comment : 'N/A'?></td>
						</tr>
						<tr>
							<td>Courier Name</td>
							<td>
							<? 
								if (!empty($order->courier_name)){ 
									echo $order->courier_name;
								}else{
									echo 'N/A';
								}
							?>
							</td>
						</tr>
						<tr>
							<td>Courier Tracking Number</td>
							<td><?=!empty($order->tracking_no) ? $order->tracking_no : 'N/A'?></td>
						</tr>
					</table>
					<table border="0" cellpadding="0" cellspacing="0" width="100%" class="grid marginbottom">
						<tr>
							<th>Product Name</th>
							<th width="100" class="aright">Unit Price</th>
							<th width="40" class="acenter">Qty</th>
							<th width="100" class="aright">Subtotal</th>
						</tr>
						<?php
							$counter = 0;
							foreach($order_item_list as $order_item) {
								if ($counter == 0) {
									$class = 'row_ab_a';
									$counter++;
								} else {
									$class = 'row_ab_b';
									$counter = 0;
								}
								$product= new dbo('product',$order_item->product_id);
								$brand = new dbo('brand',$product->brand_id);
						?>
						<tr class="<?=$class?>">
							<td>
								<h6><?=$brand->name?></h6>
								<?=$product->name?> (Frame:<?=$order_item->colour_name?> / Lens:<?=$order_item->lens_name?>)
							</td>
							<td class="aright"><?=html_text::currency($order_item->unit_price)?></td>
							<td class="acenter"><?=$order_item->quantity?></td>
							<td class="aright"><?=html_text::currency($order_item->unit_price*$order_item->quantity)?></td>
						</tr>
						<? } ?>
						<tr class="summary">
							<td colspan="3">
								<strong>Subtotal:</strong><br />
								Delivery Cost:
							</td>
							<td>
								<strong><?=html_text::currency($order_item->unit_price*$order_item->quantity)?></strong><br />
								<?=html_text::currency($order->delivery_cost)?>
							</td>
						</tr>
						<tr class="summary-final">
							<td colspan="3">Total</td>
							<td><?=html_text::currency($order_item->unit_price*$order_item->quantity+$order->delivery_cost)?></td>
						</tr>
					</table>
				</div>
				</fieldset>
			</div>
		</div>
	
<?php require_once('inc_footer.php') ?>