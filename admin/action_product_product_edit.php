<?php
$_ACCESS = 'product.product';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$product = new dbo('product', $_GET['id']);

$form = html_form::get_form('form_product_product_edit');

if (!$form->validate()) {
	$form->set_failure();
}

if ($form->get('category_id_1') == '') {
	$form->set_failure('Please select a category for this product.');
}

if ($product->image_1 == '' && empty($_FILES['image_1']['name'])) {
	$form->set_failure('You must at least upload an image to Image 1');
}


$product->name = $form->get('name');
$product->alias = webpulse::create_alias($product->name, 'product', $product->product_id, true);
$product->parent_category = $form->get('parent_category');
$product->category_id_1 = (int)$form->get('category_id_1');
$product->category_id_2 = (int)$form->get('category_id_2');
$product->brand_id = (int)$form->get('brand_id');
$product->delivery_class_id = (int)$form->get('delivery_id');
$product->sub_heading = $form->get('sub_heading');
$product->price = $form->get('price');
$product->features = $form->get('features');
$product->weight = (int)$form->get('weight');
$product->status = 'active';



////// aus only by victor

if ($form->checked('aus_only', 1)) {
	$product->aus_only = '1';
}
else {
	$product->aus_only = '0';
}

//////////////////////




for ($i=1; $i<=7; $i++) {
	$img = 'image_'.$i;
	if (!empty($_FILES[$img]['name'])) {
		$current = '../'.$product->$img;
		if (is_file($current)) {
			unlink($current);
			deleteThumbnail($current, 'small');
			deleteThumbnail($current, 'medium');
			deleteThumbnail($current, 'large');
		}

		$ext = strtolower(substr(strrchr($_FILES[$img]['name'], '.'), 1 ));
		$filename = 'images/product/'.$product->product_id.'_'.$i.'.'.$ext;
		move_uploaded_file($_FILES[$img]["tmp_name"], '../'.$filename);
		$product->$img = $filename;

		createThumbnail('../'.$product->$img, 'small');
		createThumbnail('../'.$product->$img, 'medium');
		createThumbnail('../'.$product->$img, 'large');
	}
}

$img_delete = $form->get('img_delete');
foreach($img_delete as $id) {
	$img = 'image_'.$id;
	$current = '../'.$product->$img;
	if (is_file($current)) {
		unlink($current);
		deleteThumbnail($current, 'small');
		deleteThumbnail($current, 'medium');
		deleteThumbnail($current, 'large');
	}
	$product->$img = '';
}

$img = 'image_rollover';
if (!empty($_FILES[$img]['name'])) {
	$current = '../'.$product->$img;
	if (is_file($current)) {
		unlink($current);
	}

	$ext = strtolower(substr(strrchr($_FILES[$img]['name'], '.'), 1 ));
	$filename = 'images/product/'.$product->product_id.'_rollover.'.$ext;
	move_uploaded_file($_FILES[$img]["tmp_name"], '../'.$filename);
	$product->$img = $filename;
}

$product->update();

function createBannerImage($feature_product, $banner_path, $new_image=false, $i=0) {
	if (!empty($feature_product->banner)) {
		unlink('../'.$feature_product->banner);
	}
	$ext = strtolower(substr(strrchr($banner_path, '.'), 1 ));
	if ($new_image) {
		$filename = 'images/storefront/'.$i.'.'.$ext;
		move_uploaded_file($_FILES['image_'.$i]["tmp_name"], '../'.$filename);
		$feature_product->banner_path = $filename;
	}
	$product = new dbo('product',$feature_product->product_id);
	$brand = new dbo('brand',$product->brand_id);
	// Add feature product text on banner image
	$bg = '../'.$feature_product->banner_path;
	$image = $ext == 'gif' ? imagecreatefromgif($bg) : imagecreatefromjpeg($bg);
	$color = imagecolorallocate($image, 255, 255, 255);
	// Feature Product
	imagettftext($image, 14, 0, 550, 87, $color, '../font/SYNCHROS.ttf', strtoupper('Feature Product'));
	// Brand Name
	imagettftext($image, 11, 0, 550, 114, $color, '../font/helvetica.ttf', $brand->name);
	// Product Name
	imagettftext($image, 11, 0, 550, 130, $color, '../font/helvetica.ttf', $product->name);
	// More
	imagettftext($image, 12, 0, 550, 160, $color, '../font/SYNCHROS.ttf', "- MORE");
	$path = 'images/storefront/banner_'.$feature_product->feature_products_id.'.'.$ext;
	if ($ext == 'gif') {
		imagegif($image, '../'.$path);
	} else {
		imagejpeg($image, '../'.$path);
	}
	$feature_product->banner = $path;
	return $feature_product;
}
//Banner update
//check whether this product is a feature product, if it is, update the banner as well
//what happen if you change the brand or delete the brand name?
$feature_products = new dbo_list('feature_products','WHERE product_id = '.$product->product_id);
if ($feature_products->count() > 0){
	foreach($feature_products->get_all() as $feature_product){
		$feature_product = createBannerImage($feature_product,$feature_product->banner_path);		
		$feature_product->update();
	}
}


//Stock management here
//Get the updates first
$colour_list = $product->load_children('colour');
foreach($colour_list as $colour){
	$colour->name = $form->get('colour_'.$colour->colour_id);
	if ($colour->update()){
		$lens_list = $colour->load_children('lens');
		foreach($lens_list as $lens){
			$lens_code = $form->get('code_'.$colour->colour_id.'_'.$lens->lens_id);
			$lens_name = $form->get('lens_'.$colour->colour_id.'_'.$lens->lens_id);
			
			if ($lens_name != ''){
				$lens->code = $lens_code;
				$lens->name = $lens_name;
				$lens->update();
			}
		}
	}
	$new_code = $form->get('new_code_'.$colour->colour_id);
	$new_lens = $form->get('new_lens_'.$colour->colour_id);
	if ($new_lens != ''){
		$lens = new dbo('lens');
		$lens->colour_id = $colour->colour_id;
		$lens->code = $new_code;
		$lens->name = $new_lens;
		$lens->insert();
	}

}

if ($form->get('new_colour') != ''){
	$colour = new dbo('colour');
	$colour->product_id = $product->product_id;
	$colour->name = $form->get('new_colour');
	$colour->insert();
}

html_message::add('Product updated successfully.', 'info');
http::redirect(http::get_path());
?>