<?php
$_ACCESS = 'product.category';
$_SECTION = 'Product';
$_PAGE = 'Delete Category(s)';

require_once('inc.php');

//http::halt_if(!isset($_GET['id']));
if (!isset($_GET['id'])){
	echo "helo";exit;
}

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

$category_array = array();
foreach ($ids as $id) {
	if ($id == 1 || $id == 2){
		html_message::add('You are not allowed to delete the main categories (Male and Female)');
		http::redirect('product_categories.php');
	}
	$category_array[] = new dbo('category', $id);
}

$form = new html_form('form_category_delete', 'action_product_category_delete.php?'.http::build_query($_GET));

$form->add(new html_form_button('delete', 'Delete'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Product Management'=>'product_product.php', 'Category Management'=>'product_categories.php', $_PAGE=>'');

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
			<p>
				All categories in this category will be delete.<br />
				<b>All products</b> in this category will also be deleted.<br />
				Please move them to another category before deleting if you would like to keep these categories/products.
			</p>
			<p>Are you sure you want to delete the following category(s)?</p>
			<ul>
				<?php foreach ($category_array as $category) {?>
				<li><?=$category->name?></li>
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