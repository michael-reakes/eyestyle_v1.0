<?php
$_ACCESS = 'product.lens';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));


$lens = new dbo('lens', $_GET['id']);
if ($lens->delete()) {
	html_message::add('Lense deleted successfully', 'info');
}
else{
	html_message::add('Unable to delete lense, the server might be down, please try again later.', 'info');
}
http::redirect(http::get_path());

?>