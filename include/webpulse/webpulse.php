<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Functions to handle HTML output.
 *
 * @package html
 */
class webpulse {

	/**
	 * Output the HTML codes of a link within a WebPulse grid.
	 *
	 * @static
	 *
	 * @param string $link The url for the link (e.g. customer.php)
	 * @param string $title The title that will be displayed for the link (e.g. Product Name)
	 * @return string $id The ID of the object to be targeted
	 */
	 function grid_link($link, $title, $img=NULL, $target=NULL) {
	 	$target = $target == NULL ? '' : ' target="'.$target.'"';
		
	 	if ($img == null) {
			return '<a href="'.$link.'" title="'.$title.'"'.$target.'>'.$title.'</a>';
		} else {
			return '<a href="'.$link.'" title="'.$title.'"'.$target.'><img src="'.$img.'" alt="'.$title.'" /></a>';
		}
	}
	
	/**
	 * Checks whether a variable is set or not
	 *
	 * @static
	 *
	 * @param string $prop
	 * @param string $default
	 * @return bool
	 */
	function is_set(&$prop, $default=''){
		return isset($prop) ? $prop : $default;
	}
	
	/**
	 * Get the next sort order from a specified database table
	 *
	 * @static
	 *
	 * @param string $table_name The name of the database table (e.g. category)
	 * @return int
	 */
	function get_next_sort_order($table, $field='sort_order'){
		global $db;
		$max = $db->max($table, $field);
		if ($max !== false) {
			return $max + 10;
		} else {
			return 0;
		}
	}
	
	/**
	 * Log an operation performed in WebPulse
	 *
	 * @static
	 *
	 * @param string $type The section that the log action is involved in (e.g. crm, emarketing)
	 * @param string $message The description for the log operation
	 * @return string $data An optional parameter to store information that can be retrieved from the log (e.g. xml data)
	 */
	 function log($type, $message, $reference_id=0, $data=null) {
	 	global $_ACCESS, $db;
		
	 	$log = $db->dbo('log');
		$log->id = NULL;
		$log->type = $type;
		$log->message = $message;
		$log->reference_id = $reference_id;
		$log->data = $data;

		if ($_ACCESS != null) {
			$log->user_id = $_ACCESS->get_user_id();
		} else {
			$log->user_id = 0;
		}

		$log->location = webpulse::base_root().webpulse::request_uri();
		
		$referer = webpulse::referer_uri();
		if (!empty($referer)) {
			$log->referer = $referer;
		} else {
			$log->referer = 'None';
		}

		$log->hostname = webpulse::remote_address();
		$log->timestamp = time();
		return $log->insert();
	 }
	
	/**
	 * Retrieve the base root for the url
	 *
	 * @static
	 *
	 * @return string
	 */
	function base_root() {
		$root = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http';
		return $root.'://'.$_SERVER['HTTP_HOST'];
	}
	
	/**
	 * Retrieve the request uri
	 *
	 * @static
	 *
	 * @return string
	 */
	function request_uri() {
	  if (isset($_SERVER['REQUEST_URI'])) {
		$uri = $_SERVER['REQUEST_URI'];
	  } else {
		if (isset($_SERVER['argv'])) {
		  $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['argv'][0];
		} else {
		  $uri = $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
		}
	  }
	
	  return $uri;
	}

	/**
	 * Retrieve the referer uri
	 *
	 * @static
	 *
	 * @return string 
	 */
	function referer_uri() {
		if (isset($_SERVER['HTTP_REFERER'])) {
			return $_SERVER['HTTP_REFERER'];
		} else {
			return 'None';
		}
	}
	
	/**
	 * Retrieve the remote address
	 *
	 * @static
	 *
	 * @return string 
	 */
	function remote_address() {
		if (isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		} else {
			return 'None';
		}
	}
	
	// -----------------------
	// SEARCH ENGINE FUNCTIONS
	// -----------------------
	/**
	 * Append the dbo keywords and description to the global $_KEYWORDS and $_DESCRIPTION variables
	 *
	 * @static
	 *
	 * @return array 
	 */
	function add_meta($dbo) {
		global $_KEYWORDS, $_DESCRIPTION;
		
		if (!isset($_KEYWORDS)) $_KEYWORDS = '';
		if (!isset($_DESCRIPTION)) $_DESCRIPTION = '';
		
		if (!empty($dbo->keywords)) {
			$_KEYWORDS .= !empty($_KEYWORDS) ? ', '.$dbo->keywords : $dbo->keywords;
		}
		
		if (!empty($dbo->description)) {
			if (!empty($_DESCRIPTION) && !text::ends_with($_DESCRIPTION, '.')) {
				$_DESCRIPTION .= '. ';
			}
			$_DESCRIPTION .= $dbo->description;
		}
		
		return array($_KEYWORDS, $_DESCRIPTION);
	}
	
