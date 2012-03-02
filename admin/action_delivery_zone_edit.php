<?php
$_ACCESS = 'delivery';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));
$zone = new dbo('zone', $_GET['id']);

$form = html_form::get_form('form_delivery_zone_edit');

if (!$form->validate()) {
	$form->set_failure();
}
$d_zone_ids = $form->get('d_zone_id');

$zone->name = $form->get('name');
$zone->update();

$post_zone_list = new dbo_list('post_zone', 'WHERE `zone_id` = "'.$zone->zone_id.'"');
foreach ($post_zone_list->get_all() as $d_zone) {
	$d_zone->zone_id = 0;
	$d_zone->update();
}
foreach ($d_zone_ids as $d_zone_id) {
	$d_zone = new dbo('post_zone', $d_zone_id);
	$d_zone->zone_id = $zone->zone_id;
	$d_zone->update();
}

html_message::add('Zone updated successfully.', 'info');
http::redirect(http::get_path());
?>