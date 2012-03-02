<?php
$_ACCESS = 'delivery';
$_SECTION = 'Delivery';
$_PAGE = 'Couriers';

require_once('inc.php');

http::register_path();

$courier_list = new dbo_list('courier');
$breadcrumbs = array('Home'=>'./', $_SECTION=>'delivery_matrix.php', $_PAGE=>'');

$pager = new html_pager($courier_list, array('courier_id'=>'ID', 'name'=>'Courier Company Name'));

$form = new html_form('form_delivery_delivery_couriers', 'action_delivery_couriers.php');
foreach ($pager->get_page() as $courier) {
	$form->add(new html_form_checkbox('checked', $courier->courier_id, 'checkbox', false, "javacript:checkAllTicked('form_delivery_delivery_couriers', 'checked[]', 'check_all');"));
}
$form->add(new html_form_button('submit_add', 'Add Courier', '', 'submit', true));
$form->add(new html_form_button('submit_delete', 'Delete', '', 'submit', true));
$form->register();

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_delivery.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$pager->show()?>
		<?=$form->output_open()?>
		<div class="band"><?=$form->output('submit_add')?> <?=$form->output('submit_delete')?></div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
			<tr class="table_heading">
				<th width="10px"><input name="check_all" type="checkbox" class="checkbox" onclick="checkAll('form_delivery_delivery_couriers','check_all','checked[]');"/></th>
				<?=$pager->column('courier_id')?>
				<?=$pager->column('name')?>
				<th>Action</th>
			</tr>
			<?php foreach ($pager->get_page() as $courier) { ?>
				<tr class="table_row">
					<td><?=$form->output('checked', $courier->courier_id)?></td>
					<td><?=$courier->courier_id?></td>
					<td><?=$courier->name?></td>
					<td align="center"><a href="delivery_courier_edit.php?id=<?=$courier->courier_id?>" title="Edit Courier"><img src="images/icon_edit.gif"/></a> <a href="delivery_courier_delete.php?id[]=<?=$courier->courier_id?>" title="Delete courier"><img src="images/icon_delete.gif"/></a></td>
				</tr>
			<?php } ?>
		</table>
		<div class="band"><?=$form->output('submit_add')?> <?=$form->output('submit_delete')?></div>
		<?=$form->output_close()?>
		<?=$pager->show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>