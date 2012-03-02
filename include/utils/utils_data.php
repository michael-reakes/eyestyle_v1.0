<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Functions to handle date & time
 *
 * @package utils
 */
class utils_data {

	/**
	 * Get all subcategories below this category. Returning an array of category ids
	 *
	 * @static
	 *
	 * @param string $category_id
	 * @return array 
	 */
	function get_all_descendants($cid){
		$main_cat = new dbo('category',$cid);
		$categories = $main_cat->load_children('category');
		$cat_ids = array();
		foreach($categories as $category){
			$cat_ids[] = $category->category_id;
			if (count($this->get_all_descendants($category->category_id)) > 0){
				$cat_ids = array_merge($cat_ids,$this->get_all_descendants($category->category_id));
			}
		}
		return $cat_ids; 
	}

	function get_ancestor($cid){
		$category = new dbo('category',$cid);
		
		while ($category->parent_id != 0) {
			$category = new dbo('category',$category->parent_id);			
		}	
		return $category->category_id;
	}

	function get_men_brands(){
		return $this->get_brands_from_category(1);		
	}

	function get_women_brands(){
		return $this->get_brands_from_category(2);
	}

	function get_brands_from_category($cid){
		$brand_list = new record_list('SELECT b.brand_id, b.name FROM `brand` b INNER JOIN `product` p ON b.brand_id = p.brand_id',
									  'WHERE p.status = "active" AND p.parent_category IN ('.(int)$cid.', 0) 
									   GROUP BY b.brand_id HAVING COUNT(p.product_id) > 0',
									  'b.name');
		return $brand_list->get_all();
	}

}
?>