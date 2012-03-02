<?php
require_once('inc.php');

$_SECTION = 'Shop';
$_PAGE = 'Product Search';

if (!isset($_GET['term']) || strlen($_GET['term']) < 2) {
	$invalid = true;
	$search_len = 0;
	html_message::add('Your search must be at least 2 characters long.');
	$result = 0;
} else {
	$invalid = false;
	$key = $_GET['term'];
	$condition = 'product.name LIKE "%'.$key.'%" OR product.code LIKE "%'.$key.'%" OR product.sub_heading LIKE "%'.$key.'%"';
	//$condition .= ' OR brand_id.name LIKE "%'.$key.'%"';
	$condition .= ' OR brand.name LIKE "%'.$key.'%"';
	$condition .= ' OR colour.name LIKE "%'.$key.'%"';
	$condition .= ' OR lens.name LIKE "%'.$key.'%"';
	//$search_list = new dbo_list('product', 'WHERE product.status = "active" AND ('.$condition.')','name','ASC',true);
	//$condition .= ' OR product.brand_id = (SELECT brand_id FROM brand WHERE name LIKE "%'.$key.'%")';
	//$search_list = new dbo_list('product', 'WHERE product.status = "active" AND ('.$condition.')');
	$select_clause = "SELECT brand.name AS brand, product.* FROM product product LEFT JOIN colour colour ON (product.product_id = colour.product_id) LEFT JOIN lens lens ON (colour.colour_id = lens.colour_id) LEFT JOIN brand brand ON (brand.brand_id = product.brand_id)";
	$where_clause = 'WHERE product.status = "active" AND ('.$condition.') GROUP BY product.product_id';
	$search_list = new record_list($select_clause,$where_clause);

	$men_list = array();
	$women_list = array();
	$data_getter = new utils_data();
	foreach($search_list->get_all() as $product){
		$men_list[] = $product;
	}
	$search_len = $search_list->count();
	$result = $search_len;
	$term = $_GET['term'];
}



require_once('inc_header.php');
?>

	
		<div id="content" class="clearfix">
			<? require_once('inc_category_lcolumn.php'); ?>
			<div class="rcolumn">
				<div class="breadcrumb">
					<ul>
						<li><a href="home.php">Home</a></li>
						<li>/</li>
						<li class="selected">search results</li>
					</ul>
				</div>
				<? html_message::show() ?>
				<? if ($result <= 0) { ?>
				There are no matching results for your search criteria. 
				<? } else { ?>
				
				<ul class="productlist clearfix">
				<?
					$i = 1;
					foreach($men_list as $product){
						if ($i % 4 == 0) {
							$class = "last";
							$clear = "<li class=\"clear\" />";
						}
						else {
							$class = "";
							$clear = "";
						}
						$url = url::linkProduct($product);
				?>
					<li class=<?=$class?>>
						<a href="<?=$url?>"><img src="<?=$_ROOT.getMediumThumbnail($product->image_1)?>" alt="<?=$product->name?>" width="175" height="86"<?=$product->image_rollover != '' ? ' rel="'.$_ROOT.$product->image_rollover.'"' : ''?> /></a>
						<h4><a href="<?=$url?>"><?=$product->name?></a></h4>
						<h5 class="price"><?=html_text::currency($product->price)?></h5>
					</li>
					<?=$clear?>
				<?
						$i++;
					}
				?>
				</ul>
				
				<?
				}
				?>
			</div>
		</div>
	
	
	
	<?php require_once('inc_footer.php'); ?>