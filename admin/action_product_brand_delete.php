<?php
$_ACCESS = 'product.brand';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

foreach ($ids as $id) {
	$brand = new dbo('brand', $id);
	$products = $brand->load_children('product');
	foreach($products as $product){
		$product->status = 'inactive';
		//colour and lense also not active?
		$product->update();
	}
	$images = array('title', 'image_men', 'image_women');
	foreach($images as $image)
		if (is_file('../'.$brand->$image)) unlink('../'.$brand->$image);

	$brand->delete();
}

html_message::add('Brand(s) deleted successfully', 'info');
http::redirect(http::get_path());
?>