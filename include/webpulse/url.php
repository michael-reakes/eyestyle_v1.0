<?php
/**
* @access private
*/

/**
* @access private
*/

class url {
	
	function link($link) {
		return url::getRootFromRequestUri().$link;
	}
	
	function linkProduct($id, $gender=false) {
		global $db, $_ROOT;
		$root = url::getRootFromRequestUri();
		
		if (!is_object($id)) {
			$item = new dbo('product',$id);
		} else {
			$item = &$id;
		}
		
		$brand = new dbo('brand',$item->brand_id);

		// return $root.($gender ? $gender : url::getGender()).'/'.$brand->alias.'/'.$item->alias.'.html';
		return $root.url::getGender().'/'.$brand->alias.'/'.$item->alias.'.html';
	}
	
	function linkBrand($id, $gender=NULL) {
		global $db, $_ROOT;
		
		if ($gender!=NULL) {
			$genderlink = $gender;	
		}
		else {
			$genderlink = url::getGender();
		}
		
		$root = url::getRootFromRequestUri();
		
		if (!is_object($id)) {
			$item = new dbo('brand',$id);
		} else {
			$item = &$id;
		}

		return $root.$genderlink.'/designers/'.$item->alias.'/';
	}
	
	function linkCategory($id, $gender=NULL) {
		global $db, $_ROOT;
		
		if ($gender!=NULL) {
			$genderlink = $gender;	
		}
		else {
			$genderlink = url::getGender();
		}
		
		$root = url::getRootFromRequestUri();
		$category_arr = array();
				
		if (!is_object($id)) { // ID is passed in - retrieve category dbo
			$category = new dbo('category',$id);
		} else { // Entire category object is passed in
			$category = &$id;
		}

		$category_arr[] = $category->alias;
		$parent_id = $category->parent_id;
		while ($parent_id != 0) {
			$category_list = new dbo_list('category','WHERE `id` = '.$parent_id);
			if ($category = $category_list->get_first()) {
				$category_arr[] = $category->alias;
			} else {
				$parent_id = 0;
			}
		}
		
		$category_arr = array_reverse($category_arr);
		
		return $root.$genderlink.'/cat/'.implode('/',$category_arr).'/';
	}
	
	function linkContent($id) {
		global $db, $_ROOT;
		$root = url::getRootFromRequestUri();
		
		if (!is_object($id)) {
			$item = new dbo('page',$id);
		} else {
			$item = &$id;
		}
		
		return !empty($item->alias) ? $root.'company/'.$item->alias.'.html' : $root;
	}
	
	function linkContentByName($name) {
		global $db, $_ROOT;
		$root = url::getRootFromRequestUri();
		
		$page_list = new dbo_list('page','WHERE `title` = "'.$name.'"');
		if ($page = $page_list->get_first()) {
			return $root.'company/'.$page->alias.'.html';
		}
		return $root;
	}
	
	function linkMyAccount($url) {
		global $db, $_ROOT;
		$root = url::getRootFromRequestUri();
		
		return $root.'my-account/'.(!empty($url) ? $url.'/' : '');
	}
	
	function getRootFromRequestUri() {
		$num_directories = substr_count(substr($_SERVER['REQUEST_URI'], strlen(SITE_APP_ROOT)), '/');
		$root = '';
		while ($num_directories > 0) {
			$root .= '../';
			$num_directories--;
		}
		return $root;
	}

	function getGender() {
		$gender = 'mens';
		if (isset($_GET['gender']) && $_GET['gender'] == 'womens') $gender = 'womens';
		return $gender;
	}
}

?>