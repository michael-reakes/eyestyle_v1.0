<?php
$_ACCESS = 'product.product';
$_SECTION = 'Product';
$_PAGE = 'Stock Management';

require_once('inc.php');

http::register_path();

$view_mode = 'all';
$key = '';
if (isset($_GET['bid'])) {
	$view_mode = 'brand';
	$bid = $_GET['bid'];
}
if (isset($_GET['cid'])) {
	$view_mode = 'cat';
	$cid = $_GET['cid'];
}
if (isset($_GET['key'])) {
	$view_mode = 'search';
	$key = $_GET['key'];
}

switch ($view_mode) {
	case 'all':
		$product_list = new dbo_list('product','WHERE `status` = "active"');
		break;
	case 'cat':
		$current = new dbo('category', $cid);
		$cat_breadcrumbs = array($current->name=>'');
		while ($current->parent_id != 0) {
			$parent = new dbo('category', $current->parent_id);
			$cat_breadcrumbs[$parent->name] = $_SERVER['PHP_SELF'].'?'.http::build_query($_GET, array('cid','key')).'&cid='.$parent->category_id;
			$current = $parent;
		}
		$cat_breadcrumbs = array_reverse($cat_breadcrumbs);
		$subcat_list = new dbo_list('category', 'WHERE `parent_id` = '.$cid, 'name');
		$product_list = new dbo_list('product', 'WHERE `status` = "active" AND (`category_id_1` = '.$cid.' OR `category_id_2` = '.$cid.')');
		break;
	case 'search':
		$product_list = new dbo_list('product', 'WHERE `status` = "active" AND `sub_heading` LIKE "%'.$key.'%" OR `name` LIKE "%'.$key.'%"');
		break;
	case 'brand':
		$product_list = new dbo_list('product', 'WHERE `status` = "active" AND `brand_id` = "'.$bid.'"');
		break;
}

$len = $product_list->count();
$pager = new html_pager($product_list, array('code'=>'Code', 'name'=>'Product Name','price'=>'Price'),'a');

$brand_list = new dbo_list('brand','','name');
$brand_options = array();
foreach($brand_list->get_all() as $brand) {
	$brand_options[$brand->brand_id] = $brand->name;
}

$form_brand = new html_form('form_store_stock_brand', $_SERVER['PHP_SELF'],'GET');
$form_brand->add(new html_form_select('bid',$brand_options,'-- select a brand --',true,false,'',(isset($bid) ? $bid : '')));
$form_brand->add(new html_form_button('submit','Go'));
$form_brand->register();

$form = new html_form('form_store_stock', 'action_product_stock.php');
$lens_list = new dbo_list('lens');
foreach($lens_list->get_all() as $lens){
	$form->add(new html_form_text($lens->lens_id, false, $lens->quantity,'','',10,10));
}

$pids = '';
foreach ($pager->get_page() as $product) {
	$colours = $product->load_children('colour');
	foreach($colours as $colour){
		$lenses = $colour->load_children('lens');
		foreach ($lenses as $lens){
			$form->add(new html_form_text('lens_'.$lens->lens_id,true,$lens->quantity,'align_center',false,5,5));
			$pids .= $lens->lens_id.'|';
		}
	}
}
$form->add(new html_form_hidden('pids', $pids));

$form->add(new html_form_button('submit_update', 'Update', '', 'submit', true));

$form->register();

$form_search = new html_form('form_search', $_SERVER['PHP_SELF'], 'GET');
$form_search->add(new html_form_text('key', false, $key));
$form_search->add(new html_form_image_button('submit', 'images/icon_search.gif', '', 'icon_btn'));

$breadcrumbs = array('Home'=>'./', 'Product Management'=>'product_product.php', $_PAGE=>'');

