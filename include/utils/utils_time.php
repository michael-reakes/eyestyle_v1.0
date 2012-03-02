<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Functions to handle date & time
 *
 * @package utils
 */
class utils_time {

	/**
	 * Get the timestamp of a database "datetime" or "date"
	 *
	 * @static
	 *
	 * @param string $db_datetime Database "datetime" or "date"
	 * @param boolean $is_gmt Whether the database time is in GMT
	 * @return int
	 */
	function timestamp($db_datetime, $is_gmt = false) {
		if ($db_datetime != "0000-00-00 00:00:00" && $db_datetime != '0000-00-00') {
			$year = substr($db_datetime, 0, 4);
			$month = substr($db_datetime, 5, 2);
			$day = substr($db_datetime, 8, 2);
			$hour = substr($db_datetime, 11, 2);
			$minute = substr($db_datetime, 14, 2);
			$second = substr($db_datetime, 17, 2);

			if ($is_gmt) {
				$timestamp = gmmktime($hour, $minute, $second, $month, $day, $year);
			} else {
				$timestamp = mktime($hour, $minute, $second, $month, $day, $year);
			}

			return $timestamp;
		} else {
			return 0;
		}
	}

	/**
	 * Format a database "datetime" or "date" to local date & time format
	 *
	 * @static
	 *
	 * @param string $db_datetime Database "datetime" or "date"
	 * @param boolean $is_gmt Whether the database time is in GMT
	 * @return string
	 */
	function datetime($db_datetime, $is_gmt = false) {
		if ($db_datetime != "0000-00-00 00:00:00" && $db_datetime != '0000-00-00') {
			$timestamp = utils_time::timestamp($db_datetime, $is_gmt);

			$datetime = date("d/m/Y H:i:s", $timestamp);

			return $datetime;
		} else {
			return '--';
		}
	}

	/**
	 * Format a database "datetime" or "date" to local date format
	 *
	 * @static
	 *
	 * @param string $db_datetime Database "datetime" or "date"
	 * @param boolean $is_gmt Whether the database time is in GMT
	 * @return string
	 */
	function date($db_datetime, $is_gmt = false) {
		if ($db_datetime != "0000-00-00 00:00:00") {
			$timestamp = utils_time::timestamp($db_datetime, $is_gmt);

			$datetime = date("d/m/Y", $timestamp);

			return $datetime;
		} else {
			return '--';
		}
	}

	/**
	 * Format a database "datetime" or "date" to local day/month format
	 *
	 * @static
	 *
	 * @param string $db_datetime Database "datetime" or "date"
	 * @param boolean $is_gmt Whether the database time is in GMT
	 * @return string
	 */
	function month($db_datetime, $is_gmt = false) {
		if ($db_datetime != "0000-00-00 00:00:00") {
			$timestamp = utils_time::timestamp($db_datetime, $is_gmt);

			$datetime = date("d/m", $timestamp);

			return $datetime;
		} else {
			return '--';
		}
	}

	/**
	 * Format a database "datetime" or "date" to the time left
	 *
	 * @static
	 *
	 * @param string $db_datetime Database "datetime" or "date"
	 * @param boolean $is_gmt Whether the database time is in GMT
	 * @return string
	 */
	function time_left($db_datetime, $is_gmt = false) {
		$timestamp = utils_time::timestamp($db_datetime, $is_gmt);

		$time_left =  $timestamp - time();

		if ($time_left < 60) { // less than 1 minute
			$string = '<1m';
		} elseif ($time_left < 3600) { // less than 1 hour
			$string = (int)($time_left/60).'m';
		} elseif ($time_left < 3600*24) { // less than 1 day
			$string = (int)($time_left/3600).'h '.(int)(($time_left%3600)/60).'m';
		} else { // more than 1 day
			$string = (int)($time_left/3600/24).'d '.(int)(($time_left%(3600*24))/3600).'h '.(int)((($time_left%(3600*24))%3600)/60).'m';
		}

		return $string;
	}

	/**
	 * Format a timestamp to database "datetime" format
	 *
	 * @static
	 *
	 * @param int $timestamp Timestamp to be formatted
	 * @return string
	 */
	function db_datetime($timestamp = -1) {
		if ($timestamp === -1) {
			$timestamp = time();
		}

		if ($timestamp == 0) {
			return '0000-00-00 00:00:00';
		} else {
			return date('Y-m-d H:i:s', $timestamp);
		}
	}

	/**
	 * Format a local date into database "datetime" format
	 *
	 * @static
	 *
	 * @param string $calendar_date Local date in "DD/MM/YYYY" format
	 * @param boolean $end_of_day Whether to use the last second of the day
	 * @return string
	 */
	function db_datetime_str($calendar_date, $end_of_day = false) {
		$date = substr($calendar_date, 0, 2);
		$month = substr($calendar_date, 3, 2);
		$year = substr($calendar_date, 6, 4);

		$time = $end_of_day?" 23:59:59":" 00:00:00";

		return $year.'-'.$month.'-'.$date.$time;
	}
}
?>