<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Database object. Provides funtions for one record.
 * This class relies on the database schema defined in $_DB_SCHEMA.
 *
 * @package db
 */
class dbo {
	var $_NAME;
	var $_KEY;
	var $_LINKS;

	/**
	 * If $id is provided, the object loads record from database.
	 *
	 * @param string $name Table name
	 * @param mixed $id Primary key of the record to be loaded
	 * @param boolean $load_links Whether to load parent records
	 * @return dbo
	 */
	function dbo($name, $id=-1, $load_links = false) {
		global $_DB_SCHEMA;

		$this->_NAME = $name;
		$this->_KEY = $_DB_SCHEMA['pkeys'][$name];
		$this->_LINKS = array();

		foreach ($_DB_SCHEMA['tables'][$name] as $col) {
			$this->$col = '';
		}

		if ($id != -1)
		{
			$this->load($id, $load_links);
		}
	}

	/**
	 * Load record from database
	 *
	 * @param mixed $id Primary key of the record to be loaded
	 * @param boolean $load_links Whether to load parent records
	 * @return boolean
	 */
	function load($id, $load_links = false) {
		global $_DB, $_DB_SCHEMA;

		if (is_array($id) && !is_array($this->_KEY)) {
			trigger_error('Primary key is not defined as multi-column.', E_USER_ERROR);
		}
		if (!is_array($id) && is_array($this->_KEY)) {
			trigger_error('Primary key is defined as multi-column.', E_USER_ERROR);
		}
		if (is_array($id) && is_array($this->_KEY) && (count($id) != count($this->_KEY))) {
			trigger_error('Number of primary key column does not match schema definition.', E_USER_ERROR);
		}

		if (is_array($id) && is_array($this->_KEY)) {
			for ($i=0; $i<count($this->_KEY); $i++) {
				$key = $this->_KEY[$i];
				$this->$key = $id[$i];
			}
		} else {
			$key = $this->_KEY;
			$this->$key = $id;
		}

		$where_string = $this->where_string($load_links);

		$parents = db_utils::parents($this->_NAME);
		$leftjoin_string = '';

		if ($load_links) {
			foreach ($parents as $child_column=>$parent) {
				$leftjoin_string .= ' LEFT JOIN `'.$parent.'` `'.$child_column.'` ON '.$this->_NAME.'.'.$child_column.' = '.$child_column.'.'.$_DB_SCHEMA['pkeys'][$parent];
			}
		}

		$_DB->sql_query('SELECT * FROM `'.$this->_NAME.'`'.$leftjoin_string.' WHERE '.$where_string.';');

		if ($row = $_DB->sql_fetch2drow()) {
			$this->load_2d_array($row, $load_links);
			return true;
		} else {
			return false; // 'false' if nothing loaded
		}
	}

