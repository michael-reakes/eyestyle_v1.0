<div class="panel">
	<div class="title">Product Management</div>
	<?php if (access::verify($_STAFF->access, 'product.brand')) {?>
	<div class="subtitle">Designer Management</div>
	<div class="list">
		<a href="product_brand.php">Designer List</a>
		<a href="product_brand_add.php">Add Designer</a>
	</div>
	<?php } ?>
	
	<?php if (access::verify($_STAFF->access, 'product.category')) {?>
	<div class="subtitle">Category Management</div>
	<div class="list">
		<a href="product_categories.php">Category List</a>
		<a href="product_category_add.php">Add Category</a>
	</div>
	<?php } ?>
	
	<?php if (access::verify($_STAFF->access, 'product.product')) {?>
	<div class="subtitle">Product Management</div>
	<div class="list">
		<a href="product_product.php">Product List</a>
		<a href="product_product_add.php">Add Product</a>
	</div>
	<?php } ?>

	<?php /* if (access::verify($_STAFF->access, 'product.product')) {?>
	<div class="subtitle">Stock Management</div>
	<div class="list">
		<a href="product_stock.php">Manage Stock</a>
	</div>
	<?php } */ ?>
</div>
