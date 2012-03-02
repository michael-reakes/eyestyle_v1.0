<?php
$_ACCESS = 'delivery';

require_once('inc.php');

$class_list = new dbo_list('delivery_class', '');
$classes = $class_list->get_all();

$zone_list = new dbo_list('zone');
$zones = $zone_list->get_all();

$form = html_form::get_form('form_system_delivery_matrix');

foreach ($classes as $class) {
	foreach ($zones as $zone) {
		$matrix_list = new dbo_list('delivery_matrix', 'WHERE `delivery_class_id` = "'.$class->delivery_class_id.'" AND `zone_id` = "'.$zone->zone_id.'"');
		$matrix = $matrix_list->get_first();
		if ($matrix) {
			$matrix->price = $form->get('price_'.$class->delivery_class_id.'_'.$zone->zone_id);
			$matrix->update();
		}//insertion? will this ever happen? 
		else {
			$matrix = new dbo('delivery_matrix');
			$matrix->delivery_class_id = $class->delivery_class_id;
			$matrix->zone_id = $zone->zone_id;
			$matrix->price = $form->get('price_'.$class->delivery_class_id.'_'.$zone->zone_id);
			if (!$matrix->insert()){
				echo "something wrong with insertion";exit;
			}
		}
	}
}

html_message::add('Delivery matrix updated successfully', 'info');
http::redirect(http::get_path());
?>