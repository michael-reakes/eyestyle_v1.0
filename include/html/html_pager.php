<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Pagination system. This class requires a dbo_list object as an input.
 *
 * @package html
 */
class html_pager {
	var $config;
	var $db_list;
	var $current_page;
	var $total_page;
	var $total_item;
	var $url;
	var $keys;
	var $sort_array;
	var $sort_key;
	var $sort_order;

	/**
	 * Initialise the pagination object.
	 *
	 * @param dbo_list $db_list The record set object to handle in this class
	 * @param array $sort_array An associative array of sortable columns. Array("column"=>"label").
	 * @param string $default_order "a" for "ASC", "d" for "DESC"
	 * @return html_pager
	 */
	function html_pager($db_list, $sort_array, $default_order='a') {
		global $_CONFIG;
		$this->config = isset($_CONFIG['html_pager']) ? $_CONFIG['html_pager'] : NULL;

		$this->sort_array = $sort_array; // key=>description

		$key_pos = !empty($_GET['s']) ? intval($_GET['s']) : 0;
		$this->keys = array_keys($sort_array);

		$this->sort_key =  $this->keys[$key_pos];
		$this->sort_order = (!empty($_GET['o']) && ($_GET['o'] == 'a' || $_GET['o'] == 'd')) ? $_GET['o'] : $default_order;

		$this->url = $_SERVER['REQUEST_URI'];
		$this->db_list = $db_list;
		$this->db_list->set_orderby($this->sort_key);
		$this->db_list->sort = ($this->sort_order=='a')?'ASC':'DESC';

		$this->total_item = $this->db_list->count();

		$total = ceil($this->total_item/$this->db_list->num_per_page);
		$current = (!empty($_GET["p"])) ? intval($_GET["p"]) : 1;

		if ($current < 1) {
			$current = 1;
		}

		if ($total < 1) {
			$total = 1;
		}

		if ($current > $total) {
			$current = $total;
		}

		$this->current_page = $current;
		$this->total_page = $total;
	}

	/**
	 * Return an array of database objects in the specified page
	 *
	 * @return array
	 */
	function get_page() {
		return $this->db_list->get_page($this->current_page);
	}

	/**
	 * Return the HTML codes to display the pagination. The CSS classes can be defined in the config file.
	 *
	 * @param string $template Specify a template file to be used
	 * @return string
	 */
	function show($template = '') {
		global $_ROOT;
		if ($_ROOT == null) $_ROOT = '../';
		$tpl = new html_template();

		$tpl->set('ROOT', $_ROOT);
		$tpl->set('goto_action', $this->url);
		$tpl->set('goto_hidden_fields', http::hidden_fields($_GET, 'p'));
		$tpl->set('current_page', $this->current_page);
		$tpl->set('total_page', $this->total_page);
		$tpl->set('total_item', $this->total_item);
		if ($this->current_page > 1) {
			$tpl->set('has_previous', true, true);
			$tpl->set('previous_url', $_ROOT.$this->output_url($this->current_page-1));
		} else {
			$tpl->set('has_previous', false, true);
		}

		$max = isset($this->config['max_number']) ? $this->config['max_number'] : 9;
		if ($this->current_page <= ceil($max/2)) {
			$start = 1;
			$end = $max;
		} elseif (($this->total_page - $this->current_page) < ceil($max/2)) {
			$end = $this->total_page;
			$start = $end - $max + 1;
		} else {
			$start = $this->current_page - $max + ceil($max/2);
			$end = $this->current_page + (int)($max/2);
		}

		$start = ($start < 1) ? 1 : $start;
		$end = ($this->total_page < $end) ? $this->total_page : $end;

		$pages = '';
		for ($i = $start; $i <= $end; $i++) {
			if ($i == $this->current_page) {
				$pages .= '<li class="selected"><a href="'.$_ROOT.$this->output_url($i).'">'.$i.'</a></li>';
				
			} else {
				$pages .= '<li><a href="'.$_ROOT.$this->output_url($i).'">'.$i.'</a></li>';
			}
			
			if ($i != $end) {
				$pages .= '<li> | </li>';
			}
		}

		$tpl->set('pages', $pages);

		if ($this->current_page < $this->total_page) {
			$tpl->set('has_next', true, true);
			$tpl->set('next_url', $_ROOT.$this->output_url($this->current_page+1));
		} else {
			$tpl->set('has_next', false, true);
		}

		if (!empty($template)) {
			return $tpl->fetch($template);
		} elseif (isset($this->config['template'])) {
			return $tpl->fetch($this->config['template']);
		} else {
			trigger_error('Template for html_pager is not defined in $_CONFIG', E_USER_ERROR);
			return '';
		}
	}

	/**
	 * Return the HTML codes to display column in a data grid table.
	 *
	 * @param string $key Column name
	 * @param int $width Width of the column
	 * @return string
	 */
	function column($key, $width = '') {
		if ($this->sort_key == $key) {
			$sort_order = ($this->sort_order == 'a') ? 'd' : 'a';
		} else {
			$sort_order = $this->sort_order;
		}

		$html = '<th';
		$html .= $width != '' ? ' width="'.$width.'"' : '';
		$html .= (isset($this->config['column_selected_class']) && $this->sort_key == $key) ? ' class="'.$this->config['column_selected_class'].'"' : '' ;
		$html .= '>';
		$html .= '<a href="'.$this->output_url($this->current_page, $key, $sort_order).'">'.$this->sort_array[$key].'</a>';
		if ($this->sort_key == $key) {
			if (isset($this->config['column_arrow_asc']) && isset($this->config['column_arrow_desc'])) {
				$arrow = ($this->sort_order == 'a') ? $this->config['column_arrow_asc'] : $this->config['column_arrow_desc'];
				$html .= ' <img src="'.$arrow.'" />';
			} else {
				$arrow = ($this->sort_order == 'a') ? '&#710;' : 'v';
				$html .= ' <b>'.$arrow.'</b>';
			}
		}
		$html .= '</th>';
		return $html;
	}

	/**
	 * Internal use.
	 *
	 * @internal
	 *
	 * @param int $pageno Page number
	 * @param string $sort_key Column name
	 * @param string $sort_order "a" or "d"
	 * @return string
	 */
	function output_url($pageno, $sort_key = '', $sort_order = '') {
		if ($sort_key == '') {
			$sort_key = $this->sort_key;
		}
		if ($sort_order == '') {
			$sort_order = $this->sort_order;
		}
		$key_pos = array_search($sort_key, $this->keys);

		$url = ltrim($this->url, SITE_APP_ROOT);
		if (strpos($this->url, '?') !== false) {
			$url = substr($url, 0, strpos($url, '?'));
		}
		$url .= '?p='.$pageno.'&s='.$key_pos.'&o='.$sort_order;
		$exclude_params = array('p','s','o','x','y','alias');
		foreach ($_GET as $name=>$value) {
			if (!in_array($name, $exclude_params)) {
				$url .= '&'.$name.'='.$value;
			}
		}
		return $url;
	}

	/**
	 * Internal use.
	 *
	 * @internal
	 *
	 * @return mixed
	 */
	function sort_key_pos() {
		return array_search($this->sort_key, $this->keys);
	}
}
?>