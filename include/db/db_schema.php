<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 *
 * @package db
 */

$_DB_SCHEMA = array();
$_DB_SCHEMA['tables'] = array (
	'staff'=> array (
		'staff_id',
		'fullname',
		'access',
		'password',
		'group_id',
		'last_login'
	),

	'staff_group'=> array (
		'staff_group_id',
		'name',
		'access'
	),

	'brand'=> array (
		'brand_id',
		'name',
		'title',
		'alias',
		'image_men',
		'image_women'
	),
	
	'product'=> array(
		'product_id',
		'brand_id',
		'parent_category',
		'category_id_1',
		'category_id_2',
		'delivery_class_id',
		'alias',
		'name',
		'status',
		'price',
		'aus_only',
		'weight',
		'sub_heading',
		'features',
		'image_1',
		'image_2',
		'image_3',
		'image_4',
		'image_5',
		'image_6',
		'image_7',
		'image_rollover'
	),

	'colour'=>array(
		'colour_id',
		'product_id',
		'name'
	),

	'lens'=>array(
		'lens_id',
		'colour_id',
		'code',
		'name'
	),

	'category'=> array(
		'category_id',
		'parent_id',
		'name',
		'sort_order',
		'title',
		'alias',
		'image_men',
		'image_women'
	),

	'order'=>array (
		'customer_id',
		'order_id',
		'billing_fullname',
		'billing_address',
		'billing_suburb',
		'billing_postcode',
		'billing_state',
		'billing_email',
		'billing_phone',
		'billing_mobile',
		'billing_country',
		'delivery_fullname',
		'delivery_phone',
		'delivery_address',
		'delivery_suburb',
		'delivery_postcode',
		'delivery_state',
		'delivery_country',
		'total',
		'delivery_cost',
		'payment_method',
		'payment_reference',
		'comment',
		'courier_name',
		'tracking_no',
		'status',
		'date_created',
		'date_processed',
		'date_delivered'
	),

	'order_item'=> array(
		'order_item_id',
		'order_id',
		'product_id',
		'unit_price',
		'quantity',
		'lens_name',
		'colour_name',
		'code'
	),

	'customer'=> array(
		'customer_id',
		'password',
		'fullname',
		'company',
		'address',
		'suburb',
		'state',
		'country',
		'postcode',
		'email',
		'phone',
		'mobile',
		'date_created',
		'last_login'	
	),

	'postcode'=>array (
		'postcode_id',
		'code',
		'location',
		'state',
		'domestic_zone_id',
		'type'
	),

	'delivery_matrix'=>array (
		'delivery_matrix_id',
		'delivery_class_id',
		'zone_id',
		'price'
	),
		
	'courier'=>array (
		'courier_id',
		'name',
		'contact'
	),
	
	'zone'=> array (
		'zone_id',
		'name',
		'type'
	),

	'post_zone'=>array (
		'post_zone_id',
		'name',
		'zone_id'
	),
	
	'delivery_class'=>array (
		'delivery_class_id',
		'name',
		'description',
		'weight'
	),

	'page'=>array(
		'page_id',
		'title',
		'content',
		'alias'
	),

	'feature_products'=>array(
		'feature_products_id',
		'banner',
		'product_id',
		'banner_path',
		'colour'
	),

	'preference'=>array(
		'name',
		'value'
	),

	'newsletter'=> array (
		'newsletter_id',
		'name',
		'from_address',
		'from_name',
		'subject',
		'body',
		'date_created',
		'date_last_sent'
	),

	'subscriber'=> array (
		'subscriber_id',
		'email',
		'date_created',
		'status'
	),
	
	'country'=> array(
		'code',
		'continent',
		'name',
		'zone_id'
	)

);

$_DB_SCHEMA['pkeys'] = array (
	'brand' => 'brand_id',
	'category' => 'category_id',
	'delivery_class' => 'delivery_class_id',
	'courier' => 'courier_id',
	'post_zone' => 'post_zone_id',
	'delivery_matrix' => 'delivery_matrix_id',
	'order' => 'order_id',
	'order_item' => 'order_item_id',
	'page' => 'page_id',
	'postcode' => 'postcode_id',
	'product' => 'product_id',
	'colour' => 'colour_id',
	'lens' => 'lens_id',
	'staff' => 'staff_id',
	'staff_group' => 'staff_group_id',
	'customer' => 'customer_id',
	'setting' => 'name',
	'zone' => 'zone_id',
	'feature_products' => 'feature_products_id',
	'preference' => 'name',
	'subscriber' => 'subscriber_id',
	'newsletter' => 'newsletter_id',
	'country' => 'code'
);

$_DB_SCHEMA['fkeys'] = array (
	array (
		'parent_table' => 'staff_group',
		'child_table' => 'staff',
		'child_column' => 'group_id'
	),

	array (
		'parent_table' => 'category',
		'child_table' => 'product',
		'child_column' => 'category_id_1'
	),

	array (
		'parent_table' => 'category',
		'child_table' => 'product',
		'child_column' => 'category_id_2'
	),

	array (
		'parent_table' => 'product',
		'child_table' => 'colour',
		'child_column' => 'product_id'
	),

	array(
		'parent_table' => 'colour',
		'child_table' => 'lens',
		'child_column' => 'colour_id'
	),

	array(
		'parent_table' => 'category',
		'child_table' => 'category',
		'child_column' => 'parent_id'
	),

	array(
		'parent_table' => 'order',
		'child_table' => 'order_item',
		'child_column' => 'order_id'	
	),

	array(
		'parent_table' => 'brand',
		'child_table' =>  'product',
		'child_column' => 'brand_id'	
	),

)
?>