	/**
	 * Retrieve the current page from the cache.
	 *
	 * Note, we do not serve cached pages when status messages are waiting (from
	 * a redirected form submission which was completed).
	 * Because the output handler is not activated, the resulting page will not
	 * get cached either.
	 */
	function page_get_cache() {
		global $_ACCESS;
		
		$cache = NULL;
		
		$msgs = html_message::get();
		$msg_count = $msgs != NULL ? count($msgs['error']) + count($msgs['warning']) + count($msgs['info']) : 0;
		if (!$_ACCESS->is_logged_in() && $_SERVER['REQUEST_METHOD'] == 'GET' && $msg_count == 0) {
			$cache = cache::get(webpulse::base_root() . webpulse::request_uri());

			if (empty($cache)) {
		  		ob_start();
			}
		}

		return $cache;
	}
	
	/**
	 * Store the current page in the cache.
	 *
	 * We try to store a gzipped version of the cache. This requires the
	 * PHP zlib extension (http://php.net/manual/en/ref.zlib.php).
	 * Presence of the extension is checked by testing for the function
	 * gzencode. There are two compression algorithms: gzip and deflate.
	 * The majority of all modern browsers support gzip or both of them.
	 * We thus only deal with the gzip variant and unzip the cache in case
	 * the browser does not accept gzip encoding.
	 *
	 * @see page_header
	 */
	function page_set_cache() {
		global $_ACCESS;

		if (!$_ACCESS->is_logged_in() && $_SERVER['REQUEST_METHOD'] == 'GET') {
			// This will fail in some cases, see page_get_cache() for the explanation.
			if ($data = ob_get_contents()) {
				$cache = TRUE;
				if (function_exists('gzencode')) {
					// We do not store the data in case the zlib mode is deflate.
					// This should be rarely happening.
					if (zlib_get_coding_type() == 'deflate') {
						$cache = FALSE;
					}
					/* else if (zlib_get_coding_type() == FALSE) {
						$data = gzencode($data, 9, FORCE_GZIP);
					}
					*/
					// The remaining case is 'gzip' which means the data is
					// already compressed and nothing left to do but to store it.
				}
				ob_end_flush();
				if ($cache && $data) {
					cache::set(webpulse::base_root() . webpulse::request_uri(), $data, CACHE_TEMPORARY, webpulse::get_headers());
				}
			}
		}
	}
	
	/**
	 * Get the HTTP response headers for the current page.
	 */
	function get_headers() {
		return webpulse::set_header();
	}
	
	/**
	 * Set an HTTP response header for the current page.
	 */
	function set_header($header = NULL) {
	  // We use an array to guarantee there are no leading or trailing delimiters.
	  // Otherwise, header('') could get called when serving the page later, which
	  // ends HTTP headers prematurely on some PHP versions.
	  static $stored_headers = array();
	
	  if (strlen($header)) {
		header($header);
		$stored_headers[] = $header;
	  }
	  return implode("\n", $stored_headers);
	}