function num_products_in_category($cid) {
	$product_list = new dbo_list('product', 'WHERE (`category_id_1` = "'.$cid.'" OR `category_id_2` = "'.$cid.'")  AND `status` = "active"');
	$num = $product_list->count();
	$category_list = new dbo_list('category', 'WHERE `parent_id` = "'.$cid.'"');
	foreach ($category_list->get_all() as $category) {
		$num += num_products_in_category($category->category_id);
	}
	return $num;
}


require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_product.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title">
			<div class="search">
				Search by product name or code: 
				<?=$form_search->output_open()?>
				<?=$form_search->output('key')?>
				<?=$form_search->output('submit')?>
				<?=http::hidden_fields($_GET, array('cid','key'))?>
				<?=$form_search->output_close()?>
			</div>
			<?=$_PAGE?>
		</div>
		<?=html_message::show()?>
		<div class="band">
			<div class="float_right">
				<?=$form_brand->output_open()?>
				Browse by Brand: <?=$form_brand->output('bid')?> <?=$form_brand->output('submit')?>
				<?=$form_brand->output_close()?>
			</div>
			<?php if ($view_mode == 'all') { ?>
				Show All
			<?php } else { ?>
				<a href="<?=$_SERVER['PHP_SELF'].'?'.http::build_query($_GET, array('cid','key'))?>">Show All</a>
			<?php } ?> | 
			<a href="<?=$_SERVER['PHP_SELF'].'?'.http::build_query($_GET, array('cid','key'))?>&cid=0">Browse by categories</a> :
			<?=$view_mode == 'cat' ? html::cat_breadcrumb($cat_breadcrumbs) : ''?>
		</div>
		<?php if ($view_mode == 'cat' && $subcat_list->count() > 0) { ?>
			<div class="category">
				<?php
				foreach ($subcat_list->get_all() as $subcat) {
					$num_product = num_products_in_category($subcat->category_id);
				?>
				<div class="item"><a href="<?=$_SERVER['PHP_SELF'].'?'.http::build_query($_GET, 'cid')?>&cid=<?=$subcat->category_id?>"><?=$subcat->name?></a> (<?=$num_product?>)</div>
				<?php } ?>
				<div class="clear_both"></div>
			</div>
		<?php } ?>
		<?=$pager->show()?>
		<?=$form->output_open()?>
		<div class="band"><?=$form->output('submit_update')?></div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
			<tr class="table_heading" style="font-weight:bold">
				<th width="75px">Stock</th>
				<?=$pager->column('code')?>
				<?=$pager->column('name')?>
				<?=$pager->column('price')?>
			</tr>
			<?php if ($len == 0 ) { ?>
			<tr class="table_row">
				<td colspan="6" align="center">There are no products</td>
			</tr>
			<?php } else {
				foreach ($pager->get_page() as $product) {
			?>
				<tr class="table_row_colour" style="border-top:solid 2px #fff">
					<td></td>
					<td align="center"><a href="product_product_edit.php?id=<?=$product->product_id?>" title="Edit Product"><?=empty($product->code)?'&nbsp':$product->code?></a></td>
					<td align="center"><a href="product_product_edit.php?id=<?=$product->product_id?>" title="Edit Product"><?=$product->name?></a></td>
					<td align="center"><a href="product_product_edit.php?id=<?=$product->product_id?>" title="Edit Product"><?=html_text::currency($product->price)?></a></td>
				</tr>
				<? 
					$colours = $product->load_children('colour');
					foreach($colours as $colour){
				?>
						<tr class="table_row_product">
							<td><b>Colour:</b></td>
							<td colspan="3" align="left"><?=$colour->name?></td>
						</tr>
				<?
						$lenses = $colour->load_children('lens');
						foreach($lenses as $lens){
				?>
							<tr class="table_row">
								<td><?=$form->output('lens_'.$lens->lens_id)?></td>
								<td colspan="3" align="left"> <?=$lens->name?> </td>
							</tr>
				<?		
						} 
					}
				?>

			<?php }
				}
			?>
		</table>
		<div class="band"><?=$form->output('submit_update')?></div>
		<?=$form->output_close()?>
		<?=$pager->show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>