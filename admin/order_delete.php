<?php
$_ACCESS = 'order';
$_SECTION = 'Order Management';
$_PAGE = 'Delete Order(s)';

require_once('inc.php');

if (isset($_GET['id']) && is_array($_GET['id'])) {
	$order_array = array();
	foreach ($_GET['id'] as $id) {
		$order_array[] = new dbo('order', $id);
	}
} else {
	http::halt();
}

$form = new html_form('form_order_delete', 'action_order_delete.php?'.http::build_query($_GET));

$form->add(new html_form_button('delete', 'Delete'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'order_order.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_order.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=$form->output_open()?>
		<div class="info">
			<h4>Are you sure?</h4>
			<p>Are you sure you want to delete the following order(s)?</p>
			<ul>
				<?php foreach ($order_array as $order) {?>
				<li><?=$order->order_id?></li>
				<?php } ?>
			</ul>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?>&nbsp;<?=$form->output('delete')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>