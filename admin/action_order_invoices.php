<?php
$_ACCESS = 'order';

require_once('inc.php');

$form = html_form::get_form('form_order_invoices');

if (!$form->validate()) {
	$form->set_failure();
} 

$status_array = $form->get('status');
if(empty($status_array)) {
	$form->set_failure('Please select at least one status.');
}

$start = utils_time::db_datetime_str($form->get('date'));
$end = utils_time::db_datetime_str($form->get('date'), true);

$where_array = array();
foreach ($status_array as $status) {
	switch($status) {
		case 'confirmed':
			$where_array[] = '(`date_paid` >= "'.$start.'" AND `date_paid` <= "'.$end.'")';
			break;
		case 'delivered':
			$where_array[] = '(`date_dispatched` >= "'.$start.'" AND `date_dispatched` <= "'.$end.'")';
			break;
	}
}

$order_list = new dbo_list('order', 'WHERE '.implode(' OR ', $where_array));

if($order_list->count() == 0) {
	$form->set_failure('Sorry, there is no order that matches your criteria.');
}

$url = 'order_print.php?';
foreach ($order_list->get_all() as $order) {
	$url .= '&id[]='.$order->order_id;
}
http::redirect($url);

?>