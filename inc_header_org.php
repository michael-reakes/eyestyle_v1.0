<?php
	//$_PAGE = 'home';

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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>EYESTYLE.COM.AU</title>
<link href="css/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="css/index.css" rel="stylesheet" type="text/css" />
<script language="JavaScript" type="text/javascript" src="js/main.js"></script>
<script language="JavaScript" type="text/javascript" src="js/mm_menu.js"></script>
<script language="JavaScript" type="text/javascript">
<!--
function mmLoadMenus() {
  if (window.menu_brand) return;
  window.menu_brand = new Menu("root",140,20,"Arial, Helvetica, sans-serif",11,"#808080","#000000","#ffffff","#ffffff","left","middle",9,0,500,-5,7,true,true,true,0,true,true);
<?
if ($_SESSION['gender'] == 'women'){
	$menu_cid = 2;
}else $menu_cid = 1;

$data_getter = new utils_data();
$brand_ids = array();
if ($menu_cid == 1) $brand_ids = $data_getter->get_men_brands();
else $brand_ids = $data_getter->get_women_brands();

foreach($brand_ids as $my_bid){
	$brand_menu = new dbo('brand',$my_bid);
	echo ("menu_brand.addMenuItem(\"".$brand_menu->name."\",\"location='brand.php?bid=".$brand_menu->brand_id."'\");");
}
	
?>
   menu_brand.hideOnMouseOut=true;
   menu_brand.bgColor='#ffffff';
   menu_brand.menuBorder=1;
   menu_brand.menuLiteBgColor='#ffffff';
   menu_brand.menuBorderBgColor='#999999';
   
   if (window.menu_men) return;
  window.menu_men = new Menu("root",120,20,"Arial, Helvetica, sans-serif",11,"#808080","#000000","#ffffff","#ffffff","left","middle",9,0,500,-5,7,true,true,true,0,true,true);
	<? 
		$category_list = new dbo_list('category',"WHERE parent_id = 1"); 
		$men_subcat_exist = false;
		if ($category_list->count() > 0) $men_subcat_exist = true;
		foreach ($category_list->get_all() as $category){
			echo ("menu_men.addMenuItem(\"".$category->name."\",\"location='category.php?cid=".$category->category_id."'\");");
		}
	?>
   menu_men.hideOnMouseOut=true;
   menu_men.bgColor='#ffffff';
   menu_men.menuBorder=1;
   menu_men.menuLiteBgColor='#ffffff';
   menu_men.menuBorderBgColor='#999999';
   
    if (window.menu_women) return;
  window.menu_women = new Menu("root",140,20,"Arial, Helvetica, sans-serif",11,"#808080","#000000","#ffffff","#ffffff","left","middle",9,0,500,-5,7,true,true,true,0,true,true);
	<? 
		$category_list = new dbo_list('category',"WHERE parent_id = 2"); 
		$women_subcat_exist = false;
		if ($category_list->count() > 0) $women_subcat_exist = true;
		foreach ($category_list->get_all() as $category){
			echo ("menu_women.addMenuItem(\"".$category->name."\",\"location='category.php?cid=".$category->category_id."'\");");
		}
	?>
   menu_women.hideOnMouseOut=true;
   menu_women.bgColor='#ffffff';
   menu_women.menuBorder=1;
   menu_women.menuLiteBgColor='#ffffff';
   menu_women.menuBorderBgColor='#999999';

menu_women.writeMenus();
} // mmLoadMenus()
//-->
</script>
</head>
<body <? if (isset($javascript)) echo $javascript; ?>>
<div id="wrapper">
  <div id="masthead">
    <div id="logo"><a href="./"><img src="images/layout/logo.gif" alt="EYESTYLE.COM.AU" /></a></div>

	<table cellspacing="0" cellpadding="0">
		<tr><td></td><td></td></tr>
		<tr><td></td><td></td></tr>
	</table>


    <div id="search"><img src="images/layout/title/product_search.gif" alt="Product Search" /><br />
		<?php
			$form_search = new html_form('form_search','search.php','post');
			$form_search->add(new html_form_text('term',true,'','',false,24));
			$form_search->add(new html_form_image_button('btn_submit','images/icon/arrow.gif','&gt;','no_border'));
			$form_search->register();
		?>
		<?=$form_search->output_open()?>
		<?=$form_search->output('term','','style="width:150px;"')?> <?=$form_search->output('btn_submit')?>
		<?=$form_search->output_close()?>
	</div>
	<div id="shopping_bag">
		<a href="cart.php"><img src="images/icon/cart.gif" alt="My Shopping Bag"/></a><br />
		<div class="items"> 
			<?php $cart_items = $_CHECKOUT->cart_total_items(); ?>
			<?=$cart_items?> 
			<? if ($cart_items > 0) {
					echo (($cart_items != 1) ? "items" : "item");
					echo " | ".html_text::currency($_CHECKOUT->cart_total());
				}
				else {
					echo "items";
				}
			?>
		</div>		
	</div>
    <div class="clear_both"></div>
  </div>
  <div id="nav">
    <ul class="inline">
	  <script language="JavaScript1.2">mmLoadMenus();</script>
      <li><a href="home.php?pref=men"><img src="images/btn/menu_men.gif" alt="Mens Collection" name="link_men" id="link_men" <?
	  if ($men_subcat_exist){ ?>
		onmouseover="MM_showMenu(window.menu_men,-10,19,null,'link_men')" onmouseout="MM_startTimeout();" 
	  <? } ?>/></a></li>
      <li><img src="images/btn/menu_divider.gif" alt="|" /></li>
      <li><a href="home.php?pref=women"><img src="images/btn/menu_women.gif" alt="Womens Collection" name="link_women" id="link_women" <?
	  if ($women_subcat_exist){ ?>
		onmouseover="MM_showMenu(window.menu_women,-10,19,null,'link_women')" onmouseout="MM_startTimeout();" 
	  <? } ?>/></a></li>
      <li><img src="images/btn/menu_divider.gif" alt="|" /></li>
      <li><a href="brand.php"><img src="images/btn/menu_brand.gif" alt="Brands" name="link_brand" id="link_brand" onmouseover="MM_showMenu(window.menu_brand,-10,19,null,'link_brand')" onmouseout="MM_startTimeout();" /></a></li>
      <li><img src="images/btn/menu_divider.gif" alt="|" /></li>
      <li><a href="myaccount.php"><img src="images/btn/menu_account.gif" alt="My Account" /></a></li>
      <li><img src="images/btn/menu_divider.gif" alt="|" /></li>
      <li><a href="about.php"><img src="images/btn/menu_about.gif" alt="About Us" /></a></li>
      <li><img src="images/btn/menu_divider.gif" alt="|" /></li>
      <li><a href="contact.php"><img src="images/btn/menu_contact.gif" alt="Contact Us" /></a></li>
    </ul>
  </div>