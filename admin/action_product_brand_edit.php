<?php
$_ACCESS = 'product.brand';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$brand = new dbo('brand', $_GET['id']);

$form = html_form::get_form('form_product_brand_edit');

if (!$form->validate()) {
	$form->set_failure();
}

$brand->name = $form->get('name');
$brand->alias = webpulse::create_alias($brand->name, 'brand', $brand->brand_id);

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

if ($brand->update()){
	html_message::add('Brand updated successfully.', 'info');
}
else{
	$form->set_failure('Sorry the brand could not be updated. The server is either down or busy at the moment. Please try again later.');
}
http::redirect('product_brand.php');
?>