<?php
function paypal_ipn() {
	global $_CONFIG;
	// create by victor, 2009/09/04
	// official source from paypal : http://www.paypal.com/cgi-bin/webscr?cmd=p/pdn/ipn-codesamples-pop-outside#php
	
	
	// read the post from PayPal system and add 'cmd'
	$req = 'cmd=_notify-validate';
	
	foreach ($_POST as $key => $value) {
		$value = urlencode(stripslashes($value));
		$req .= "&$key=$value";
	}
	/*
	// post back to PayPal system to validate
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Host: www.paypal.com:443\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
	$fp = fsockopen ('ssl://'.$_CONFIG['paypal']['server'], 443, $errno, $errstr, 30);
	
	if (!$fp) {
		echo 'here';exit;
		// HTTP ERROR
	} else {
		fputs ($fp, $header . $req);
		while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			if (strcmp ($res, "VERIFIED") == 0) {
				return true;
			}
			else if (strcmp ($res, "INVALID") == 0) {
				return false;
			}
		}
		fclose ($fp);
	}
	*/

	// post back to PayPal system to validate
	$res = get_web_page('https://'.$_CONFIG['paypal']['server'].'/cgi-bin/webscr', $req);
	if ($res['errno'] != 0) {
		// HTTP ERROR
	} else {
		if (strcmp ($res['content'], "VERIFIED") == 0) {
			return true;
		}
		else if (strcmp ($res['content'], "INVALID") == 0) {
			return false;
		}
	}

	return false;
}

function get_web_page( $url,$curl_data )
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,         // return web page
        CURLOPT_HEADER         => false,        // don't return headers
        CURLOPT_FOLLOWLOCATION => true,         // follow redirects
        CURLOPT_ENCODING       => "",           // handle all encodings
        CURLOPT_USERAGENT      => "",     // who am i
        CURLOPT_AUTOREFERER    => true,         // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,          // timeout on connect
        CURLOPT_TIMEOUT        => 120,          // timeout on response
        CURLOPT_MAXREDIRS      => 10,           // stop after 10 redirects
        CURLOPT_POST            => 1,            // i am sending post data
        CURLOPT_POSTFIELDS     => $curl_data,    // this are my post vars
        CURLOPT_SSL_VERIFYHOST => 0,            // don't verify ssl
        CURLOPT_SSL_VERIFYPEER => false,        //
        CURLOPT_VERBOSE        => 1                //
    );

    $ch      = curl_init($url);
    curl_setopt_array($ch,$options);
    $content = curl_exec($ch);
    $err     = curl_errno($ch);
    $errmsg  = curl_error($ch) ;
    //$header  = curl_getinfo($ch);
    curl_close($ch);
	
	$header = array(
		'errno' => $err,
		'errmsg' => $errmsg,
		'content' => $content
	);
    return $header;
} 