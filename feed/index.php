<?php
require('../inc.php');

$csv = '';
$separator = "\t";
$category = "Sunglasses";
$currency = "AUD$";
$shipping = "$0.00";
$website = "http://www.eyestyle.com.au";

$product_list = new record_list('SELECT p.product_id, p.alias, p.name, p.features, p.image_1, p.image_2, p.image_3, p.image_4, p.image_5, p.image_6, p.price, p.category_id_1, b.name AS brand, b.alias AS brand_alias FROM `product` p 
								 INNER JOIN `brand` b ON p.brand_id = b.brand_id', 'WHERE p.status = "active"');
foreach($product_list->get_all() as $product) {
	$image_id = 1;
	$gender = utils_data::get_ancestor($product->category_id_1) == 2 ? 'women' : 'men';
	
	$colour_list = new dbo_list('colour','WHERE `product_id` = '.$product->product_id);
	foreach($colour_list->get_all() as $colour) {
		$lens_list = new dbo_list('lens','WHERE `colour_id` = '.$colour->colour_id);
		foreach($lens_list->get_all() as $lens) {
			$image = 'image_'.$image_id;
			$csv .= (!empty($lens->code) ? $lens->code : 'N/A').$separator;
			$csv .= $product->name.' - '.$colour->name.' (Frame) / '.$lens->name.' (Lens)'.$separator;
			$csv .= implode(", ",explode("\r\n",$product->features)).$separator;
			$csv .= 'http://www.eyestyle.com.au/'.(!empty($product->$image) ? $product->$image : $product->image_1).$separator;
			$csv .= $category.$separator;
			$csv .= $product->brand.$separator;	
			$csv .= 'http://www.eyestyle.com.au/'.$gender.'/'.$product->brand_alias.'/'.$product->alias.'.html'.$separator;
			$csv .= $currency.$separator;
			$csv .= html_text::currency($product->price).$separator;
			$csv .= $shipping.$separator;
			$csv .= $website.$separator;
			$csv .= "\n";
			
			$image_id++;
		}
	}
}

print $csv;
?>