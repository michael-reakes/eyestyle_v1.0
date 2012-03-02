<?php
$_ACCESS = 'delivery';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

foreach ($ids as $id) {
	$zone = new dbo('zone', $id);
	$domestic_zone_list = new dbo_list('post_zone', 'WHERE `zone_id` = "'.$zone->zone_id.'"');
	foreach ($domestic_zone_list->get_all() as $domestic_zone) {
		$domestic_zone->zone_id = 0;
		$domestic_zone->update();
	}
	$zone->delete();
}

html_message::add('Zone(s) deleted successfully', 'info');
http::redirect('delivery_zones.php');
?>