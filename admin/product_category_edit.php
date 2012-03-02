<?php
$_ACCESS = 'product.category';
$_SECTION = 'Product';
$_PAGE = 'Edit Category';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

//to safeguard the male and female category from updating
$noneditable = false;
if ($_GET['id'] == 1 || $_GET['id'] == 2){
	$noneditable = true;
}

$category = new dbo('category', $_GET['id']);

$form = new html_form('form_product_category_edit', 'action_product_category_edit.php?'.http::build_query($_GET));

$form->add(new html_form_text('name', true, $category->name,'',$noneditable, 57));
$form->add(new html_form_file('image_men', true, $category->image_men));
$form->add(new html_form_file('image_women', true, $category->image_women));
$form->add(new html_form_button('submit', 'Save'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Product Management'=>'product_product.php','Category Management'=>'product_categories.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_product.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$form->output_open()?>
		<div class="info">
			<table border="0" cellpadding="0" cellspacing="0" width="550">
				<tr>
					<td class="attribute_label">Category Name: <?=$form->output_required('name')?></td>
					<td class="attribute_value"><?=$form->output('name')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Banner (Mens): <?=html::check_required($form,'image_men')?></td>
					<td class="attribute_value">
						<?php if (is_file('../'.$category->image_men)): ?>
						<p><img src="../<?=$category->image_men?>" alt="<?=basename($category->image_men)?>" width="300" /></p>
						<?php endif; ?>
						<?=$form->output('image_men')?>
					</td>
				</tr>
				<tr>
					<td class="attribute_label" width="160">Banner (Womens): <?=html::check_required($form,'image_women')?></td>
					<td class="attribute_value">
						<?php if (is_file('../'.$category->image_women)): ?>
						<p><img src="../<?=$category->image_women?>" alt="<?=basename($category->image_women)?>" width="300" /></p>
						<?php endif; ?>
						<?=$form->output('image_women')?>
					</td>
				</tr>
			</table>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?>&nbsp;<?=$form->output('submit')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>