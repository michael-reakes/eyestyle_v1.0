<?php
$_ACCESS = 'product.category';
$_SECTION = 'Product';
$_PAGE = 'Add Category';

require_once('inc.php');

$form = new html_form('form_product_category_add', 'action_product_category_add.php');

$form->add(new html_form_text('name', true,'','full'));
$form->add(new html_form_file('image_men', true));
$form->add(new html_form_file('image_women', true));
$form->add(new html_form_button('submit', 'Add Category'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->add(new html_form_file('banner'));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Product Management'=>'product_product.php', 'Category Management'=>'product_categories.php', $_PAGE=>'');

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
					<td class="attribute_label" width="180">Category Name: <?=$form->output_required('name')?></td>
					<td class="attribute_value" width="370"><?=$form->output('name')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Banner (Mens): <?=html::check_required($form,'image_men')?></td>
					<td class="attribute_value"><?=$form->output('image_men')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Banner (Womens): <?=html::check_required($form,'image_women')?></td>
					<td class="attribute_value"><?=$form->output('image_women')?></td>
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