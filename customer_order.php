<?php
$_REQUIRE_SSL = true;
require_once('inc.php');

http::register_path();

customer::check_login();

$_PAGE = 'my purchases';

$status = isset($_GET['status']) ? $_GET['status'] : 'all';

if ($status == 'all') {
	$order_list = new dbo_list('order','WHERE `billing_email` = "'.$_CUSTOMER->email.'"');
} else {
	$order_list = new dbo_list('order','WHERE `status` = "'.$status.'" AND `billing_email` = "'.$_CUSTOMER->email.'"');
}
$pager = new html_pager($order_list, array('date_created'=>'Order Date', 'order_id'=>'Order Number', 'status'=>'Status','total'=>'Total'),'d');

$order_len = $order_list->count();

$form = new html_form('form_order','customer_order_view.php','get');
$form->add(new html_form_text('id',true,'','',false,false,20));
$form->add(new html_form_image_button('btn_submit','images/btn_arrow_green.gif','search','no_border'));
$form->register();

$breadcrumb = array('home'=>'./','my account'=>'customer_account_details',$_PAGE=>'');

require_once('inc_header.php');

?>


		<div id="content" class="clearfix">
			<?php require_once('inc_customer_lcolumn.php'); ?>
			<div class="rcolumn">
				<?=html::breadcrumb($breadcrumb)?>
				<?php if ($order_len == 0) { ?>
				<div class="controls clearfix">
					<ul class="filters">
						<li<?=$status == 'all' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php')?>">All Orders</a></li>
						<li<?=$status == 'unconfirmed' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=unconfirmed')?>">Unconfirmed</a></li>
						<li<?=$status == 'confirmed' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=confirmed')?>">Confirmed</a></li>
						<li<?=$status == 'processing' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=processing')?>">Processing</a></li>
						<li<?=$status == 'delivered' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=delivered')?>">Dispatched</a></li>
					</ul>
				</div>
				<p>There are no <?=$status?> orders.</p>
				<?php } else { ?>
				<div class="controls clearfix">
				<? if ($pager->total_page > 1) { $pager->show();} ?>
					<ul class="filters">
						<li<?=$status == 'all' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php')?>">All Orders</a></li>
						<li<?=$status == 'unconfirmed' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=unconfirmed')?>">Unconfirmed</a></li>
						<li<?=$status == 'confirmed' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=confirmed')?>">Confirmed</a></li>
						<li<?=$status == 'processing' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=processing')?>">Processing</a></li>
						<li<?=$status == 'delivered' ? ' class="selected"' : ''?>><a href="<?=http::url('customer_order.php?status=delivered')?>">Dispatched</a></li>
					</ul>
				</div>
				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="grid">
					<tr>
						<th>Order Number</th>
						<th>Status</th>
						<th>Order Date</th>
						<th class="aright">Total</th>
						<th width="15">&nbsp;</th>
					</tr>					
					<?php
						$counter = 0;
						foreach($pager->get_page() as $order) {
							if ($counter == 0) { 
								$class = 'row_ab_a';
								$counter++;
							} else {
								$class = 'row_ab_b';
								$counter = 0;
							}
					?>
					<tr class="<?=$class?>">
						<td><?=$order->order_id?></td>
						<td><?=$order->status == 'delivered' ? 'Dispatched' : ucwords($order->status)?></td>
						<td><?=utils_time::date($order->date_created)?></td>
						<td class="aright"><?=html_text::currency($order->total)?></td>
						<td class="acenter"><a href="<?=http::url('customer_order_view.php?id='.$order->order_id)?>"><img src="images/icons/view.gif" alt="View order" width="15" height="15" /></a></td>
					</tr>
					<?php } ?>
				</table>
				<div class="controls bottomcontrols clearfix">
				<?=$pager->show()?>
				</div>
				<?php } ?>
			</div>
		</div>

		

<?php require_once('inc_footer.php') ?>