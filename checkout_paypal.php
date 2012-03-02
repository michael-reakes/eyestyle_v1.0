<?
$_REQUIRE_SSL = true;

require_once('inc.php');

$order = new dbo('order');
if (!$order->load($_CHECKOUT->order_id)) {
	http::redirect('checkout.php',true);
}

$_CHECKOUT->redirectedtopaypal = TRUE;

?>

<script language="JavaScript" type="text/javascript">
	<!--
		function goPayPal() {
			var formPayPal = document.getElementById('paypalpayment');
			formPayPal.submit();
		}
	-->
</script>

<body onload="goPayPal();">

<form id="paypalpayment" name="_xclick" action="https://<?=$_CONFIG['paypal']['server']?>/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="<?=$_CONFIG['paypal']['account']?>" />
<input type="hidden" name="currency_code" value="AUD" />
<input type="hidden" name="item_name" value="Eyestyle Order" />
<input type="hidden" name="rm" value="2" />
<input type="hidden" name="cbt" value="Click here to go back to Eyestyle" />
<input type="hidden" name="no_shipping" value="1" />
<input type="hidden" name="return" value="<?=http::url('checkout_complete.php?order='.$order->order_id, true)?>" />
<input type="hidden" name="cancel_return" value="<?=http::url('checkout_cancelled.php?order='.$order->order_id, true)?>'" />
<input type="hidden" name="notify_url" value="<?=http::url('checkout_paypal_ipn.php', true)?>" />
<input type="hidden" name="no_note" value="1" />
<input type="hidden" name="invoice" value="<?=$order->order_id?>" />
<input type="hidden" name="amount" value="<?=html_text::currency($order->total)?>" />
</form>
<br /><br />
<center>
<h4>Please wait while we redirect you to PayPal ...</h4>
</center>
</body>