<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Functions to handle HTML output.
 *
 * @package html
 */
class html {

	/**
	 * Output the HTML codes of a navigation breadcrumb. The CSS classes can be defined in config file.
	 *
	 * @static
	 *
	 * @param array $array An array of Array('Label'=>'URL')
	 * @param boolean $https Use HTTPS for the URLs or not
	 * @return string
	 */
	function breadcrumb ($array, $https=false) {
		global $_CONFIG;

		$html = '';
		$len = count($array);
		if ($len > 0) {
			$html .= '<div';
			$html .= isset($_CONFIG['html']['breadcrumb_id']) ? ' class="'.$_CONFIG['html']['breadcrumb_id'].'"' : '';
			$html .= '><ul>';
			$i = 1;
			foreach ($array as $label=>$url) {
				if ($i == $len) {
					$html .= '<li class="'.$_CONFIG['html']['breadcrumb_selected_class'].'">';
				} else {
					$html .= '<li>';
				}
				if ($url == '') {
					$html .= $label;
				} else {
					$html .= '<a href="'.http::url($url, $https).'">'.$label.'</a>';
				}
				$html .= '</li>';
				if ($i < $len) {
					$html .= '<li>'.$_CONFIG['html']['breadcrumb_seperator'].'</li>';
				}
				$i++;
			}
			$html .= '</ul></div>';
		}
		return $html;
	}

	/**
	 * @static
	 *
	 * @param array $array
	 * @return string
	 */
	function cat_breadcrumb ($array) {
		global $_CONFIG;

		$html = '';
		if (count($array) > 0) {
			$i = 1;
			foreach ($array as $label=>$url) {
				if ($i < count($array)) {
					$html .= '<a href="'.$url.'">'.$label.'</a> &gt; ';
				} else {
					if (isset($_CONFIG['html']['breadcrumb_selected_class'])) {
						$html .= '<span class="'.$_CONFIG['html']['breadcrumb_selected_class'].'">'.$label.'</span>';
					} else {
						$html .= $label;
					}
				}
				$i++;
			}
		}
		return $html;
	}

	/**
	 * @static
	 *
	 * @param int $cid Category ID
	 * @return string
	 */
	function cat_path($cid) {
		$category = new dbo('category', $cid);
		$path = $category->name;
		while ($category->parent_id != 0) {
			$category = new dbo('category', $category->parent_id);
			$path = $category->name . ' > ' . $path;
		}
		return $path;
	}

	function check_required($form, $field) {
		if ($form->required($field)) {
			print '<span class="required">*</span>';
		}
	}

}
?>