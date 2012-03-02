<?php
$_ACCESS = 'product.category';

require_once('inc.php');

global $xml;
global $depth;
global $stack;
global $max_depth;

$depth = 0;

$type = isset($_GET['type']) ? $_GET['type'] : '';

$stack = array();
if (isset($_GET['id'])) {
	$curr_category = new dbo('category',$_GET['id']);

	$stack[] = $curr_category->category_id;
	$bc_category = $curr_category;
	while ($bc_category->parent_id != 0) {
		$bc_category = new dbo('category', $bc_category->parent_id);
		$stack[] = $bc_category->category_id;
	}
	$stack = array_reverse($stack);
}
$max_depth = count($stack);


function subcategory_tree($parent_id) {
	global $xml;
	global $depth;
	global $stack;
	global $max_depth;

	$category_list = new dbo_list('category','WHERE `parent_id` = "'.$parent_id.'"','name');
	foreach($category_list->get_all() as $category) {
		$subcat_list = new dbo_list('category','WHERE `parent_id` = "'.$category->category_id.'"','name');

		$attributes = 'label="'.$category->name.'" id="'.$category->category_id.'"';
		if ($category->parent_id != 0) {
			$attributes .= ' isBranch="false"';
		}

		if (isset($stack[$depth]) && $stack[$depth] == $category->category_id) {
			if($depth == ($max_depth-1)) {
				$attributes .= ' highlight="true"';
			} else {
				$attributes .= ' open="true"';
			}

			$depth++;
		}

		$xml .= '<node '.$attributes.'>';

		subcategory_tree($category->category_id);

		$xml .= '</node>';
	}
}

$xml = '<?xml version="1.0" encoding="utf-8"?>';
$xml .= '<node>';
if ($type == 'category') {
	if (isset($curr_category) && $curr_category->category_id != 0) {
		$xml .= '<node label="None" id="0"></node>';
	} else {
		$xml .= '<node label="None" id="0" highlight="true"></node>';
	}
}

subcategory_tree(0);

$xml .= '</node>';

print $xml;

?>