<?php
$_ACCESS = 'order';
$_SECTION = 'Order Management';
$_PAGE = 'Order List';

require_once('inc.php');

http::register_path();

$status = isset($_GET['status']) ? $_GET['status'] : 'unconfirmed';

$view_mode = 'all';
$key = '';
if (isset($_GET['key'])) {
	$view_mode = 'search';
	$key = $_GET['key'];
}

http::halt_if($status != 'unconfirmed' && $status != 'confirmed' && $status != 'processing' && $status != 'delivered');

switch ($view_mode) {
	case 'all':
		$order_list = new dbo_list('order','WHERE `status` = "'.$status.'"');
		break;
	case 'search':
		$order_list = new dbo_list('order','WHERE `order_id` = "'.$key.'" OR `billing_fullname` LIKE "%'.$key.'%" OR `delivery_fullname` LIKE "%'.$key.'%"');
		break;
}

$len = $order_list->count();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'order_order.php', $_PAGE=>'');

$pager = new html_pager($order_list, array('order_id'=>'Order Number', 'total'=>'Total', 'payment_method'=>'Payment Method', 'date_created'=>'Checkout Date'), 'd');

$form_search = new html_form('form_search', $_SERVER['PHP_SELF'], 'GET');
$form_search->add(new html_form_text('key', false, $key));
$form_search->add(new html_form_image_button('submit', 'images/icon_search.gif', '', 'icon_btn'));

$form = new html_form('form_order_order', 'action_order_order.php');
foreach ($pager->get_page() as $order) {
	$form->add(new html_form_checkbox('checked_order', $order->order_id, 'checkbox'));
}
$form->add(new html_form_button('submit_delete', 'Delete', '', 'submit', true));
$form->register();

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_order.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title">
			<div class="search">
				Search by Order Number / Customer Name: 
				<?=$form_search->output_open()?>
				<?=$form_search->output('key')?>
				<?=$form_search->output('submit')?>
				<?=http::hidden_fields($_GET, array('key', 'status'))?>
				<?=$form_search->output_close()?>
			</div>
			<?=$_PAGE?>
		</div>
		<?=html_message::show()?>
		<?=$pager->show()?>
		<?=$form->output_open()?>
		<div class="band"><?=$form->output('submit_delete')?></div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
			<tr class="table_heading">
				<th width="10px"><input name="check_all" type="checkbox" class="checkbox" onclick="checkAll('form_order_order','check_all','checked_order[]');"/></th>
				<?=$pager->column('order_id')?>
				<?=$pager->column('total')?>
				<?=$pager->column('payment_method')?>
				<?=$pager->column('date_created')?>
				<th>Action</th>
			</tr>
			<?php if ($len == 0) { ?>
			<tr class="table_row"><td colspan="6" align="center">There are no orders</td></tr>
			<?php } else {
			foreach ($pager->get_page() as $order) {
			?>
				<tr class="table_row">
					<td><?=$form->output('checked_order', $order->order_id)?></td>
					<td><a href="order_order_view.php?id=<?=$order->order_id?>" title="View Order"><?=$order->order_id?></a></td>
					<td align="center"><a href="order_order_view.php?id=<?=$order->order_id?>" title="View Order"><?=html_text::currency($order->total)?></a></td>
					<td align="center"><a href="order_order_view.php?id=<?=$order->order_id?>" title="View Order"><?=$order->payment_method?></a></td>
					<td align="center"><a href="order_order_view.php?id=<?=$order->order_id?>" title="View Order"><?=utils_time::date($order->date_created)?></a></td>
					<td align="center">
						<a href="order_order_view.php?id=<?=$order->order_id?>" title="View Order"><img src="images/icon_view.gif"/></a>
						<?php if ($order->status != 'unconfirmed') {?>
							<a href="action_order_print.php?id=<?=$order->order_id?>" title="Print Invoice" target="_blank"><img src="images/icon_print.gif"/></a>
						<?php } ?>
					</td>
				</tr>
			<?php }
			}
			?>
		</table>
		<div class="band"><?=$form->output('submit_delete')?></div>
		<?=$form->output_close()?>
		<?=$pager->show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>