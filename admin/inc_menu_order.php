<div class="panel">
	<div class="title">Order Management</div>
	<div class="subtitle">View Orders</div>
	<div class="list">
		<?php
			$unconfirmed = new dbo_list('order','WHERE `status` = "unconfirmed"');
			$confirmed = new dbo_list('order','WHERE `status` = "confirmed"');
			$processing = new dbo_list('order','WHERE `status` = "processing"');
			$delivered = new dbo_list('order','WHERE `status` = "delivered"');
		?>
		<a href="order_order.php?status=unconfirmed">Unconfirmed (<?=$unconfirmed->count()?>)</a>
		<a href="order_order.php?status=confirmed">Confirmed (<?=$confirmed->count()?>)</a>
		<a href="order_order.php?status=processing">Processing (<?=$processing->count()?>)</a>
		<a href="order_order.php?status=delivered">Dispatched (<?=$delivered->count()?>)</a>
	</div>
	<div class="subtitle">Order Notification Setting</div>
	<div class="list">
		<a href="order_notification.php">Order Notification Email</a>
	</div>

</div>