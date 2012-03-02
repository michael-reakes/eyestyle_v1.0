<?php
$_ACCESS = 'product.category';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

$category_array = array();
foreach ($ids as $id) {
	$category_array[] = new dbo('category', $id);
}

function delete_products_categories($category) {
	$parent_id = $category->category_id;

	$product_list = new dbo_list('product','WHERE `status` = "active" AND `category_id` = "'.$parent_id.'"');
	foreach($product_list->get_all() as $product) {
		$product->status = "inactive";
		$product->update();
	}

	$images = array('image_men', 'image_women');
	foreach($images as $image)
		if (is_file('../'.$category->$image)) unlink('../'.$category->$image);

	$category->delete();

	$category_list = new dbo_list('category','WHERE `parent_id` = "'.$parent_id.'"');
	if ($category_list->count() > 0) {
		foreach($category_list->get_all() as $category) {
			delete_products_categories($category);
		}
	}
}

foreach ($category_array as $category) {
	delete_products_categories($category);
}

html_message::add('Category(s) deleted successfully', 'info');
http::redirect(http::get_path());