<?php
$_ACCESS = 'product.product';
$_SECTION = 'Product';
$_PAGE = 'Delete Product(s)';

require_once('inc.php');

if (isset($_GET['id']) && is_array($_GET['id'])) {
	$product_array = array();
	foreach ($_GET['id'] as $id) {
		$product_array[] = new dbo('product', $id);
	}
} else {
	http::halt();
}

$form = new html_form('form_product_product_delete', 'action_product_product_delete.php?'.http::build_query($_GET));

$form->add(new html_form_button('delete', 'Delete'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Product Management'=>'product_product.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_product.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=$form->output_open()?>
		<div class="info">
			<h4>Are you sure?</h4>
			<p>Are you sure you want to delete the following product(s)?</p>
			<ul>
				<?php foreach ($product_array as $product) {?>
				<li><?=$product->name?></li>
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