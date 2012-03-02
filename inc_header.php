<?php
require_once('inc.php');
if (isset($_GET['pref'])){
	$_SESSION['gender'] = $_GET['pref'];
}
else if (isset($_SESSION['gender'])){
	//do nothing
}
else{ //default setting is men
	$_SESSION['gender'] = 'men';
}
if ($_SESSION['gender'] == 'men') {
	$keywords_dbo = new dbo('preference','meta_keywords_men');
	$description_dbo = new dbo('preference','meta_description_men');
}
else {
	$keywords_dbo = new dbo('preference','meta_keywords_women');
	$description_dbo = new dbo('preference','meta_description_women');
}

if (!isset($_TITLE)) {
	$_TITLE = "Eyestyle - Designer Sunglasses";
}

$data_getter = new utils_data();
$men_brands = $data_getter->get_men_brands();
$women_brands = $data_getter->get_women_brands();
?>
  <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- Meta Declaration -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Language" content="en-AU" />
<meta name="description" lang="en" content="<?=$description_dbo->value?>" />
<meta name="keywords" lang="en" content="<?=$keywords_dbo->value?>" />
<meta name="author" content="S3 Group, Sydney Australia" />
<meta name="copyright" content="S3 Group, Sydney Australia" />
<meta name="reply-to" content="service@s3group.com.au" />
<!--
	Website by S3 Group Pty Ltd
	Suite 304A, Level 3, 275 Alfred Street
	NORTH SYDNEY NSW 2060   
	www.s3group.com.au
