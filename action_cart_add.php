<?php

require_once('inc.php');

if (!isset($_POST['id'])) {
	http::halt();
}
else{
	$pid = $_POST['id'];
}

$form = html_form::get_form('form_cart');

$something_added = false;
$product = new dbo('product',$pid);
$colours = $product->load_children('colour');
/*foreach($colours as $colour){
	$lenses = $colour->load_children('lens');
	foreach($lenses as $lens){
		$qty = $form->get('qty_'.$lens->lens_id);
		if (intval($qty) > 0) {
			$_CHECKOUT->cart_add($lens->lens_id,intval($qty));	
			$something_added = true;
		}
	}
}
*/
if ($_POST['colour'] == '-1' ) {
	$something_added = false;
}
elseif ($_POST['colour'] != "") {
	$colourlens = preg_split('/_/',$_POST['colour']);
	$lensid = $colourlens[1];
	$_CHECKOUT->cart_add($lensid,$_POST['qty']);
	$something_added = true;
}




if ($something_added) {
	http::redirect('cart.php');
}else{
	$form->set_failure('Please choose one of the lens');
}
	

?>