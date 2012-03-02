<?php
$_ACCESS = 'delivery';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

//Old way: delivery class has a status field

foreach ($ids as $id) {
	$class = new dbo('delivery_class', $id);
	$class->status = 'inactive';
	$class->update();
}

//Need to delete records in delivery_matrix table and set the products to a default delivery class

foreach ($ids as $id){
	$delivery_class = new dbo('delivery_class',$id);
	//Update the product table, set to default delivery class id of 1
	$product_list = $delivery_class->get_children('product');
	foreach($product_list as $product){
		$product->delivery_class_id = 1;
		$product->update();
	}
	$matrix_list = $delivery_class->get_children('delivery_matrix');
	foreach($matrix_list as $matrix){
		$matrix->delete();
	}
	$delivery_class->delete();
}

html_message::add('Class(es) deleted successfully', 'info');
http::redirect(http::get_path());
?>