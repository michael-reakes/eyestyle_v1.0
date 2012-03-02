<div class="panel">
	<div class="title">Customer</div>
	<?php if (access::verify($_STAFF->access, 'all')) {?>
	<div class="subtitle">Customer</div>
	<div class="list">
		<a href="customer_customer.php">Customer List</a>
	</div>
	<?}?>
</div>

