<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Session handler funtions for using database as PHP session storage
 * All functions in this class are static
 *
 * @package db
 */
class db_session{

	function open($sess_save_path, $sess_name) {
		db_session::gc(12*60*60); // default session max life time
		return true;
	}

	function close() {
		return true;
	}

	function read( $sess_id ) {
		global $_DB;

		$_DB->sql_query("SELECT data FROM session WHERE session_id='$sess_id'");
		$result = $_DB->sql_fetchrow();
		if($result != FALSE) {
			return $result['data'];
		} else {
			$_DB->sql_query("INSERT INTO session (session_id, last_updated, data) VALUES ('$sess_id', NOW(), '')");
			return "";
		}
	}

	function write( $sess_id, $sess_data ) {
		global $_DB;
		$sess_data = addslashes($sess_data);
		$_DB->sql_query("UPDATE session SET data = '$sess_data', last_updated = NOW() WHERE session_id = '$sess_id'");
		return true;
	}

	function destroy( $sess_id ) {
		global $_DB;
		$_DB->sql_query("DELETE FROM session WHERE session_id = '$sess_id'");
		return true;
	}

	function gc( $sess_maxlife ) {
		global $_DB;
		$_DB->sql_query("DELETE FROM session WHERE UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(last_updated) > $sess_maxlife");
		return true;
	}
}
?>