<?php
/**
 * @copyright S3 Group Pty Ltd (www.s3group.com.au)
 */

/**
 * Text formatting for HTML
 *
 * @package html
 */
class html_text {

	/**
	 * Transform lines of text into bullet points.
	 *
	 * @static
	 *
	 * @param string $text Lines of text
	 * @return string
	 */
	function bullet($text,$num_lines=0) {
		$output = "";
		if (!empty($text)) {
			$array = explode("\n", $text);
			if ($num_lines == 0) { 
				foreach ($array as $bullet) {
					if (!empty($bullet)) {
						$output .= '<li>'.$bullet.'</li>';
					}
				}
			} else {
				for ($i=0; $i<$num_lines; $i++) {
					if (isset($array[$i])) {
						$output .= '<li>'.$array[$i].'</li>';
					}
				}
			}
		}
		return $output;
	}

	/**
	 * Transform text with the format [text]:[text]\r\n into a table.
	 *
	 * @static
	 *
	 * @param string $text Text to be transformed
	 * @return string
	 */
	function table($text) {
		$output = "";
		if (!empty($text)) {
			$output .= '<table border="0" cellspacing="1" cellpadding="0" width="100%">';
			$array = explode("\n", $text);
			for ($i=0; $i<count($array); $i++) {
				if (!empty($array[$i])) {
					$row = explode(":", $array[$i], 2);
					$title = isset($row[0]) ? $row[0] : '';
					$data = isset($row[1]) ? $row[1] : '';
					if ($i%2 == 0) {
						$output .= '<tr class="row_ab_a"><td class="form_title" width="30%">'.$title.'</td><td bgcolor="#EBF1F6" width="70%">'.$data.'</td></tr>';
					} else {
						$output .= '<tr class="row_ab_b"><td class="form_title">'.$title.'</td><td bgcolor="#FFFFFF">'.$data.'</td></tr>';
					}
				}
			}
			$output .= '</table>';
		}

		return $output;
	}

	/**
	 * Format decimal number to curreny format
	 *
	 * @static
	 *
	 * @param float $decimal Decimal number to be formatted
	 * @return string
	 */
	function currency($decimal) {
		if ($decimal != 0) {
			$currency = number_format($decimal, 2, '.', ',');
			return "$".$currency;
		} else {
			return '0';
		}
	}

	/**
	 * Replace "\r\n" with "<br/>"
	 *
	 * @param string $text Text to be transformed
	 * @return string
	 */
	function text($text) {
		return nl2br($text);
	}

	/**
	 * Truncate text to a specified number of characters
	 *
	 * @param string $text Text to be transformed
	 * @param int $chars Maximum number of characters
	 * @return string
	 */
	function digest($text, $chars) {
		$text = strip_tags($text);
		if (strlen($text) > $chars) {
			return substr($text, 0, $chars-3);
		} else {
			return $text;
		}
	}

	/**
	 * Return the HTML code inside "<body>" and "</body>" tags.
	 *
	 * @static
	 *
	 * @param string $html HTML codes
	 * @return string
	 */
	function html_body($html) {
		if (strpos($html, '<body') !== false) {
			$html = substr($html, strpos($html, '<body')+5);
			$html = substr($html, strpos($html, '>')+1);
			$html = substr($html, 0, strpos($html, '</body>'));
		}
		return $html;
	}

	/**
	 * @static
	 *
	 * @param string $text
	 * @return string
	 */
	function check_empty($text) {
		if (empty($text) || $text == '0.00') {
			return '';
		} else {
			return $text;
		}
	}

	/**
	 * Fill zeros before a number
	 *
	 * @static
	 *
	 * @param int $int Number to be filled
	 * @param int $num_digit Maximum number of digits
	 * @return string
	 */
	function pad_zero($int, $num_digit) {
		if (strlen($int) < $num_digit) {
			for ($i = 0; $i < $num_digit - strlen($int); $i ++) {
				$int = '0'.$int;
			}
		}
		return $int;
	}

	/**
	 * Escape double quote with HTML &quot;
	 *
	 * @static
	 *
	 * @param string $_str
	 * @return string
	 */
	function escape($_str) {
		return str_replace('"', '&quot;', $_str);
	}
}
?>