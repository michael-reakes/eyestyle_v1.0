<?php
$_ACCESS = 'delivery';

require_once('inc.php');

$form = html_form::get_form('form_delivery_zone_add');

if (!$form->validate()) {
	$form->set_failure();
}

$d_zone_ids = $form->get('d_zone_id');
if(empty($d_zone_ids)) {
	$form->set_failure('Please select at least one eParcel Zone.');
}

$zone = new dbo('zone');
$zone->name = $form->get('name');
$zone->insert();

foreach ($d_zone_ids as $d_zone_id) {
	$d_zone = new dbo('post_zone', $d_zone_id);
	$d_zone->zone_id = $zone->zone_id;
	$d_zone->update();
}

html_message::add('Zone created successfully.', 'info');
http::redirect('delivery_zones.php');
?>