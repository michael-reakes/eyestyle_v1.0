<?php
$_ACCESS = 'delivery';
$_SECTION = 'Delivery';
$_PAGE = 'Delete Courier(s)';

require_once('inc.php');

if (isset($_GET['id']) && is_array($_GET['id'])) {
	$courier_array = array();
	foreach ($_GET['id'] as $id) {
		$courier_array[] = new dbo('courier', $id);
	}
} else {
	http::halt();
}

$form = new html_form('form_delivery_delivery_courier_delete', 'action_delivery_courier_delete.php?'.http::build_query($_GET));

$form->add(new html_form_button('delete', 'Delete'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'delivery_matrix.php', 'Couriers'=>'delivery_couriers.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_delivery.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=$form->output_open()?>
		<div class="info">
			<h4>Are you sure?</h4>
			<p>Are you sure you want to delete the following courier(s)?</p>
			<ul>
				<?php foreach ($courier_array as $courier) {?>
				<li><?=$courier->name?></li>
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