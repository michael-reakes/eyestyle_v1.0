<?php

class checkout_delivery {

	function postcode_to_zone($pc) {
		$postcode_list = new dbo_list('postcode', 'WHERE `code` = "'.$pc.'"');
		$postcode = $postcode_list->get_first();
		$domestic_zone = new dbo('post_zone', $postcode->post_zone_id);
		return $domestic_zone->zone_id;
	}

	function country_to_zone($code){
		$country_list = new dbo_list('country','WHERE `code` = "'.$code.'"');
		$country = $country_list->get_first();
		return $country->zone_id;
	}

	function calculate($zone_id, $total_weight) {
		/* WARNING: THIS IS A HACK WITH ASSUMPTION ONLY ONE DELIVERY CLASS USED BY EYESTYLE */
		$total_weight = 10; //HACK!
		
		$class_list = new dbo_list('delivery_class', 'WHERE `weight` >= "'.$total_weight.'"', 'weight', 'ASC');
		$class = $class_list->get_first();

		if ($class === false) {
			$class_list = new dbo_list('delivery_class', '', 'weight', 'DESC');
			$class = $class_list->get_first();
		}
		$matrix_list = new dbo_list('delivery_matrix', 'WHERE `delivery_class_id` = "'.$class->delivery_class_id.'" AND `zone_id` = "'.$zone_id.'"');
		if(($matrix = $matrix_list->get_first()) === false) {
			return false; // zone not available for this product
		} else {
			return $matrix->price;
		}
	}
}
?>