<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * HTTP functions
 *
 * @package http
 */
class http {

	/**
	 * Build a GET query string with an array of parametres.
	 *
	 * @static
	 *
	 * @param array $para_array An associative array of parametres. Array("name"=>"value").
	 * @param mixed $exclude One parametre or an array of parametres to be excluded in the built query.
	 * @return string
	 */
	function build_query($para_array, $exclude = '') {
		$queries = array();
		$exclude_array = is_array($exclude) ? $exclude : array($exclude);
		$exclude_array[] = 'x';
		$exclude_array[] = 'y';

		if (!empty($para_array)) {
			foreach ($para_array as $key=>$value) {
				if (array_search($key, $exclude_array) === false) {
					if (is_array($value)) {
						foreach ($value as $v) {
							if ($v != '') {
								$queries[] = $key.'[]='.$v;
							}
						}
					} else {
						if ($value != '') {
							$queries[] = $key.'='.$value;
						}
					}
				}
			}

			return implode("&", $queries);
		} else {
			return "";
		}
	}

	/**
	 * Return HTML codes of hidden fileds with an array of parametres.
	 *
	 * @static
	 *
	 * @param array $para_array An associative array of parametres. Array("name"=>"value").
	 * @param mixed $exclude One parametre or an array of parametres to be excluded in the output hidden fields.
	 * @return string
	 */
	function hidden_fields($para_array, $exclude = '') {
		$string = '';
		$exclude_array = is_array($exclude) ? $exclude : array($exclude);
		$exclude_array[] = 'x';
		$exclude_array[] = 'y';

		foreach ($para_array as $key=>$value) {
			if (array_search($key, $exclude_array) === false) {
				if (is_array($value)) {
					foreach ($value as $v) {
						if ($v != '') {
							$string .= '<input type="hidden" name="'.$key.'[]" value="'.$v.'"/>';
						}
					}
				} else {
					if ($value != '') {
						$string .= '<input type="hidden" name="'.$key.'" value="'.$value.'"/>';
					}
				}
			}
		}
		return $string;
	}

	/**
	 * Fatal error and redirects to the generic error page specified in config file.
	 *
	 * @static
	 *
	 * @param string $msg Error message
	 */
	function halt($msg='') {
		global $_CONFIG, $_ROOT;

		if ($msg != '') {
			html_message::add($msg);
		}
		if (!headers_sent()) {
			header("Location: ".$_ROOT.$_CONFIG['http']['error_page']);
		} else {
			trigger_error("http::halt() - headers already sent.", E_USER_ERROR);
		}
		exit;
	}

	/**
	 * Redirects to fatal error page if the specified statement is true.
	 *
	 * @static
	 *
	 * @param boolean $condition Statement to be tested.
	 */
	function halt_if($condition) {
		if ($condition) {
			http::halt();
		}
	}

	/**
	 * Redirect to another page using the HTTP header "Location".
	 *
	 * @static
	 *
	 * @param string $url URL to be redirected to
	 */
	function redirect($url) {
		if (!headers_sent()) {
			header("Location: ".$url);
		} else {
			trigger_error("http::redirect - headers already sent.", E_USER_ERROR);
		}
		exit;
	}

	/**
	 * Output a relative path with domain and path, preceded with either "http://" or "https://"
	 *
	 * @static
	 *
	 * @param string $url Relative path
	 * @param boolean $https Whether to use HTTPS
	 * @return string
	 */
	function url($url, $https=false) {
		global $_CONFIG;

		if (substr($url, 0, 1) == '/') {
			$url = substr($url, 1);
		}

		if (SID != "") {
			$url = str_replace(SID, "", $url);
			if (strlen($url) > 0) {
				if (strrpos($url, '?') === strlen($url) - 1 || strrpos($url, '&') === strlen($url) - 1) {
					$url = substr($url, 0, -1);
				}
			}
			$url .= ( ( strpos($url, '?') != false ) ? '&' : '?' ) . SID;
		}

		if ($https) {
			$url = str_replace('PHPSESSID='.session_id(), "", $url);
			if (strlen($url) > 0) {
				if (strrpos($url, '?') === strlen($url) - 1 || strrpos($url, '&') === strlen($url) - 1) {
					$url = substr($url, 0, -1);
				}
			}
			$url .= ( ( strpos($url, '?') != false ) ? '&' : '?' ) . 'PHPSESSID=' . session_id();
		}

		$url = ltrim(substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')+1), SITE_APP_ROOT).'/'.$url;

		if ($https) {
			$host = !empty($_CONFIG['site']['https_host']) ? $_CONFIG['site']['https_host'] : $_SERVER['SERVER_NAME'];
			$port = !empty($_CONFIG['site']['https_port']) ? ':'.$_CONFIG['site']['https_port'] : '';
			$url = 'https://'.$host.$port.$url;
		} else {
			$host = !empty($_CONFIG['site']['http_host']) ? $_CONFIG['site']['http_host'] : $_SERVER['SERVER_NAME'];
			$port = !empty($_CONFIG['site']['http_port']) ? ':'.$_CONFIG['site']['http_port'] : '';
			$url = 'http://'.$host.$port.$url;
		}

		return $url;
	}

	/**
	 * Store the current URL in session.
	 *
	 * @static
	 */
	function register_path() {
		global $_CONFIG;

		$var = '_PATH'.(isset($_CONFIG['site']['application']) ? '_'.$_CONFIG['site']['application'] : '');

		if (!isset($_SESSION[$var])) {
			$_SESSION[$var] = '';
		}
		$query = http::build_query($_GET);
		if ($query != '') {
			$query = '?'.$query;
		}
		$_SESSION[$var] = $_SERVER['PHP_SELF'].$query;
	}

	/**
	 * Retrieve the URL stored in session.
	 *
	 * @static
	 *
	 * @return string
	 */
	function get_path() {
		global $_CONFIG;

		$var = '_PATH'.(isset($_CONFIG['site']['application']) ? '_'.$_CONFIG['site']['application'] : '');

		if (isset($_SESSION[$var])) {
			return $_SESSION[$var];
		}
	}
}
?>