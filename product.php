<?php
require_once('inc.php');
$_PAGE = 'product display';
if (!isset($_GET['alias'])){
	http::redirect('home.php');
}
$alias = $_GET['alias'];
http::halt_if(empty($alias));

$valid_genders = array('men','women');
$urlGender = isset($_GET['gender']) ? str_replace("s","",$_GET['gender']) : 'men';
if (!in_array($urlGender, $valid_genders)) $urlGender = 'men';
if (isset($_SESSION['gender']) && $_SESSION['gender'] != $urlGender) $_SESSION['gender'] = $urlGender;

$product_list = new dbo_list('product','WHERE `alias` = "'.$alias.'" AND `status` != "inactive"');
$product = $product_list->get_first();
http::halt_if(!$product);

$pid = $product->product_id;

if ($_SESSION['gender'] == 'women') $cid = 2;
else $cid = 1;

http::halt_if($product->status != 'active');

$brand = new dbo('brand',$product->brand_id);

// used in left column
$_SELECTED_BRANDID = $brand->brand_id;
$_SELECTED_GENDERID = $urlGender == 'women' ? 2 : 1;

$form_cart = new html_form('form_cart',$_ROOT.'action_cart_add.php');
$form_cart->add(new html_form_hidden('id',$pid));
$form_cart->add(new html_form_hidden('qty',1));

$colourlens_menu  = "<select name='colour' id='colour'>";
$colourlens_menu .= "<option selected='selected' value='-1'>- Please select-</option>";

$colours = $product->load_children('colour');
foreach($colours as $colour){
	$lenses = $colour->load_children('lens');
	foreach($lenses as $lens){
		$colourlens_menu .= "<option value='".$colour->colour_id.'_'.$lens->lens_id."'>".$colour->name." / ".$lens->name."</option>";
	}
}
$colourlens_menu .= "</select>";


$form_cart->add(new html_form_image_button('btn_add',$_ROOT.'images/btn/add_to_cart.gif','Add to Cart','input_image'));
$form_cart->register();

$breadcrumb = array('home'=>'./', 'designers'=>'', strtolower($brand->name) => ($cid == 2 ? 'womens' : 'mens').'/designers/'.$brand->alias.'/', strtolower($product->name)=>'');

$javascript = 'onload="MM_preloadImages('; 
$images = array();
for( $i = 1; $i <= 6; $i++){ 
	$image = 'image_'.$i;
	$images[] = "'".getLargeThumbnail($product->$image)."'";
}
$javascript .= implode(', ', $images);
$javascript .= ');"';

$_TITLE = 'Eyestyle - Designer Sunglasses | '.strtolower($_SESSION['gender'].' | '.$brand->name.' | '.$product->name);

require_once('inc_header.php');
?>

	
	
			<div id="content" class="clearfix">
			<? require_once('inc_category_lcolumn.php'); ?>
			<div class="rcolumn">
				<?=html::breadcrumb($breadcrumb)?>
				<p><img src="<?=$_ROOT.getLargeThumbnail($product->image_1)?>" alt="<?=$product->name?>" width="760" height="376" name="img_main_product" id="img_main_product" /></p>
				<h6 class="small">views</h6>
				<ul class="thumbnails clearfix">
					<?
						for ($i=1 ; $i <= 7; $i++){
							$image = "image_".$i;
							
							if ($product->$image == ''){
								$image_path = $_ROOT."images/no_image.gif";
							}else{
								$image_path = getLargeThumbnail($product->$image);
							}
							
							if ($product->$image != '') {
					?>
					<li<?=$i == 7 ? ' class="last"' : ''?>><a href="javascript:showProductImage('<?=$_ROOT?>','<?=$image_path?>','<?=$image?>');"><img src="<?=$_ROOT.getSmallThumbnail($product->$image)?>" alt="<?=$product->name?>" name="<?=$image?>" id="<?=$image?>" width="99" height="49" /></a></li>
					<?
								if ($i == 1) {
					?>
					<script language="javascript" type="text/javascript">showProductImage('<?=$_ROOT?>', '<?=getLargeThumbnail($product->image_1)?>','img_1');</script>
					<?
								}
							}
						}
					?> 
					
					
				</ul>
				<div class="productdesc">
					<?php if (!empty($product->sub_heading)) { ?>
					<h3><?=$product->sub_heading?></h3>
					<?php } ?>
					<ul>
						<?=html_text::bullet($product->features)?>
					</ul>
					<?
						if ($product->aus_only) {
					?>
					<span class="aus">This is an Australian only product</span>
					<?
						}
					?>
				</div>
				<?php if (count($colours) > 0): ?>
				<?=$form_cart->output_open()?>
				<?=$form_cart->output('id')?>
				<?=$form_cart->output('qty')?>
				<h5><?=html_text::currency($product->price)?></h5>
				<p><?=$colourlens_menu?></p>
				<?=html_message::show()?>

				<button type="submit" value="Add to shopping bag">Add to shopping bag</button>
				<?
				/*
				<label for="share">Share:</label> 
				<a href=""><img src="<?=$_ROOT?>images/icons/facebook.jpg" alt="Facebook" width="16" height="16" /></a>
				<a href=""><img src="<?=$_ROOT?>images/icons/twitter.jpg" alt="Twitter" width="16" height="16" /></a>
				<a href=""><img src="<?=$_ROOT?>images/icons/email.jpg" alt="Email" width="16" height="16" /></a>
				*/
				?>
				<?=$form_cart->output_close()?>
				<?php endif; ?>
			</div>
		</div>
	
	
	
	
	
	
	
	
	
	
	<?php require_once('inc_footer.php'); ?>