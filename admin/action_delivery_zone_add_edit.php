<?php
	$_ACCESS = 'delivery';
	
	require_once('inc.php');
	
	$zone = new dbo('zone');
	
	$form = html_form::get_form('form_delivery_zone');
	
	if (!$form->validate()) {
		$form->set_failure();
	}
	
	$zone = new dbo('zone');
	if ($form->get('mode') == 'edit') {
		$zone->load($form->get('id'));
	}
	
	$d_zone_ids = $form->get('d_zone_id');
	if($form->get('type') == 'domestic' && empty($d_zone_ids)) {
		$form->set_failure('Please select at least one eParcel Zone.');
	}
	$countries = $form->get('country');
	if($form->get('type') == 'international' && empty($countries)) {
		$form->set_failure('Please select at least one country.');
	}	
	
	$zone->type = $form->get('type');
	$zone->name = $form->get('name');
	$zone->insert_update();
	
	if($zone->type == 'domestic') {
		$domestic_zone_list = new dbo_list('post_zone', 'WHERE `zone_id` = "'.$zone->zone_id.'"');
		foreach ($domestic_zone_list->get_all() as $d_zone) {
			$d_zone->zone_id = 0;
			$d_zone->update();
		}
		foreach ($d_zone_ids as $d_zone_id) {
			$d_zone = new dbo('domestic_zone', $d_zone_id);
			$d_zone->zone_id = $zone->zone_id;
			$d_zone->update();
		}
	} elseif($zone->type == 'international') {
		$country_list = new dbo_list('country', 'WHERE `zone_id` = "'.$zone->zone_id.'"');
		foreach ($country_list->get_all() as $country) {
			$country->zone_id = 0;
			$country->update();
		}
		foreach ($countries as $country_code) {
			$country = new dbo('country', $country_code);
			$country->zone_id = $zone->zone_id;
			$country->update();
		}
	}
	
	html_message::add('Zone updated successfully.', 'info');
	http::redirect(http::get_path());
?>