<?php
$_ACCESS = 'product.brand';
$_SECTION = 'Product';
$_PAGE = 'Designer Management';

require_once('inc.php');

http::register_path();

$brand_list = new dbo_list('brand');

$pager = new html_pager($brand_list, array('name'=>'Designer Name'),'a');

$form = new html_form('form_product_brand', 'action_product_brand.php');
foreach ($pager->get_page() as $brand) {
	$form->add(new html_form_checkbox('checked_brand', $brand->brand_id, 'checkbox'));
}
$form->add(new html_form_button('submit_add', 'Add Designer', '', 'submit', true));
$form->add(new html_form_button('submit_delete', 'Delete', '', 'submit', true));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Product Management'=>'product_product.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_product.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$pager->show()?>
		<?=$form->output_open()?>
		<div class="band"><?=$form->output('submit_add')?> <?=$form->output('submit_delete')?></div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
			<tr class="table_heading">
				<th width="10px"><input name="check_all" type="checkbox" class="checkbox" onclick="checkAll('form_product_brand','check_all','checked_brand[]');"/></th>
				<?=$pager->column('name')?>
				<th>Action</th>
			</tr>
			<?php
			foreach ($pager->get_page() as $brand) {
			?>
				<tr class="table_row">
					<td><?=$form->output('checked_brand', $brand->brand_id)?></td>
					<td><a href="product_brand_edit.php?id=<?=$brand->brand_id?>" title="Edit Brand"><?=$brand->name?></a></td>
					<td align="center">
						<a href="product_brand_edit.php?id=<?=$brand->brand_id?>" title="Edit Brand"><img src="images/icon_edit.gif"/></a>
						<?php if ($brand->brand_id != 'admin') {?>
							<a href="product_brand_delete.php?id[]=<?=$brand->brand_id?>" title="Delete Brand"><img src="images/icon_delete.gif"/></a>
						<?php } ?>
					</td>
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