<?php
/**
 * @copyright S3 Gropu Pty Ltd (www.s3group.com.au)
 *
 */

/**
 * Utility functions for database operations
 *
 * @package db
 */
class db_utils {

	/**
	 * Escape double quotes in an SQL query
	 *
	 * @static
	 *
	 * @param string $_str SQL query
	 * @return string
	 */
	function escape($_str) {
		$_str = str_replace("\\\"", "\"", $_str);
		$_str = str_replace("\"", "\\\"", $_str);

		return $_str;
	}

	/**
	 * Return an array of parent tables of a specified table, as defined in $_DB_SCHEMA
	 *
	 * @param string $table_name
	 * @return array
	 */
	function parents($table_name) {
		global $_DB_SCHEMA;

		$parents = array();

		$fkeys = $_DB_SCHEMA['fkeys'];
		foreach ($fkeys as $fkey) {
			if ($fkey['child_table'] == $table_name) {
				$parents[$fkey['child_column']] = $fkey['parent_table'];
			}
		}
		return $parents;
	}
}

?>