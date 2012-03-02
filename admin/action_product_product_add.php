<?php
$_ACCESS = 'product.product';

require_once('inc.php');

$form = html_form::get_form('form_product_product_add');

if (!$form->validate()) {
	$form->set_failure();
}

if ($form->get('category_id_1') == '') {
	$form->set_failure('Please select a category for this product.');
}

if (empty($_FILES['image_1']['name'])) {
	$form->set_failure('You must at least upload an image to Image 1');
}

$product = new dbo('product');
$product->name = $form->get('name');
$product->alias = webpulse::create_alias($product->name, 'product', false, true);
$product->parent_category = (int)$form->get('parent_category');
$product->category_id_1 = (int)$form->get('category_id_1');
$product->category_id_2 = (int)$form->get('category_id_2');
$product->brand_id = (int)$form->get('brand_id');
$product->delivery_class_id = (int)$form->get('delivery_id');
$product->sub_heading = $form->get('sub_heading');
$product->price = $form->get('price');
$product->features = $form->get('features');
$product->status = 'active';
$product->weight = (int)$form->get('weight');

////// aus only by victor

if ($form->checked('aus_only', 1)) {
	$product->aus_only = '1';
}
else {
	$product->aus_only = '0';
}

//////////////////////


$product->insert();

for ($i=1; $i<=7; $i++) {
	$img = 'image_'.$i;
	if (!empty($_FILES[$img]['name'])) {
		$ext = strtolower(substr(strrchr($_FILES[$img]['name'], '.'), 1 ));
		$filename = 'images/product/'.$product->product_id.'_'.$i.'.'.$ext;
		move_uploaded_file($_FILES[$img]["tmp_name"], '../'.$filename);
		$product->$img = $filename;

		createThumbnail('../'.$product->$img, 'small');
		createThumbnail('../'.$product->$img, 'medium');
		createThumbnail('../'.$product->$img, 'large');
	}
}

$img = 'image_rollover';
if (!empty($_FILES[$img]['name'])) {
	$ext = strtolower(substr(strrchr($_FILES[$img]['name'], '.'), 1 ));
	$filename = 'images/product/'.$product->product_id.'_rollover.'.$ext;
	move_uploaded_file($_FILES[$img]["tmp_name"], '../'.$filename);
	$product->$img = $filename;
}

$product->update();

//add colour and lens
for ($x=1; $x<=3; $x++){
	$colour = new dbo('colour');
	$colour->product_id = $product->product_id;
	$colour_name = $form->get('colour_'.$x);
	if ($colour_name != ''){
		$colour->name = $colour_name;
		if ($colour->insert()){
			for ($i=1; $i<=3; $i++){
				$lens = new dbo('lens');
				$lens->colour_id = $colour->colour_id;
				$lens_code = $form->get('code_'.$x.'_'.$i);
				$lens_name = $form->get('lens_'.$x.'_'.$i);
				if ($lens_name != ''){
					$lens->code = $lens_code;
					$lens->name = $lens_name;
					$lens->insert();
				}
			}
		}
	}
}

html_message::add('Product created successfully.', 'info');
http::redirect('product_product.php');
?>