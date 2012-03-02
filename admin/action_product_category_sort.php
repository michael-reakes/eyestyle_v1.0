<?php
$_ACCESS = 'product.category';

require_once('inc.php');

http::halt_if(!isset($_GET['action']) || !isset($_GET['id']));

$action = $_GET['action'];
$category = new dbo('category', $_GET['id']);

if ($action == 'up') {
	$category_list = new dbo_list('category', 'WHERE `sort_order` < '.$category->sort_order.' AND `parent_id` = '.$category->parent_id, 'sort_order', 'DESC');
} else {
	$category_list = new dbo_list('category', 'WHERE `sort_order` > '.$category->sort_order.' AND `parent_id` = '.$category->parent_id, 'sort_order', 'ASC');
}

if ($next = $category_list->get_first()) {
	$swap = $category->sort_order;
	$category->sort_order = $next->sort_order;
	$next->sort_order = $swap;
	$category->update();
	$next->update();
	html_message::add('Display order updated', 'info');
}

http::redirect(http::get_path());
?>