<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Apply access level to Admin area.
 *
 * @package access
 */
class access {

	/**
	 * Verify staff access rights against page permission
	 *
	 * @static
	 * @param string $access_right staff access rights concatenated with "|"
	 * @param string $access_code page permission assigned with $_ACCESS
	 * @return boolean
	 */
	function verify($access_right, $access_code) {
		if ($access_code == 'public' || $access_code == 'all') {
			return true;
		} elseif ($access_right == '') {
			return false;
		} else {
			$rights = explode('|', $access_right);
			foreach ($rights as $right) {
				if (strpos($access_code, $right) === 0) {
					return true;
				}
				if (strpos($right, $access_code) === 0) {
					return true;
				}
			}
			return false;
		}
	}

	/**
	 * Generate an access right string to be stored in the staff record
	 *
	 * @static
	 * @param array $ticked_rights An array of accessible codes
	 * @return string
	 */
	function access_string($ticked_rights) {
		$rights = array();

		foreach ($ticked_rights as $ticked) {
			$found = false;
			foreach ($rights as $right) {
				if (strpos($ticked, $right) === 0) {
					$found = true;
				}
			}
			if (!$found) {
				$rights[] = $ticked;
			}
		}

		return implode('|', $rights);
	}
}
?>