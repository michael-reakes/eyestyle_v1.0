<?php
$_ACCESS = 'product.brand';

require_once('inc.php');

$form = html_form::get_form('form_product_brand_add');

if (!$form->validate()) {
	$form->set_failure();
}

$brand = new dbo('brand');
$brand->name = $form->get('name');
$brand->alias = webpulse::create_alias($brand->name, 'brand');

$images = array('image_men', 'image_women');
foreach($images as $key) {
	if (isset($_FILES[$key])) {
		$delete = isset($_POST[$key.'_delete']);
		$uploaded = is_uploaded_file($_FILES[$key]['tmp_name']);
		if ( ($delete || $uploaded)
				&& is_file('../'.$brand->$key) ) {
			unlink('../'.$brand->$key);
		}

		if ($delete) {
			$brand->$key= '';
		} else if ($uploaded) {
			$fileinfo = pathinfo($_FILES[$key]['name']);
			$dirName = 'images/brand/';
			$basename = $dirName.$fileinfo['basename'];
			$i = 1;
			while(is_file('../'.$basename)) {
				$basename = $dirName.$fileinfo['filename'].$i.'.'.$fileinfo['extension'];
				$i++;
			}
			move_uploaded_file($_FILES[$key]["tmp_name"], '../'.$basename);

			$brand->$key = $basename;
		}
	}
}

if ($brand->insert()){
	html_message::add('Brand created successfully.', 'info');
	http::redirect('product_brand.php');
}
else{
	html_message::add('An error occured.', 'info');
	http::redirect('product_brand.php');	
}
?>