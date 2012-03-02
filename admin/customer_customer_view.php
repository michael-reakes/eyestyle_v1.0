<?php
$_ACCESS = 'all';
$_SECTION = 'Customer';
$_PAGE = 'View Customer Account';

require_once('inc.php');

http::register_path();

http::halt_if(!isset($_GET['id']));
$this_customer = new dbo('customer', $_GET['id']);

$breadcrumbs = array('Home'=>'./', 'Customers'=>'customer_customer.php',$_PAGE=>'');

$order_list = new dbo_list('order', 'WHERE `customer_id` = "'.$this_customer->customer_id.'" AND `status` != "neworder"');
$no_purchase = $order_list->count();
$total_purchase = $order_list->sum('total');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_customer.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?> - (<?=$this_customer->fullname?>)</div>
		<?=html_message::show()?>
		<div class="page_subtitle">Account Details</div>
		<div class="info">
			<div class="float_right">
				<div class="float_panel_title">Order History</div>
				<div class="float_panel_body">
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="float_panel_col_left">Number of Orders</td>
							<td class="float_panel_col_right"><?=$no_purchase?></td>
						</tr>
						<tr>
							<td class="float_panel_col_left">Total Order Amount</td>
							<td class="float_panel_col_right"><?=html_text::currency($total_purchase)?></td>
						</tr>
					</table>
				</div>
			</div>
			<table border="0" cellpadding="0" cellspacing="0" width="450px">
				<tr>
					<td class="attribute_label">Fullname:</td>
					<td class="attribute_value"><?=$this_customer->fullname?></td>
				</tr>

				<tr>
					<td class="attribute_label">Company:</td>
					<td class="attribute_value"><?=$this_customer->company?></td>
				</tr>

				<tr>
					<td class="attribute_label">Address:</td>
					<td class="attribute_value"><?=nl2br($this_customer->address)?><br/><?=$this_customer->suburb?> <?=$this_customer->state?> <?=$this_customer->postcode?><br />
					<?=$this_customer->country?>
					</td>
				</tr>
				<tr>
					<td class="attribute_label">Email:</td>
					<td class="attribute_value"><a href="mailto:<?=$this_customer->email?>"><?=$this_customer->email?></a></td>
				</tr>
	
				<tr>
					<td class="attribute_label">Phone:</td>
					<td class="attribute_value"><?=$this_customer->phone?></td>
				</tr>
				<tr>
					<td class="attribute_label">Mobile:</td>
					<td class="attribute_value"><?=$this_customer->mobile?></td>
				</tr>

				<tr>
					<td class="attribute_label">Creation Time:</td>
					<td class="attribute_value"><?=utils_time::datetime($this_customer->date_created)?></td>
				</tr>

				<tr>
					<td class="attribute_label">Last Login Time:</td>
					<td class="attribute_value"><?=utils_time::datetime($this_customer->last_login)?></td>
				</tr>

			</table>
		</div>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>