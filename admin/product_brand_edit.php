<?php
$_ACCESS = 'product.brand';
$_SECTION = 'Product';
$_PAGE = 'Edit Designer';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

http::register_path();

$brand = new dbo('brand', $_GET['id']);

$form = new html_form('form_product_brand_edit', 'action_product_brand_edit.php?'.http::build_query($_GET));

$form->add(new html_form_text('name', true, $brand->name,'full'));
$form->add(new html_form_file('image_men', true, $brand->image_men));
$form->add(new html_form_file('image_women', true, $brand->image_women));
$form->add(new html_form_button('submit', 'Save'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Product Management'=>'product_product.php', 'Designer Management'=>'product_brand.php', $_PAGE=>'');

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
			<table border="0" cellpadding="0" cellspacing="0" width="470px">
				<tr>
					<td class="attribute_label">Designer Name: <?=html::check_required($form,'name')?></td>
					<td class="attribute_value"><?=$form->output('name')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Banner (Mens): <?=html::check_required($form,'image_men')?></td>
					<td class="attribute_value">
						<?php if (is_file('../'.$brand->image_men)): ?>
						<img src="../<?=$brand->image_men?>" alt="<?=basename($brand->image_men)?>" width="300" />
						<?php endif; ?>
						<?=$form->output('image_men')?>
					</td>
				</tr>
				<tr>
					<td class="attribute_label" width="160">Banner (Womens): <?=html::check_required($form,'image_women')?></td>
					<td class="attribute_value">
						<?php if (is_file('../'.$brand->image_women)): ?>
						<img src="../<?=$brand->image_women?>" alt="<?=basename($brand->image_women)?>" width="300" />
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