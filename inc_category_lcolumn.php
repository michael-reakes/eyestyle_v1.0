<?
$data_getter = new utils_data();
$men_brands = $data_getter->get_men_brands();
$women_brands = $data_getter->get_women_brands();

switch ($_GET['gender']) {
	case 'womens':
		$catGenderId = 2;
		break;
	default:
		$catGenderId = 1;
		break;
}
?>
			<div class="lcolumn">
				<h5><a href="<?=$_ROOT?>cart.php">my shopping bag</a></h5>
				<h5>womens</h5>
				<ul>
					<li><a href="#" title="shop by designer" onclick="showDesignersList(2)">shop by designer</a></li>
					<ul id="womens_list"<?=isset($_SELECTED_GENDERID) && $_SELECTED_GENDERID == 2 ? ' style="display:block"' : ''?>>
					
					<? foreach($women_brands as $brand){ ?>
						<li<?=isset($_SELECTED_BRANDID) && $_SELECTED_BRANDID == $brand->brand_id ? ' class="selected"' : ''?>><a href="<?=url::linkBrand($brand->brand_id,"womens")?>"><?=$brand->name?></a></li>
					<? } ?>
					</ul>
				<? 
					$category_list = new dbo_list('category',''); 
					$women_subcat_exist = false;
					if ($category_list->count() > 0) $women_subcat_exist = true;
					if ($women_subcat_exist) {
						foreach ($category_list->get_all() as $category){
				?>
					<li<?=$catGenderId == 2 && 
						  isset($_SELECTED_CATEGORYID) && $_SELECTED_CATEGORYID == $category->category_id ? ' class="selected"' : ''?>><a href="<?=url::linkCategory($category,"womens") ?>" title="<?=$category->name;?>"><?=$category->name;?></a></li>
				<?
						}
					}
				?>
				</ul>
				<h5>mens</h5>
				<ul>
					<li><a href="#" title="shop by designer" onclick="showDesignersList(1)">shop by designer</a></li>
					<ul id="mens_list"<?=isset($_SELECTED_GENDERID) && $_SELECTED_GENDERID == 1 ? ' style="display:block"' : ''?>>
					
					<? foreach($men_brands as $brand){ ?>
						<li<?=isset($_SELECTED_BRANDID) && $_SELECTED_BRANDID == $brand->brand_id ? ' class="selected"' : ''?>><a href="<?=url::linkBrand($brand->brand_id,"mens")?>"><?=$brand->name?></a></li>
					<? } ?>
					</ul>
				<? 
					$category_list = new dbo_list('category',''); 
					$men_subcat_exist = false;
					if ($category_list->count() > 0) $men_subcat_exist = true;
					if ($men_subcat_exist) {
						foreach ($category_list->get_all() as $category){
				?>
					<li<?=$catGenderId == 1 && 
						  isset($_SELECTED_CATEGORYID) && $_SELECTED_CATEGORYID == $category->category_id ? ' class="selected"' : ''?>><a href="<?=url::linkCategory($category,"mens") ?>" title="<?=$category->name;?>"><?=$category->name;?></a></li>
				<?
						}
					}
				?>
				</ul>
			</div>