-->
<title><?=$_TITLE?></title>
<!-- Javascript Includes -->
<script type="text/javascript" src="<?=$_ROOT?>js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="<?=$_ROOT?>js/global.js?20091013"></script>
<script type="text/javascript" src="<?=$_ROOT?>js/main.js"></script>
<script type="text/javascript" src="<?=$_ROOT?>js/mm_menu.js"></script>
<!-- Stylesheet Includes -->
<link href="<?=$_ROOT?>css/screen.css" rel="stylesheet" type="text/css" media="all" />
<!--[if IE]>
<link href="<?=$_ROOT?>css/ie.css" rel="stylesheet" type="text/css" media="all" />
<![endif]-->
<!--[if lt IE 7]>
<link href="<?=$_ROOT?>css/ie6.css" rel="stylesheet" type="text/css" media="all" />
<![endif]-->
</head>
<body>
<div id="wrapper">
	<div id="frame">
		<div id="topbar" class="clearfix">
			<fieldset class="search">
				<?php
					$form_search = new html_form('form_search',$_ROOT.'search.php','get');
					$form_search->add(new html_form_text('term',true,'','',false,30,200));
					$form_search->add(new html_form_image_button('btn_submit','images/icon/arrow.gif','&gt;','no_border'));
					$form_search->register();
				?>
				<?=$form_search->output_open()?>
				<label for="keyword">search</label>
				<input type="text" name="term" id="keyword" size="30" maxlength="200" />
				<?=$form_search->output_close()?>
				
			</fieldset>
			<ul id="nav">
				<li><a href="#" title="Mens">Mens</a>
					<div class="dropdown clearfix">
						<div class="categories">
							<h3>Shop by Category</h3>
							<ul>
					<?
						$category_list = new dbo_list('category',''); 
						$men_subcat_exist = false;
						if ($category_list->count() > 0) $men_subcat_exist = true;
						if ($men_subcat_exist) {
							$i = 0;
							foreach ($category_list->get_all() as $category){
					?>
								<li><a href="<?=url::linkCategory($category,"mens") ?>" title="<?=$category->name;?>"><?=$category->name;?></a></li>
							
					<?		} ?>
							</ul>
						</div>
					<?	
						}
					?>
						<div class="designers">
							<h3>Shop by Designer</h3>
					<?
						$splitted = array_chunk($men_brands, ceil(count($men_brands)/3));
						$ulclasses = array('threecol-a', 'threecol-b', 'threecol-c');
						for ($i = 0; $i < 3; $i++) {
							if (isset($splitted[$i])) {
					?>
							<ul class="<?=$ulclasses[$i]?>">
					<?			foreach($splitted[$i] as $brand) { ?>
								<li><a href="<?=url::linkBrand($brand->brand_id,"mens")?>" title="<?=$brand->name?>"><?=$brand->name?></a></li>
					<?			} ?>
							</ul>
					<?		}
						}
					?>
						</div>
					</div>
				
				
				</li>
				<li><a href="#" title="Womens">Womens</a>
					<div class="dropdown clearfix">
						<div class="categories">
							<h3>Shop by Category</h3>
							<ul>
					<?
						$category_list = new dbo_list('category',''); 
						$women_subcat_exist = false;
						if ($category_list->count() > 0) $women_subcat_exist = true;
						if ($women_subcat_exist) {
							$i = 0;
							foreach ($category_list->get_all() as $category){
					?>
								<li><a href="<?=url::linkCategory($category,"womens") ?>" title="<?=$category->name;?>"><?=$category->name;?></a></li>
							
					<?		} ?>
							</ul>
						</div>
					<?	
						}
					?>
						<div class="designers">
							<h3>Shop by Designer</h3>
					<?
						$splitted = array_chunk($women_brands, ceil(count($women_brands)/3));
						$ulclasses = array('threecol-a', 'threecol-b', 'threecol-c');
						for ($i = 0; $i < 3; $i++) {
							if (isset($splitted[$i])) {
					?>
							<ul class="<?=$ulclasses[$i]?>">
					<?			foreach($splitted[$i] as $brand) { ?>
								<li><a href="<?=url::linkBrand($brand->brand_id,"womens")?>" title="<?=$brand->name?>"><?=$brand->name?></a></li>
					<?			} ?>
							</ul>
					<?		}
						}
					?>
						</div>
					</div>
				</li>
				<li><a href="#" title="Designers">Designers</a>
					<div class="dropdown dropdown_designers clearfix">
						<div class="mens">
							<h3>Mens</h3>
					<?
						$splitted = array_chunk($men_brands, ceil(count($men_brands)/2));
						$ulclasses = array('threecol-a', 'threecol-b', 'threecol-c');
						for ($i = 0; $i < 2; $i++) {
							if (isset($splitted[$i])) {
					?>
							<ul class="<?=$ulclasses[$i]?>">
					<?			foreach($splitted[$i] as $brand) { ?>
								<li><a href="<?=url::linkBrand($brand->brand_id,"mens")?>" title="<?=$brand->name?>"><?=$brand->name?></a></li>
					<?			} ?>
							</ul>
					<?		}
						}
					?>
						</div>
						<div class="womens">
							<h3>Womens</h3>
					<?
						$splitted = array_chunk($women_brands, ceil(count($women_brands)/2));
						$ulclasses = array('threecol-a', 'threecol-b', 'threecol-c');
						for ($i = 0; $i < 2; $i++) {
							if (isset($splitted[$i])) {
					?>
							<ul class="<?=$ulclasses[$i]?>">
					<?			foreach($splitted[$i] as $brand) { ?>
								<li><a href="<?=url::linkBrand($brand->brand_id,"womens")?>" title="<?=$brand->name?>"><?=$brand->name?></a></li>
					<?			} ?>
							</ul>
					<?		}
						}
					?>
						</div>
					</div>
				</li>
				<li><a href="<?=url::linkContentByName('About Us')?>" title="About Us">About Us</a></li>
				<li><a href="<?=url::linkContentByName('Contact Us')?>" title="Contact Us">Contact Us</a></li>
				<li><a href="<?=url::link('newsletter_subscribe.php')?>" title="Subscribe to Newsletter">Newsletter</a></li>
				<li><a href="<?=url::link('customer_account_details.php')?>" title="My Account">My Account</a></li>
				<li class="last"><a href="<?=url::link('faq.php')?>" title="Legal Stuff">FAQ</a></li>
			</ul>
		</div>
		<h1 id="logo"><a href="<?=$_ROOT?>" title="Eyestyle"><img src="<?=$_ROOT?>images/logo.gif" alt="Eyestyle" width="138" height="51" /></a></h1>