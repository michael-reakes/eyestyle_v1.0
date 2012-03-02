<?php
$_ACCESS = 'delivery';
$_SECTION = 'Delivery';
$_PAGE = 'Delivery Zones';

require_once('inc.php');

http::register_path();

$zone_list = new dbo_list('zone');
$breadcrumbs = array('Home'=>'./', 'Delivery'=>'delivery_matrix.php', $_PAGE=>'');

$pager = new html_pager($zone_list, array('zone_id'=>'Zone ID', 'name'=>'Zone Name'));

$form = new html_form('form_delivery_delivery_zones', 'action_delivery_zones.php');
foreach ($pager->get_page() as $zone) {
	$form->add(new html_form_checkbox('checked', $zone->zone_id, 'checkbox', false, "javacript:checkAllTicked('form_delivery_delivery_zones', 'checked[]', 'check_all');"));
}
$form->add(new html_form_button('submit_add', 'Add Zone', '', 'submit', true));
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
				<th width="10px"><input name="check_all" type="checkbox" class="checkbox" onclick="checkAll('form_delivery_delivery_zones','check_all','checked[]');"/></th>
				<?=$pager->column('zone_id')?>
				<?=$pager->column('name')?>
				<th>Action</th>
			</tr>
			<?php foreach ($pager->get_page() as $zone) { ?>
				<tr class="table_row">
					<td><?=$form->output('checked', $zone->zone_id)?></td>
					<td><?=$zone->zone_id?></td>
					<td><?=$zone->name?></td>
					<td align="center"><a href="delivery_zone_add_edit.php?id=<?=$zone->zone_id?>" title="Edit Zone"><img src="images/icon_edit.gif"/></a> <a href="delivery_zone_delete.php?id[]=<?=$zone->zone_id?>" title="Delete Zone"><img src="images/icon_delete.gif"/></a></td>
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