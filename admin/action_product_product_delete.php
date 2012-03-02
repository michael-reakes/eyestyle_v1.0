<?php
$_ACCESS = 'product.product';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

foreach ($ids as $id) {
	$product = new dbo('product', $id);
	for ($i=1; $i<=10; $i++) {
		$img = 'image_'.$i;
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
	$current = '../'.$product->$img;
	if (is_file($current)) {
		unlink($current);
	}
	$product->image_rollover = '';
	$product->status = 'inactive';
	$product->update();

	/* Need to find out whether this a feature product of a category and update
	the category accordingly!
	*/

	$category = new dbo('category',$product->category_id);
	if ($category->feature_1 == $id){
		$category->feature_1 = 0;
		$category->update();
	}else if ($category->feature_2 == $id){
		$category->feature_2 = 0;
		$category->update();
	}
}

html_message::add('Product(s) deleted successfully', 'info');
http::redirect(http::get_path());