	/**
	 * Load record from an associative array
	 *
	 * @param array $row Associative array
	 * @return boolean
	 */
	function load_array($row) {
		if ($row != NULL) {
			foreach ($row as $col=>$val) {
				$this->$col = $val;
			}
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Load record from an 2-dimensional array
	 *
	 * @param array $row 2-dimensional array
	 * @param boolean $load_links Whether to load parent records
	 * @return boolean
	 */
	function load_2d_array($row, $load_links = false) {
		if ($row != NULL) {
			$this_table = $row[$this->_NAME];
			foreach ($this_table as $col=>$val) {
				$this->$col = $val;
			}

			if ($load_links) {
				$parents = db_utils::parents($this->_NAME);
				foreach ($parents as $child_column=>$parent) {
					$parent_dbo = new dbo($parent);
					$parent_dbo->load_array($row[$child_column]);
					$this->_LINKS[$child_column] = $parent_dbo;
				}
			}

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Insert record into database
	 *
	 * @return boolean
	 */
	function insert() {
		global $_DB, $_DB_SCHEMA;

		$cols = array();
		$vals = array();
		foreach ($_DB_SCHEMA['tables'][$this->_NAME] as $col) {
			if ($col != $this->_KEY) {
				$cols[] = '`'.$col.'`';
				$vals[] = '"'.db_utils::escape($this->$col).'"';
			}
		}
		$_DB->sql_query('INSERT INTO `'.$this->_NAME.'` ('.implode(', ',$cols).') VALUES ('.implode(', ', $vals).');');
		if ($_DB->sql_affectedrows() === 1) {
			if (is_array($this->_KEY)) {
				return true;
			} else {
				$key = $this->_KEY;

				if ($this->$key == '') {
					return $this->$key = $_DB->sql_nextid();
				} else {
					return $this->$key;
				}
			}
		} else {
			return false;
		}
	}

	/**
	 * Update record
	 *
	 * @return boolean
	 */
	function update() {
		global $_DB, $_DB_SCHEMA;

		$sets = array();
		foreach ($_DB_SCHEMA['tables'][$this->_NAME] as $col) {
			$sets[] = '`'.$col.'` = "'.db_utils::escape($this->$col).'"';
		}

		$where_string = $this->where_string();
		if ($_DB->sql_query('UPDATE `'.$this->_NAME.'` SET '.implode(', ', $sets).' WHERE '.$where_string.';')){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * If the primary key exists, this function will update the record. Otherwise, the record is inserted.
	 *
	 * @return boolean
	 */
	function insert_update() {
		global $_DB;

		$where_string = $this->where_string();
		$_DB->sql_query('SELECT * FROM `'.$this->_NAME.'` WHERE '.$where_string.';');

		if ($_DB->sql_fetchrow()) {
			return $this->update();
		} else {
			return $this->insert();
		}
	}

	/**
	 * Delete the record.
	 *
	 * @return boolean
	 */
	function delete() {
		global $_DB;

		$where_string = $this->where_string();
		$_DB->sql_query('DELETE FROM `'.$this->_NAME.'` WHERE '.$where_string.';');
		if ($_DB->sql_affectedrows() === 1) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Load an array of children records as defined in $_DB_SCHEMA.
	 *
	 * @param string $name Child table name
	 * @param string $orderby Order by column name
	 * @param string $sort Sorting order "ASC" or "DESC"
	 * @return array
	 */
	function load_children($name, $orderby = '', $sort = 'ASC') {
		global $_DB, $_DB_SCHEMA;

		if (is_array($this->_KEY)) {
			trigger_error('Primary key of parent table cannot be multi-column', E_USER_ERROR);
		}

		$fkeys = $_DB_SCHEMA['fkeys'];
		$columns = array();
		foreach ($fkeys as $fkey) {
			if ($fkey['parent_table'] == $this->_NAME && $fkey['child_table'] == $name) {
				$columns[] = $fkey['child_column'];
			}
		}

		$key = $this->_KEY;
		if (count($columns) == 0) {
			trigger_error('Cannot find column '.$name, E_USER_ERROR);
		} elseif (count($columns) == 1) {
			$where_string = '`'.$columns[0].'` = "'.$this->$key.'"';
		} else {
			$clauses = array();
			foreach ($columns as $column) {
				$clauses[] = '`'.$column.'` = "'.$this->$key.'"';
			}
			$where_string = '('.implode(' OR ', $clauses).')';
		}

		if ($orderby == '') {
			if (!is_array($_DB_SCHEMA['pkeys'][$name])) {
				$orderby = $_DB_SCHEMA['pkeys'][$name];
			} else {
				$orderby = $_DB_SCHEMA['pkeys'][$name][0];
			}
		}

		$key = $this->_KEY;
		$result = array();
		$_DB->sql_query('SELECT * FROM `'.$name.'` WHERE '.$where_string.' ORDER BY `'.$orderby.'` '.$sort.';');
		while ($row = $_DB->sql_fetchrow()) {
			$child = new dbo($name);
			$child->load_array($row);
			$result[] = $child;
		}
		return $result;
	}

	/**
	 * Delete children records as defined in $_DB_SCHEMA.
	 *
	 * @param string $name Child table name
	 * @return int
	 */
	function delete_children($name) {
		global $_DB, $_DB_SCHEMA;

		if (is_array($this->_KEY)) {
			trigger_error('Primary key of parent table cannot be multi-column', E_USER_ERROR);
		}

		$fkeys = $_DB_SCHEMA['fkeys'];
		$column = '';
		foreach ($fkeys as $fkey) {
			if ($fkey['parent_table'] == $this->_NAME && $fkey['child_table'] == $name) {
				$column = $fkey['child_column'];
			}
		}

		$key = $this->_KEY;
		$_DB->sql_query('DELETE FROM `'.$name.'` WHERE `'.$column.'` = "'.$this->$key.'";');
		return $_DB->sql_affectedrows();
	}

	function link($column_name) {
		if (isset($this->_LINKS[$column_name])) {
			return $this->_LINKS[$column_name];
		} else {
			return false;
		}
	}

	/**
	 * Internal Use, handles multi-column primary key
	 *
	 * @return string
	 */
	function where_string($load_links = false) {
		if (is_array($this->_KEY)) {
			$where_array = array();
			foreach ($this->_KEY as $key) {
				if ($load_links) {
					$where_array[] = $this->_NAME.'.'.$key.' = "'.$this->$key.'"';
				} else {
					$where_array[] = '`'.$key.'` = "'.$this->$key.'"';
				}
			}
			return implode(' AND ', $where_array);
		} else {
			$key = $this->_KEY;
			return '`'.$key.'` = "'.$this->$key.'"';
		}
	}
}

/**
 * Provide funtions to manipulate a set of database records as objects.
 *
 * @package db
 *
 */
class dbo_list {
	var $_NAME;
	var $where;
	var $orderby;
	var $sort;
	var $load_links;

	var $num_per_page = 15;

	/**
	 * Constructor. Initialise the object with criteria to load records.
	 *
	 * @param string $name Table name
	 * @param string $where The "WHERE" clause of the SQL query. Note: "WHERE" must be specified in this parametre.
	 * @param string $orderby Order by column name, use
	 * @param string $sort Sorting order "ASC" or "DESC"
	 * @param boolean $load_parents Whether to load parent tables
	 * @return dbo_list
	 */
	function dbo_list($name, $where = '', $orderby = '', $sort = 'ASC', $load_links = false) {
		global $_CONFIG;

		$this->_NAME = $name;
		$this->where = $where;
		$this->sort = $sort;
		$this->load_links = $load_links;
		$this->set_orderby($orderby);

		if (isset($_CONFIG['db']['num_per_page'])) {
			$this->num_per_page = $_CONFIG['db']['num_per_page'];
		}
	}

	/**
	 * Set ORDER BY
	 *
	 * @param string $orderby ORDER BY column name
	 */
	function set_orderby($orderby = '') {
		global $_DB_SCHEMA;

		if ($orderby == '') {
			if (!is_array($_DB_SCHEMA['pkeys'][$this->_NAME])) {
				$this->orderby = $_DB_SCHEMA['pkeys'][$this->_NAME];
			} else {
				$this->orderby = $_DB_SCHEMA['pkeys'][$this->_NAME][0];
			}
		} else {
			$tokens = explode('.', $orderby);
			if ($this->load_links) {
				if (count($tokens) == 1) {
					$this->orderby = $this->_NAME.'.'.$orderby;
				} else {
					$this->orderby = $orderby;
				}
			} else {
				$this->orderby = '`'.$orderby.'`';
			}
		}
	}

	/**
	 * Set the maximum number of records returned by get_page() function.
	 *
	 * @param int $num Maximum number of records per page
	 */
	function set_num_per_page($num) {
		$this->num_per_page = $num;
	}

	/**
	 * Count number of records exsiting in the record set
	 *
	 * @return int
	 */
	function count() {
		global $_DB, $_DB_SCHEMA;

		$parents = db_utils::parents($this->_NAME);
		$leftjoin_string = '';

		if ($this->load_links) {
			foreach ($parents as $child_column=>$parent) {
				$leftjoin_string .= ' LEFT JOIN `'.$parent.'` `'.$child_column.'` ON '.$this->_NAME.'.'.$child_column.' = '.$child_column.'.'.$_DB_SCHEMA['pkeys'][$parent];
			}
		}

		$_DB->sql_query('SELECT COUNT(*) AS c FROM `'.$this->_NAME.'`'.$leftjoin_string.' '.$this->where.';');
		$row = $_DB->sql_fetchrow();
		return $row["c"];
	}
	
	/**
	 * Average value of the specified column in the record set
	 *
	 * @param string $column Name of the column to be calculated
	 * @return mixed
	 */
	function avg($column) {
		global $_DB;

		$_DB->sql_query('SELECT AVG(`'.$column.'`) AS a FROM `'.$this->_NAME.'` '.$this->where.';');
		$row = $_DB->sql_fetchrow();
		return $row["a"];
	}

	/**
	 * Sum of the values in the specified column in the record set
	 *
	 * @param string $column Name of the column to be calculated
	 * @return mixed
	 */
	function sum($column) {
		global $_DB;

		$_DB->sql_query('SELECT SUM(`'.$column.'`) AS s FROM `'.$this->_NAME.'` '.$this->where.';');
		$row = $_DB->sql_fetchrow();
		return $row["s"];
	}

	/**
	 * Maximum value of the specified column in the record set
	 *
	 * @param string $column Name of the column to be calculated
	 * @return mixed
	 */
	function max($column) {
		global $_DB;

		$_DB->sql_query('SELECT MAX(`'.$column.'`) AS m FROM `'.$this->_NAME.'` '.$this->where.';');
		$row = $_DB->sql_fetchrow();
		return $row["m"];
	}

	/**
	 * Minimum value of the specified column in the record set
	 *
	 * @param string $column Name of the column to be calculated
	 * @return mixed
	 */
	function min($column) {
		global $_DB;

		$_DB->sql_query('SELECT MIN(`'.$column.'`) AS m FROM `'.$this->_NAME.'` '.$this->where.';');
		$row = $_DB->sql_fetchrow();
		return $row["m"];
	}

	/**
	 * Number of pages of records.
	 *
	 * @return int
	 */
	function get_num_pages() {
		return ceil($this->count()/$this->num_per_page);
	}

	/**
	 * Get the first record of the record set
	 *
	 * @return dbo
	 */
	function get_first() {
		global $_DB, $_DB_SCHEMA;

		$parents = db_utils::parents($this->_NAME);
		$leftjoin_string = '';

		if ($this->load_links) {
			foreach ($parents as $child_column=>$parent) {
				$leftjoin_string .= ' LEFT JOIN `'.$parent.'` `'.$child_column.'` ON '.$this->_NAME.'.'.$child_column.' = '.$child_column.'.'.$_DB_SCHEMA['pkeys'][$parent];
			}
		}

		$_DB->sql_query('SELECT * FROM `'.$this->_NAME.'`'.$leftjoin_string.' '.$this->where.' ORDER BY '.$this->orderby.' '.$this->sort.' LIMIT 1;');

		if ($row = $_DB->sql_fetch2drow()) {
			$item = new dbo($this->_NAME);
			$item->load_2d_array($row, $this->load_links);
			return $item;
		} else {
			return false;
		}
	}

	/**
	 * Get an array of database objects of all records
	 *
	 * @return array
	 */
	function get_all() {
		global $_DB, $_DB_SCHEMA;

		$result = array();

		$parents = db_utils::parents($this->_NAME);
		$leftjoin_string = '';

		if ($this->load_links) {
			foreach ($parents as $child_column=>$parent) {
				$leftjoin_string .= ' LEFT JOIN `'.$parent.'` `'.$child_column.'` ON '.$this->_NAME.'.'.$child_column.' = '.$child_column.'.'.$_DB_SCHEMA['pkeys'][$parent];
			}
		}

		$_DB->sql_query('SELECT * FROM `'.$this->_NAME.'`'.$leftjoin_string.' '.$this->where.' ORDER BY '.$this->orderby.' '.$this->sort.';');

		while ($row = $_DB->sql_fetch2drow()) {
			$item = new dbo($this->_NAME);
			$item->load_2d_array($row, $this->load_links);
			$result[] = $item;
		}
		return $result;
	}

	/**
	 * Delete all records in the record set
	 *
	 * @return int
	 */
	function delete_all() {
		global $_DB;

		$_DB->sql_query('DELETE FROM `'.$this->_NAME.'` '.$this->where.';');
		return $_DB->sql_affectedrows();
	}

	/**
	 * Get an array of database objects of the records in the specified page
	 *
	 * @param int $page_no Page number
	 * @return array
	 */
	function get_page($page_no) {
		global $_DB, $_DB_SCHEMA;

		$result = array();
		$page_start = $this->num_per_page * ($page_no-1);

		$parents = db_utils::parents($this->_NAME);
		$leftjoin_string = '';

		if ($this->load_links) {
			foreach ($parents as $child_column=>$parent) {
				$leftjoin_string .= ' LEFT JOIN `'.$parent.'` `'.$child_column.'` ON '.$this->_NAME.'.'.$child_column.' = '.$child_column.'.'.$_DB_SCHEMA['pkeys'][$parent];
			}
		}

		$_DB->sql_query('SELECT * FROM `'.$this->_NAME.'`'.$leftjoin_string.' '.$this->where.' ORDER BY '.$this->orderby.' '.$this->sort.' LIMIT '.$page_start.', '.$this->num_per_page.';');

		while ($row = $_DB->sql_fetch2drow()) {
			$item = new dbo($this->_NAME);
			$item->load_2d_array($row, $this->load_links);
			$result[] = $item;
		}
		return $result;
	}
}

/**
 * Provide funtions to manipulate a set of database records as arrays.
 *
 * @package db
 *
 */
class record_list {
	var $select;
	var $from;
	var $where;
	var $orderby;
	var $sort;
	var $limit;
	var $load_links;

	var $num_per_page = 15;

	/**
	 * Constructor. Initialise the object with criteria to load records.
	 *
	 * @param string $select The "SELECT ... FROM ..." clause of the SQL query;
	 * @param string $where The "WHERE" clause of the SQL query. Note: "WHERE" must be specified in this parametre.
	 * @param string $orderby Order by column name, use
	 * @param string $sort Sorting order "ASC" or "DESC"
	 * @return record_list
	 */
	function record_list($select, $where = '', $orderby = '', $sort = 'ASC',$limit = '') {
		global $_CONFIG;

		$this->select = $select;
		$this->from = substr($this->select, strpos(strtoupper($this->select), ' FROM '));
		$this->where = $where;
		$this->sort = $sort;
		$this->set_orderby($orderby);
		$this->limit = $limit;

		if (isset($_CONFIG['db']['num_per_page'])) {
			$this->num_per_page = $_CONFIG['db']['num_per_page'];
		}
	}

	/**
	 * Set ORDER BY
	 *
	 * @param string $orderby ORDER BY column name
	 */
	function set_orderby($orderby = '') {
		global $_DB_SCHEMA;
		$this->orderby = $orderby;
	}

	/**
	 * Set the maximum number of records returned by get_page() function.
	 *
	 * @param int $num Maximum number of records per page
	 */
	function set_num_per_page($num) {
		$this->num_per_page = $num;
	}

	/**
	 * Count number of records exsiting in the record set
	 *
	 * @return int
	 */
	function count() {
		global $_DB, $_DB_SCHEMA;

		$_DB->sql_query('SELECT COUNT(*) AS c'.$this->from.' '.$this->where.';');
		$row = $_DB->sql_fetchrow();
		return $row["c"];
	}
	
	/**
	 * Average value of the specified column in the record set
	 *
	 * @param string $column Name of the column to be calculated
	 * @return mixed
	 */
	function avg($column) {
		global $_DB;

		$_DB->sql_query('SELECT AVG('.$column.') AS a'.$this->from.' '.$this->where.';');
		$row = $_DB->sql_fetchrow();
		return $row["a"];
	}

	/**
	 * Sum of the values in the specified column in the record set
	 *
	 * @param string $column Name of the column to be calculated
	 * @return mixed
	 */
	function sum($column) {
		global $_DB;

		$_DB->sql_query('SELECT SUM('.$column.') AS s'.$this->from.' '.$this->where.';');
		$row = $_DB->sql_fetchrow();
		return $row["s"];
	}

	/**
	 * Maximum value of the specified column in the record set
	 *
	 * @param string $column Name of the column to be calculated
	 * @return mixed
	 */
	function max($column) {
		global $_DB;

		$_DB->sql_query('SELECT MAX('.$column.') AS m'.$this->from.' '.$this->where.';');
		$row = $_DB->sql_fetchrow();
		return $row["m"];
	}

	/**
	 * Minimum value of the specified column in the record set
	 *
	 * @param string $column Name of the column to be calculated
	 * @return mixed
	 */
	function min($column) {
		global $_DB;

		$_DB->sql_query('SELECT MIN('.$column.') AS m'.$this->from.' '.$this->where.';');
		$row = $_DB->sql_fetchrow();
		return $row["m"];
	}

	/**
	 * Number of pages of records.
	 *
	 * @return int
	 */
	function get_num_pages() {
		return ceil($this->count()/$this->num_per_page);
	}

	/**
	 * Get the first record of the record set
	 *
	 * @return dbo
	 */
	function get_first() {
		global $_DB, $_DB_SCHEMA;
		$order_by_statement = '';
		if (!empty ($this->orderby))
			$order_by_statement = ' ORDER BY '.$this->orderby.' '.(!empty($this->orderby)?$this->sort:'');

		$_DB->sql_query($this->select.' '.$this->where.$order_by_statement.' LIMIT 1;');

		if ($row = $_DB->sql_fetchrow()) {
			return new _record($row);
		} else {
			return false;
		}
	}

	/**
	 * Get an array of database objects of all records
	 *
	 * @return array
	 */
	function get_all() {
		global $_DB, $_DB_SCHEMA;

		$result = array();
		//modified from the original supplied version
		$order_by_statement = '';
		if (!empty ($this->orderby))
			$order_by_statement = ' ORDER BY '.$this->orderby.' '.(!empty($this->orderby)?$this->sort:'');
		
		$limit_statement = '';
		if (!empty ($this->limit))
			$limit_statement = ' LIMIT 0,'.$this->limit;
		$_DB->sql_query($this->select.' '.$this->where.$order_by_statement.$limit_statement.';');
		//echo $this->select.' '.$this->where.$order_by_statement.$limit_statement.';';exit;
		while ($row = $_DB->sql_fetchrow()) {
			$result[] = new _record($row);
		}
		return $result;
	}

	/**
	 * Get an array of database objects of the records in the specified page
	 *
	 * @param int $page_no Page number
	 * @return array
	 */
	function get_page($page_no) {
		global $_DB, $_DB_SCHEMA;

		$result = array();
		$page_start = $this->num_per_page * ($page_no-1);

		$_DB->sql_query($this->select.' '.$this->where.' ORDER BY '.$this->orderby.' '.(!empty($this->orderby)?$this->sort:'').' LIMIT '.$page_start.', '.$this->num_per_page.';');
		//echo $this->select.' '.$this->where.' ORDER BY '.$this->orderby.' '.(!empty($this->orderby)?$this->sort:'').' LIMIT '.$page_start.', '.$this->num_per_page.';';

		while ($row = $_DB->sql_fetchrow()) {
			$result[] = new _record($row);
		}
		return $result;
	}
}

/**
 * Helper Class for record object
 */

class _record {

	function _record($row) {
		foreach ($row as $column=>$value) {
			$this->$column = $value;
		}
	}
}
?>