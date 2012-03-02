<?php
$_ACCESS = 'product.colour';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));


$colour = new dbo('colour', $_GET['id']);
$lenses = $colour->load_children('lens');
foreach($lenses as $lens){
	/*$lenses->status = 'inactive';
	$lenses->update();*/
	$lens->delete();
}
/*
$colour->status = 'inactive';
$colour->update();*/
$colour->delete();


html_message::add('Colour deleted successfully', 'info');
http::redirect(http::get_path());
?>