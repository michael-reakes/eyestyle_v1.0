<?php
require_once('inc.php');

// Use gender from URL as reference
switch($_GET['gender']) {
	case 'womens':
		$_SESSION['gender'] = 'womens';
		$breadcrumb = array('home'=>'./','womens'=>'women/');
		$genderid = 2;
		break;
	default:
		$_SESSION['gender'] = 'men';
		$breadcrumb = array('home'=>'./','mens'=>'men/');
		$genderid = 1;
		break;
}

if (isset($_GET['alias'])){
	$alias = $_GET['alias'];
	if ($alias == '') http::redirect('home.php');
	$brand_list = new dbo_list('brand','WHERE `alias` = "'.$alias.'"');
} else {
	//default is to get the first brand of the gender
	$brand_list = new dbo_list('brand','','name');
}

$brand = $brand_list->get_first();
if (!$brand) http::redirect('home.php');

$bid = $brand->brand_id;

// used in left column
$_SELECTED_BRANDID = $bid;
$_SELECTED_GENDERID = $genderid;

//sorting function
if (isset($_GET['sort'])) {
	$sort = $_GET['sort'];
}
else {
	$sort = 1;
}

if (($sort == "") || ($sort < 1) || ($sort > 3)) {
	$sort = 1;
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
									AND b.brand_id = '.$bid.' 
									AND p.parent_category IN ('.(int)$genderid.', 0)');


$pager = new html_pager($product_list,array($sort_field=>$sort_label),$sort_order);
$breadcrumb = array('home'=>'./', 'designers'=>'', ($genderid == 2 ? 'womens' : 'mens') => '', strtolower($brand->name)=>'');

$_TITLE = strtoupper('Eyestyle - Designer Sunglasses | Designer - '.$brand->name);

$banner = $genderid == 2 ? $brand->image_women : $brand->image_men;
if (!is_file($_LOCAL.$banner)) $banner = 'images/banner_inner.jpg';

require_once('inc_header.php');
?>
			<div id="content" class="clearfix">
				<? require_once('inc_category_lcolumn.php'); ?>
				<div class="rcolumn">
					<?=html::breadcrumb($breadcrumb)?>
					<p><img src="<?=$_ROOT.$banner?>" alt="<?=basename($banner)?>" width="760" height="300" /></p>
					<? if ($product_list->count() > 0) { ?>
					<div class="breadcrumb">
						<ul class="filters">
							<li class="label">sort by:</li>
							<li class="<?=$sort_class[1]?>"><a href="<?=url::linkBrand($bid,$_SESSION['gender']).'?sort=1'?>">latest items</a></li>
							<li class="<?=$sort_class[2]?>"><a href="<?=url::linkBrand($bid,$_SESSION['gender']).'?sort=2'?>">price</a></li>
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
									$clear = "<li class=\"clear\" />";
								}
								else {
									$class = "";
									$clear = "";
								}
								$url = url::linkProduct($product);
						?>
						<li<?=$class?>>
							<a href="<?=$url?>"><img src="<?=$_ROOT.getMediumThumbnail($product->image_1)?>" alt="<?=$product->name?>" width="175" height="86"<?=$product->image_rollover != '' ? ' rel="'.$_ROOT.$product->image_rollover.'"' : ''?> /></a>
							<h4><a href="<?=$url?>"><?=$product->name?></a></h4>
							<h5 class="price"><?=html_text::currency($product->price)?></h5>
						</li>
						<?=$clear?>
						<? } ?>
					</ul>
					<?	if ($pager->total_page > 1) { ?>
					<div class="controls bottomcontrols clearfix">
						<?=$pager->show()?>
					</div>
					<?	} ?>
					<? } else { ?>
					<p>There are no products by this designer.</p>
					<? } ?>
				</div>
			</div>

<?php require_once('inc_footer.php'); ?>