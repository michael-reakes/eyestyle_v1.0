<?php
$_ACCESS = 'product.category';
$_SECTION = 'Product';
$_PAGE = 'Category Management';

require_once('inc.php');

http::register_path();

$category_list = new dbo_list('category','WHERE `parent_id` = 0');
$pager = new html_pager($category_list, array('sort_order'=>'Sort'));
$len = $category_list->count();

$form = new html_form('form_product_categories', 'action_product_categories.php');
$form->add(new html_form_checkbox('check_all', '', 'checkbox', false, "javascript:checkAll('".$form->name."','check_all[]','checked_id[]');"));
foreach ($pager->get_page() as $category) {
	$form->add(new html_form_checkbox('checked_id', $category->category_id, 'checkbox', false, "javascript:checkAllTicked('".$form->name."','checked_id[]','check_all[]');"));

	category_checkbox($category->category_id);
}

$form->add(new html_form_button('submit_add', 'Add Category', '', 'submit', true));
$form->add(new html_form_button('submit_delete', 'Delete', '', 'submit', true));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Product Management'=>'product_product.php', $_PAGE=>'');

function category_checkbox($parent_id) {
	global $form;
	$category_list = new dbo_list('category', "WHERE `parent_id` = ".$parent_id, "sort_order", "ASC");
	foreach ($category_list->get_all() as $category) {
		$form->add(new html_form_checkbox('checked_id', $category->category_id, 'checkbox', false, "javascript:checkAllTicked('".$form->name."','checked_id[]','check_all[]');"));

		$sub_category_list = new dbo_list('category', "WHERE `parent_id` = ".$category->category_id, "category_id", "ASC");
		if ($sub_category_list->count() > 0) {
			category_checkbox($category->category_id, $form);
		}
	}
}

function category_table($parent_id, $depth) {
	global $form;
	$category_list = new dbo_list('category', "WHERE `parent_id` = ".$parent_id, "sort_order", "ASC");
	$category_array = $category_list->get_all();
	for ($i=0; $i<count($category_array); $i++) {
		$category = $category_array[$i];
		print '<tr class="table_subrow"><td>'.$form->output('checked_id',$category->category_id).'</td>';
		print '<td class="align_center">';
		if ($i != 0) {
			print '<a href="action_product_category_sort.php?id='.$category->category_id.'&action=up" title="Move Up"><img src="images/arrow_moveup.gif" /></a>';
		} else {
			print '<img src="images/arrow_moveup_disabled.gif" />';
		}
		if ($i != count($category_array) - 1) {
			print ' <a href="action_product_category_sort.php?id='.$category->category_id.'&action=down" title="Move Down"><img src="images/arrow_movedown.gif" /></a>';
		} else {
			print ' <img src="images/arrow_movedown_disabled.gif" />';
		}
		print '</td>';
		print '<td><a href="product_category_edit.php?id='.$category->category_id.'" title="Edit Category">';
		for ($j=0; $j<$depth; $j++) {
			print '--';
		}
		print ' '.$category->name.'</a></td>';
		print '<td class="align_center"><a href="product_category_edit.php?id='.$category->category_id.'" title="Edit Category"><img src="images/icon_edit.gif"/></a> <a href="product_category_delete.php?id[]='.$category->category_id.'" title="Delete Category"><img src="images/icon_delete.gif"/></a></td></tr>';

		$sub_category_list = new dbo_list('category', "WHERE `parent_id` = ".$category->category_id, "category_id", "ASC");
		if ($sub_category_list->count() > 0) {
			category_table($category->category_id, $depth + 1);
		}
	}
}

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
				<th width="10px"><?=$form->output('check_all')?></th>
				<th width="40px">Sort</th>
				<th>Category Name</th>
				<th>Action</th>
			</tr>
			<?php if ($len == 0) { ?>
				<tr class="table_row">
					<td class="align_center" colspan="4">There are no categories</td>
				</tr>
			<?php } else {
					$category_array = $pager->get_page();
					for ($i=0; $i<count($category_array); $i++) {
						$category = $category_array[$i];
			?>
				<tr class="table_row">
					<td>
						<? if ($category->category_id != 1 || $category->category_id != 2) { ?>
						<?=$form->output('checked_id', $category->category_id)?>
						<? } ?>
					</td>
					
					<td class="align_center">
						<?php if ($i != 0) { ?>
							<a href="action_product_category_sort.php?id=<?=$category->category_id?>&action=up" title="Move Up"><img src="images/arrow_moveup.gif" /></a>
						<?php } else {?>
							<img src="images/arrow_moveup_disabled.gif" />
						<?php } ?>
						<?php if ($i != count($category_array) - 1) { ?>
							<a href="action_product_category_sort.php?id=<?=$category->category_id?>&action=down" title="Move Down"><img src="images/arrow_movedown.gif" /></a>
						<?php } else {?>
							<img src="images/arrow_movedown_disabled.gif" />
						<?php } ?>
					</td>

					<td><a href="product_category_edit.php?id=<?=$category->category_id?>" title="Edit Category"><?=$category->name?></a></td>
					<td class="align_center">
						<a href="product_category_edit.php?id=<?=$category->category_id?>" title="Edit Category"><img src="images/icon_edit.gif"/></a>
						<a href="product_category_delete.php?id[]=<?=$category->category_id?>" title="Delete Category"><img src="images/icon_delete.gif"/></a>
					</td>
				</tr>
				<?=category_table($category->category_id, 1)?>
			<?php
					}
				}
			?>
		</table>
		<div class="band"><?=$form->output('submit_add')?> <?=$form->output('submit_delete')?></div>
		<?=$form->output_close()?>
		<?=$pager->show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>