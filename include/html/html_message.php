<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Message system based on session
 *
 * @package html
 */
class html_message {

	/**
	 * Add a message. Message type can be "error", "warning" or "info".
	 *
	 * @static
	 *
	 * @param string $msg Message
	 * @param string $type Message type. "error", "warning" or "info.
	 */
	function add($msg, $type = 'error') {
		if (!isset($_SESSION['_MSG'])) {
			$_SESSION['_MSG'] = array('error'=>array(), 'warning'=>array(), 'info'=>array());
		}
		$_SESSION['_MSG'][$type][] = $msg;
	}

	/**
	 * Return HTML codes to display messages.
	 *
	 * @param string $type Type of messages to show. "all", "error", "warning" or "info"
	 * @param string $template Specify a template file to be used
	 * @return string
	 */
	function show($type = 'all', $template = '') {
		global $_CONFIG;

		$tpl = new html_template();


		if (($type == 'all' || $type == 'error') && !empty($_SESSION['_MSG']['error'])) {
			$tpl->set('has_error', true, true);
			$tpl->set('error', implode('<br/>', $_SESSION['_MSG']['error']));
			$_SESSION['_MSG']['error'] = array();
		} else {
			$tpl->set('has_error', false, true);
		}

		if (($type == 'all' || $type == 'warning') && !empty($_SESSION['_MSG']['warning'])) {
			$tpl->set('has_warning', true, true);
			$tpl->set('warning', implode('<br/>', $_SESSION['_MSG']['warning']));
			$_SESSION['_MSG']['warning'] = array();
		} else {
			$tpl->set('has_warning', false, true);
		}

		if (($type == 'all' || $type == 'info') && !empty($_SESSION['_MSG']['info'])) {
			$tpl->set('has_info', true, true);
			$tpl->set('info', implode('<br/>', $_SESSION['_MSG']['info']));
			$_SESSION['_MSG']['info'] = array();
		} else {
			$tpl->set('has_info', false, true);
		}

		if (!empty($template)) {
			return $tpl->fetch($template);
		} elseif (isset($_CONFIG['html_message']['template'])) {
			return $tpl->fetch($_CONFIG['html_message']['template']);
		} else {
			trigger_error('Template for html_message is not defined in $_CONFIG', E_USER_ERROR);
			return '';
		}
	}
}
?>