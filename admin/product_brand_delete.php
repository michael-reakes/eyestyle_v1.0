<?php
$_ACCESS = 'product.brand';
$_SECTION = 'Product';
$_PAGE = 'Delete Designer(s)';

require_once('inc.php');

if (isset($_GET['id']) && is_array($_GET['id'])) {
	$brand_array = array();
	foreach ($_GET['id'] as $id) {
		$brand_array[] = new dbo('brand', $id);
	}
} else {
	http::halt();
}

$form = new html_form('form_product_brand_delete', 'action_product_brand_delete.php?'.http::build_query($_GET));

$form->add(new html_form_button('delete', 'Delete'));
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
		<?=$form->output_open()?>
		<div class="info">
			<h4>Are you sure?</h4>
			<b>All products</b> associated with this designer will also be deleted.<br />
			Please move the products to other designer before deleting if you would like to keep these products.
			<ul>
				<?php foreach ($brand_array as $brand) {?>
				<li><?=$brand->name?></li>
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