	/**
	 * Set HTTP headers in preparation for a page response.
	 *
	 * @see page_set_cache
	 */
	function page_header() {
		if (webpulse::variable_get('cache', 0)) {
			if ($cache = webpulse::page_get_cache()) {
				//bootstrap_invoke_all('init');
				// Set default values:
				$date = gmdate('D, d M Y H:i:s', $cache->created) .' GMT';
				$etag = '"'. md5($date) .'"';
				
				// Check http headers:
				$modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] == $date : NULL;
				if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && ($timestamp = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])) > 0) {
					$modified_since = $cache->created <= $timestamp;
				} else {
					$modified_since = NULL;
				}
				$none_match = !empty($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] == $etag : NULL;

				// The type checking here is very important, be careful when changing entries.
				if (($modified_since !== NULL || $none_match !== NULL) && $modified_since !== false && $none_match !== false) {
					header('HTTP/1.0 304 Not Modified');
					exit();
				}
				
				// Send appropriate response:
				header("Last-Modified: $date");
				header("ETag: $etag");
				
				// Determine if the browser accepts gzipped data.
				if ( zlib_get_coding_type() == 'gzip' || (@strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === false && function_exists('gzencode')) ) {
					// Strip the gzip header and run uncompress.
					$cache->data = gzinflate(substr(substr($cache->data, 10), 0, -8));
				}
				/* elseif (function_exists('gzencode')) {
					header('Content-Encoding: gzip');
				}
				*/
				// Send the original request's headers.  We send them one after
				// another so PHP's header() function can deal with duplicate
				// headers.
				$headers = explode("\n", $cache->headers);
				foreach ($headers as $header) {
					header($header);
				}
				print $cache->data;
				//bootstrap_invoke_all('exit');
				exit();
			} else {
				header("Expires: Sun, 19 Nov 1978 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
			}
		}
	}
	
	/**
	 * Perform end-of-request tasks.
	 *
	 * This function sets the page cache if appropriate, and allows modules to
	 * react to the closing of the page by calling hook_exit().
	 */
	function page_footer() {
		if (webpulse::variable_get('cache', 0)) {
			webpulse::page_set_cache();
		}
		
		//module_invoke_all('exit');
	}
	
	/**
	 * Return a persistent variable.
	 *
	 * @param $name
	 *   The name of the variable to return.
	 * @param $default
	 *   The default value to use if this variable has never been set.
	 * @return
	 *   The value of the variable.
	 */
	function variable_get($name, $default) {
	  global $_CONFIG;
	
	  return isset($_CONFIG[$name]) ? $_CONFIG[$name] : $default;
	}
	
	/**
	 * Set a persistent variable.
	 *
	 * @param $name
	 *   The name of the variable to set.
	 * @param $value
	 *   The value to set. This can be any PHP data type; these functions take care
	 *   of serialization as necessary.
	 */
	function variable_set($name, $value) {
		global $_CONFIG, $db;
		
		$db->lock_table('preference');
		$preference = $db->dbo('preference',$name);
		$preference->delete();
		$db->query('INSERT INTO `preference` (`name`,`value`) VALUES (?,?)','ss',$name,serialize($value));
		$db->execute();
		$db->unlock_tables();

		cache::clear_all('variables');
		
		$_CONFIG[$name] = $value;
	}
	
	/**
	 * Unset a persistent variable.
	 *
	 * @param $name
	 *   The name of the variable to undefine.
	 */
	function variable_del($name) {
		global $_CONFIG, $db;
		
		$preference = $db->dbo('preference',$name);
		$preference->delete();
		
		cache::clear_all('variables');
		
		unset($_CONFIG[$name]);
	}
	
	/**
	 * Load the persistent variable table.
	 *
	 * The variable table is composed of values that have been saved in the table
	 * with variable_set() as well as those explicitly specified in the configuration
	 * file.
	 */
	function variable_init($conf = array()) {
		global $db;
		// NOTE: caching the variables improves performance with 20% when serving cached pages.
		if ($cached = cache::get('variables')) {
			$variables = unserialize($cached->data);
		}
		else {
			$variables = array();
			$db->query('SELECT * FROM preference');
			foreach($db->get_records() as $variable) {
			  $variables[$variable->name] = unserialize($variable->value);
			}
			cache::set('variables', serialize($variables));
		}
			
		foreach ($conf as $name => $value) {
			$variables[$name] = $value;
		}	
		
		return $variables;
	}
	
	function configure_ssl() {
		global $_CONFIG, $_REQUIRE_SSL;
		
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			if (!isset($_REQUIRE_SSL)) {
				$_REQUIRE_SSL = false;
			}
		
			if ($_SERVER['SERVER_PORT']  == 443 || (!empty($_CONFIG['site_https_port']) && $_SERVER['SERVER_PORT'] == $_CONFIG['site_https_port'])) {
				$is_ssl = true;
			} else {
				$is_ssl = false;
			}
		
			$url = $_SERVER['PHP_SELF'];
		
			if ($_REQUIRE_SSL && !$is_ssl) {
				$host = !empty($_CONFIG['site_https_host']) ? $_CONFIG['site_https_host'] : $_SERVER['SERVER_NAME'];
				$port = !empty($_CONFIG['site_https_port']) ? ':'.$_CONFIG['site_https_port'] : '';
				$dir = !empty($_CONFIG['site_https_dir']) ? $_CONFIG['site_https_dir'] : '';
				$url = 'https://'.$host.$port.$dir.$url;
				$query = http::build_query($_GET);
				//$url .= !empty($query) ? '?'.$query.'&'.session_name().'='.session_id() : '?'.session_name().'='.session_id();
				$url .= !empty($query) ? '?'.$query : '';
				http::redirect($url);
			}
			if (!$_REQUIRE_SSL && $is_ssl) {
				$host = !empty($_CONFIG['site_http_host']) ? $_CONFIG['site_http_host'] : $_SERVER['SERVER_NAME'];
				$port = !empty($_CONFIG['site_http_port']) ? ':'.$_CONFIG['site_http_port'] : '';
				$url = 'http://'.$host.$port.$url;
				$query = http::build_query($_GET, session_name());
				$url .= !empty($query) ? '?'.$query : '';
				http::redirect($url);
			}
		}
	}
	
	function _fix_gpc_magic(&$item) {
	  if (is_array($item)) {
		array_walk($item, array('webpulse','_fix_gpc_magic'));
	  }
	  else {
		$item = stripslashes($item);
	  }
	}
	
	/**
	 * Correct double-escaping problems caused by "magic quotes" in some PHP
	 * installations.
	 */
	function fix_gpc_magic() {
		static $fixed = false;
		if (!$fixed && ini_get('magic_quotes_gpc')) {
			array_walk($_GET, array('webpulse','_fix_gpc_magic'));
			array_walk($_POST, array('webpulse','_fix_gpc_magic'));
			array_walk($_COOKIE, array('webpulse','_fix_gpc_magic'));
			array_walk($_REQUEST, array('webpulse','_fix_gpc_magic'));
			$fixed = true;
		}
	}

	/**
	 * Creating an alias (for a specific table (optional) and specific record (optional))
	 *
	 * @param alias
	 * @param table_name, table name for this alias, the function will check
	 *		whether there has been another alias on this particular table.
	 * @param this_id, if record id for this alias is known (in edit mode usually),
	 *   	the function will check whether there is a record with the same alias,
	 *		if the record with the same alias is the same record that we are updating,
	 *		then we don't do anything otherwise we append the id on the alias.
	 *
	 *
	 * @return
	 *   an alias
	 */
	function create_alias($alias, $table_name = false, $this_id = false, $checkInactive=false){
		global $db;

		$alias = strtolower($alias);
		
		$allowed = 'abcdefghijklmnopqrstuvwxyz-1234567890';

		$len = strlen($alias);
		for ($i=($len-1); $i>=0; $i--) {
			if (strpos($allowed, $alias{$i}) === false) {
				$alias = str_replace($alias{$i}, '-', $alias);
			}
		}

		while(strpos($alias,'--') !== false) {
			$alias = str_replace('--','-',$alias);
		}
		
		//Do the uniqueness test for the alias on the given table
		if ($table_name){
			$sql = 'WHERE `alias` = "'.$alias.'"';
			if ($checkInactive) $sql .= ' AND `status` != "inactive"';
			$item_list = new dbo_list($table_name, $sql);
			if ($this_id){ //edit mode
				if ($item = $item_list->get_first()){ //there is a record with this alias
					$id = $table_name.'_id';
					if ($item->$id != $this_id) { //and the alias doesnt belong to this id
						$alias = $alias."-".$this_id; 		 //append id in this case
					}
				}
			} else { //add mode
				if ($item_list->count() >= 1) {
					$counter = 0;
					$orig_alias = $alias;
					$found = true;
					while($found) {
						$counter++;
						$alias = $orig_alias.'-'.$counter;
						$sql = 'WHERE `alias` = "'.$alias.'"';
						if ($checkInactive) $sql .= ' AND `status` != "inactive"';
						$item_list = new dbo_list($table_name, $sql,'','LIMIT 1');
						$found = ($item_list->count() == 1);
					}
				}
			}
		}
		return $alias;
	}
	
	function getFilenameInfo($filename) {
		return array(substr($filename,0,strrpos($filename,'.')),substr($filename,strrpos($filename,'.')+1,strlen($filename)));
	}
}

function htmlutf($s) {
	return htmlentities(utf8_encode($s), ENT_QUOTES, 'utf-8');
}

function htmlutfdecode($s) {
	return utf8_decode(html_entity_decode($s, ENT_QUOTES, 'utf-8'));
}
?>