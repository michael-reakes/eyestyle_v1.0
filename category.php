<?php
require_once('inc.php');

http::halt_if(!isset($_GET['lvl0']));
$lvl0 = $_GET['lvl0'];
$lvl1 = isset($_GET['lvl1']) ? $_GET['lvl1'] : false;
$lvl2 = isset($_GET['lvl2']) ? $_GET['lvl2'] : false;

if ($lvl2) {
	$lvl = $lvl2;
} else if ($lvl1) {
	$lvl = $lvl1;
} else {
	$lvl = $lvl0;
}

$category_list = new dbo_list('category','WHERE `alias` = "'.$lvl.'"');
http::halt_if(!($category = $category_list->get_first()));

$cid = $category->category_id;

switch($_GET['gender']) {
	case 'womens':
		$_SESSION['gender'] = 'womens';
		$genderid = 2;
		$breadcrumb = array('home'=>'./','womens'=>'');	
		break;
	default:
		$_SESSION['gender'] = 'men';
		$genderid = 1;
		$breadcrumb = array('home'=>'./','mens'=>'');
		break;
}

$_SELECTED_CATEGORYID = $cid;

$cat_chain = array(strtolower($category->name)=>'');
$parent_id = $category->parent_id;

// Create a counter to avoid infinite loop - max 10 times
$i = 1;
while ($parent_id != 0 && $i < 10) {
	$parentCategory = new dbo('category',$parent_id);
	if ($parentCategory->parent_id != 0) {
		$cat_chain[strtolower($parentCategory->name)] = url::linkCategory($parentCategory);
		$parent_id = $parentCategory->parent_id;
	} else {
		break;
	}
	$i++;
}
$cat_chain = array_reverse($cat_chain);
$breadcrumb = array_merge($breadcrumb, $cat_chain);

$_TITLE = strtoupper('Eyestyle - Designer Sunglasses | Category - '.$category->name);

//sorting function
if (isset($_GET['sort'])) {
	$sort = $_GET['sort'];
}
else {
	$sort = 0;
}

if (($sort == "") || ($sort < 0) || ($sort > 3)) {
	$sort = 0;
}

$sort_class = array("","","","");
$sort_class[$sort]="selected";
switch ($sort) {
	case 0: //name
	default:
		$sort_field = 'b.name';
		$sort_label = 'Name';
		$sort_order = 'a';
		break;
	case 1: //new item
		$sort_field = 'p.product_id';
		$sort_label = 'Product ID';
		$sort_order = 'd';
		break;
	case 2: //price
		$sort_field = 'p.price';
		$sort_label = 'Price';
		$sort_order = 'a';
		break;
}
$product_list = new record_list('SELECT p.* FROM `product` p 
								 INNER JOIN `brand` b ON b.brand_id = p.brand_id ', 
								 'WHERE p.status = "active" 
									AND (p.category_id_1 = '.$cid.' OR p.category_id_2 = '.$cid.') 
									AND p.parent_category IN ('.(int)$genderid.', 0)');

$pager = new html_pager($product_list,array($sort_field=>$sort_label),$sort_order);

$banner = $genderid == 2 ? $category->image_women : $category->image_men;
if (!is_file($_LOCAL.$banner)) $banner = 'images/banner_inner.jpg';

require_once('inc_header.php');
?>

			<div id="content" class="clearfix">
			<? require_once('inc_category_lcolumn.php'); ?>
			<div class="rcolumn">
				<?=html::breadcrumb($breadcrumb)?>
				<? $category = new dbo('category',$cid); ?>
				<p><img src="<?=$_ROOT.$banner?>" alt="<?=basename($banner)?>" width="760" height="300" /></p>
				<?php if ($product_list->count() > 0) { ?>
				
				<div class="breadcrumb">
					<ul class="filters">
						<li class="label">sort by:</li>
						
						<li class="<?=$sort_class[0]?>"><a href="<?=url::linkCategory($cid,$_SESSION['gender']).'?sort=0'?>">designer</a></li>
						<li class="<?=$sort_class[1]?>"><a href="<?=url::linkCategory($cid,$_SESSION['gender']).'?sort=1'?>">latest items</a></li>
						<li class="<?=$sort_class[2]?>"><a href="<?=url::linkCategory($cid,$_SESSION['gender']).'?sort=2'?>">price</a></li>
				
					</ul>
					</div>
					<div class="controls clearfix">
					<?=$pager->total_page > 1 ? $pager->show() : ''?>
				
				</div>
				<ul class="productlist clearfix">

					<?	
						$i = 0;
						foreach($pager->get_page() as $product){
							$i++;
							if ($i % 4 == 0) {
								$class = ' class="last"';
								$clear = "<li class='clear' />";
							}
							else {
								$class = "";
								$clear = "";
							}
					?>
					<li<?=$class?>>
						<a href="<?=url::linkProduct($product)?>"><img src="<?=$_ROOT.getMediumThumbnail($product->image_1)?>" alt="<?=$product->name?>" width="175" height="86"<?=$product->image_rollover != '' ? ' rel="'.$_ROOT.$product->image_rollover.'"' : ''?> /></a>
						<h4><a href="<?=url::linkProduct($product)?>"><?=$product->name?></a></h4>
						<h5 class="price"><?=html_text::currency($product->price)?></h5>
					</li>
					<?=$clear?>
					<? } ?>
					<li class="clear" />
				</ul>
				<?	if ($pager->total_page > 1) { ?>
				<div class="controls bottomcontrols clearfix">
					<?=$pager->show()?>
				</div>
				<?	} ?>
				<?php } else { ?>
				<p>There are no products in this category.</p>
				<?php } ?>
			</div>
		</div>
	
	
	
	<?php require_once('inc_footer.php'); ?>