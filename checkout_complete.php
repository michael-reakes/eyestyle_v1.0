<?
$_REQUIRE_SSL = true;

require_once('inc.php');
$_PAGE = 'checkout';

$breadcrumb = array('home'=>'./',$_PAGE=>'');

if ((isset($_GET['order'])) && ($_CHECKOUT->order_id != $_GET['order'])) {
	$_CHECKOUT->checkout_reset();
	//http::redirect('./');
}

$order = new dbo('order',$_CHECKOUT->order_id);

$_CHECKOUT->checkout_reset();



?>
<?php require_once('inc_header.php') ?>

	<div id="content" class="clearfix">
			<?=html::breadcrumb($breadcrumb)?>
			<? html_message::show()?>
			<ul class="checkoutsteps">
				<li>1. Billing Details</li>
				<li>&gt;</li>
				<li>2. Delivery Details</li>
				<li>&gt;</li>
				<li>3. Order Summary</li>
				<li>&gt;</li>
				<li>4. Payment</li>
				<li>&gt;</li>
				<li class="selected">5. Complete!</li>
			</ul>
			<h1>Thank you for your order!</h1>
			<p>You should receive an order confirmation email shortly.</p>
			<p>Should you have any queries about your order, please contact us <a href="contact.html">here</a>.</p>
			<a href="./" class="actionbutton">Return to Homepage</a>
		</div>
	
	
	<?php require_once('inc_footer.php'); ?>