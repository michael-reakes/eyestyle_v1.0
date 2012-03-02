<?php
$_ACCESS = 'delivery';
$_SECTION = 'Delivery';
$_PAGE = 'Delivery Classes';

require_once('inc.php');

http::register_path();

$class_list = new dbo_list('delivery_class');
$breadcrumbs = array('Home'=>'./', $_SECTION=>'delivery_matrix.php', $_PAGE=>'');

$pager = new html_pager($class_list, array('name'=>'Class Name', 'description'=>'Description'));

$form = new html_form('form_system_delivery_classes', 'action_delivery_classes.php');
foreach ($pager->get_page() as $class) {
	$form->add(new html_form_checkbox('checked', $class->delivery_class_id, 'checkbox', false, "javacript:checkAllTicked('form_system_delivery_classes', 'checked[]', 'check_all');"));
}
$form->add(new html_form_button('submit_add', 'Add Class', '', 'submit', true));
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
		<div class="band"><?=$form->output('submit_add')?> <!--<?=$form->output('submit_delete')?>--></div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
			<tr class="table_heading">
				<th width="10px"><input name="check_all" type="checkbox" class="checkbox" onclick="checkAll('form_system_delivery_classes','check_all','checked[]');"/></th>
				<?=$pager->column('name')?>
				<?=$pager->column('description')?>
				<th>Action</th>
			</tr>
			<?php foreach ($pager->get_page() as $class) {
			?>
				<tr class="table_row">
					<td><?=$form->output('checked', $class->delivery_class_id)?></td>
					<td><?=$class->name?></td>
					<td><?=$class->description?></td>
					<td align="center"><a href="delivery_class_edit.php?id=<?=$class->delivery_class_id?>" title="Edit Class"><img src="images/icon_edit.gif"/></a> 
					<!--
					<a href="delivery_class_delete.php?id[]=<?=$class->delivery_class_id?>" title="Delete Class"><img src="images/icon_delete.gif"/></a>
					-->
					</td>
				</tr>
			<?php } ?>
		</table>
		<div class="band"><?=$form->output('submit_add')?> <!--<?=$form->output('submit_delete')?>--></div>
		<?=$form->output_close()?>
		<?=$pager->show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>