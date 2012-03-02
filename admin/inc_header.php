<?
	unset($_SESSION['resend_emails']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title><?=$_CONFIG['site']['name']?><?=isset($_SECTION)?' - '.$_SECTION:''?><?=isset($_PAGE)?' - '.$_PAGE:''?></title>
	<link href="css/admin.css" rel="stylesheet" type="text/css">
	<link href="css/utilities.css" rel="stylesheet" type="text/css">
	<link href="css/table.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" language="javascript" src="js/admin.js"></script>
</head>
<body>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td rowspan="2" id="logo">
			<a href="index.php"><img src="images/logo.gif" alt="<?=$_CONFIG['company']['name']?>" /></a><br/>
		</td>
		<td id="header">
			<div id="system_title"><?=$_CONFIG['site']['name']?></div>
			<?php if ($_STAFF != false) { ?>
			<div id="system_info">
				Hello <?=$_STAFF->fullname?>! | <a href="change_password.php">Change Password</a> | <a href="action_logout.php">Logout</a>
			</div>
			<?php } ?>
		</td>
	</tr>

	<tr>
		<td id="nav">&nbsp;
			<?php if ($_STAFF != false) { ?>
				<?php if ($_SECTION == 'Home') { ?>
					<div class="selected">Home</div>
				<?php } else { ?>
					<a href="index.php">Home</a>
				<?php } ?>

				<?php if (access::verify($_STAFF->access, 'storefront')) {?>
					<?php if ($_SECTION == 'Store Front') { ?>
					<div class="selected">Store Front</div>
					<?php } else { ?>
					<a href="store_front.php?id=1">Store Front</a>
					<?php } ?>
				<?php } ?>


				<?php if (access::verify($_STAFF->access, 'content')) {?>
					<?php if ($_SECTION == 'Content') { ?>
					<div class="selected">Content</div>
					<?php } else { ?>
					<a href="content_content.php?id=1">Content</a>
					<?php } ?>
				<?php } ?>

				<?php if (access::verify($_STAFF->access, 'product')) {?>
					<?php if ($_SECTION == 'Product') { ?>
					<div class="selected">Product</div>
					<?php } else { ?>
					<a href="product_product.php">Product</a>
					<?php } ?>
				<?php } ?>

				<?php if (access::verify($_STAFF->access, 'order')) {?>
					<?php if ($_SECTION == 'Order Management') { ?>
					<div class="selected">Order</div>
					<?php } else { ?>
					<a href="order_order.php">Order</a>
					<?php } ?>
				<?php } ?>

				<?php if (access::verify($_STAFF->access, 'delivery')) {?>
					<?php if ($_SECTION == 'Delivery') { ?>
					<div class="selected">Delivery</div>
					<?php } else { ?>
					<a href="delivery_matrix.php">Delivery</a>
					<?php } ?>
				<?php } ?>

				<?php if (access::verify($_STAFF->access, 'customer')) {?>
					<?php if ($_SECTION == 'Customer') { ?>
					<div class="selected">Customer</div>
					<?php } else { ?>
					<a href="customer_customer.php">Customer</a>
					<?php } ?>
				<?php } ?>

				<?php if (access::verify($_STAFF->access, 'newsletter')) {?>
					<?php if ($_SECTION == 'Newsletter Management') { ?>
					<div class="selected">Newsletter</div>
					<?php } else { ?>
					<a href="newsletter_subscriber.php">Newsletter</a>
					<?php } ?>
				<?php } ?>

				<?php if (access::verify($_STAFF->access, 'staff.account')) {?>
					<?php if ($_SECTION == 'Staff Management') { ?>
						<div class="selected">Staff Management</div>
					<?php } else { ?>
						<a href="staff_staff.php">Staff Management</a>
					<?php }?>
				<?php } ?>
			<?php } ?>
		</td>
	</tr>
