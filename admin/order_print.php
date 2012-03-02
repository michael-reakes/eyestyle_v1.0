<?php
$_ACCESS = 'order';
$_SECTION = 'Order Management';
$_PAGE = 'Print Invoice(s)';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$order_array = array();
if(is_array($_GET['id'])) {
	foreach ($_GET['id'] as $id) {
		$order_array[] = $id;
	}
} else {
	$order_array[] = $_GET['id'];
}

$form = new html_form('form_order_print', 'action_order_print.php?'.http::build_query($_GET), 'POST');

$form->add(new html_form_button('submit', 'Print'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'order_order.php?status=unconfirmed', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_order.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=$form->output_open('target="_blank"')?>
		<div class="info">
			<h4>Invoice Printing Confirmation</h4>
			<br />
			<p>A PDF contains the following invoices will be generated:</p>
			<ul>
				<?php foreach ($order_array as $order_id) {?>
				<li><?=$order_id?></li>
				<?php } ?>
			</ul>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?>&nbsp;<?=$form->output('submit')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>