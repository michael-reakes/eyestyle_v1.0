<?php
$_ACCESS = 'order';
$_SECTION = 'Order Management';
$_PAGE = 'View Order';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));
http::halt_if(!isset($_GET['status']));
$status = $_GET['status'];
http::halt_if($status != 'confirmed' && $status != 'delivered');


$order = new dbo('order', $_GET['id']);
$form = new html_form('form_order_order_status','action_order_order_status.php');
$form->add(new html_form_hidden('id',$order->order_id));
$form->add(new html_form_hidden('status',$status));
$form->add(new html_form_text('comment',true,'','',false,30));

if ($status == 'delivered') {
	$courier_list = new dbo_list('courier','','name');
	$courier_options = array();
	foreach($courier_list->get_all() as $courier) {
		$courier_options[$courier->name] = $courier->name;
	}
	$form->add(new html_form_select('courier_id',$courier_options,'-- select a courier --',true));
}

$form->add(new html_form_button('submit',$status == 'confirmed' ? 'Set Confirmed' : 'Set Dispatched'));
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
		<div class="page_subtitle">
			Order Details - <?=$status == 'confirmed' ? 'Set Payment Reference Number' : 'Set Courier Details'?>
		</div>
		<div class="info">
			<?=$form->output_open()?>
			<?=$form->output('id')?><?=$form->output('status')?>
			<table border="0" cellpadding="0" cellspacing="0">
				<?php if ($status == 'confirmed') { ?>
				<tr class="valign_top">
					<td class="attribute_label">
						<p>Please enter a payment reference number: <?=$form->output('comment')?> <?=$form->output('submit')?></p>
						<em>NB. an invoice email will be sent to the customer once the order is set to "confirmed".</em>
					</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td width="140px" class="attribute_label">Courier: <?=html::check_required($form, 'courier_id')?></td>
					<td class="attribute_value"><?=$form->output('courier_id')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Courier Tracking Number: <?=html::check_required($form, 'comment')?></td>
					<td class="attribute_value"><?=$form->output('comment')?> <?=$form->output('submit')?></td>
				</tr>
				<?php } ?>
			</table>
			<?=$form->output_close()?>
		</div>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>