<?php
	
	
	require_once('inc.php');
	/*
	$lens_list = new dbo_list('lens');
	foreach ($lens_list->get_all() as $lens){
		$lens->update();
	}*/
	
	$product_list = new dbo_list('product');
	$counter = 0;
	foreach ($product_list as $product){
		$product->delivery_class_id = 1;
		$product->update();
		$counter++;
	}

	echo $counter.' products have been updated';
?>