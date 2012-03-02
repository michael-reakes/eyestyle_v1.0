<?php
$_ACCESS = 'newsletter.newsletter';
$_SECTION = 'Newsletter Management';
$_PAGE = 'Send Newsletter';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$newsletter = new dbo('newsletter', $_GET['id']);

$form = html_form::get_form('form_newsletter_newsletter_preview');

if (!$form->validate()) {
	$form->set_failure();
}

$mode = $form->get('mode');
if (isset($_GET['mode'])) {
	$mode = $_GET['mode'];
}

if ($mode == 'test') {
	if ($form->get('test_to') == '') {
		$form->set_failure('Please enter at least one email address for test mode');
	} else {
		$emails = explode(';', $form->get('test_to'));
		$emails_to_send = array();
		$valid = true;
		foreach ($emails as $email) {
			if (!empty($email)) {
				$valid = $valid && utils_validation::email(trim($email));
				$emails_to_send[] = array('fullname'=>'Test member', 'email'=>trim($email));
			}
		}
		if (!$valid) {
			$form->set_failure('One or more email addresses entered are invalid');
		}
	}
} 
else if ($mode == 'resend'){
	$emails = $_SESSION['resend_emails'];
	$valid = true;
	foreach ($emails as $email) {
		if (!empty($email)) {
			$valid = $valid && utils_validation::email(trim($email));
			$emails_to_send[] = array('fullname'=>'Subscriber', 'email'=>trim($email));
		}
	}
	//reset the session here
	unset($_SESSION['resend_emails']);
}
else {
	// Valid to send - add groups to email array
	$customer = $form->get('customer');
	$non_customer = $form->get('non_customer');
	$emails_to_send = array();

	if ((empty($customer) && empty($non_customer))) {
		$form->set_failure('Please select at least one group to send to.');
	}


	if (!empty($customer) && !empty($non_customer)){
		$select_clause = "SELECT subscriber.*,customer.fullname AS fullname FROM subscriber subscriber LEFT JOIN customer customer ON subscriber.email = customer.email";
		$where_clause = "WHERE subscriber.status = \"active\"";
		$newsletter_list = new record_list($select_clause,$where_clause);

		foreach($newsletter_list->get_all() as $subscriber) {
			if ($subscriber->fullname == '')	$name = $subscriber->email;
			else $name = $subscriber->fullname;
			$emails_to_send[] = array('fullname'=>$name, 'email'=>$subscriber->email);
		}
	
	}
	else if (!empty($customer)){
		$select_clause = "SELECT subscriber.*,customer.fullname as fullname FROM subscriber subscriber INNER JOIN customer customer ON subscriber.email = customer.email";
		$where_clause = "WHERE subscriber.status = \"active\"";
		$subscriber_list = new record_list($select_clause,$where_clause);
		foreach($subscriber_list->get_all() as $subscriber){
			$emails_to_send[] = array('fullname'=>$fullname, 'email'=>$subscriber->email);
		}
	}
	else if (!empty($non_customer)){
		$member_list = new dbo_list('customer');
		$email = array();
		foreach($member_list->get_all() as $member){
			$email[] = $member->email;
		}
		$email_list = '\''.implode('\',\'',$email).'\'';
		$select_clause = "SELECT * FROM subscriber";
		$where_clause = "WHERE `status` = \"active\" AND subscriber.email NOT IN ($email_list)";
		$subscriber_list = new record_list($select_clause,$where_clause);
		foreach($subscriber_list->get_all() as $subscriber){
			$emails_to_send[] = array('fullname'=>$subscriber->email, 'email'=>$subscriber->email);
		}
	}


	if (empty($emails_to_send)) {
		$form->set_failure('There are no subscribers in selected categories.');
	} else {
		$newsletter->date_last_sent = utils_time::db_datetime();
		$newsletter->update();
	}
}


$form_resend = new html_form('form_newsletter_newsletter_send', 'newsletter_newsletter_send.php', 'GET');
$form_resend->add(new html_form_hidden('id',$_GET['id']));
$form_resend->add(new html_form_hidden('mode','resend'));
$form_resend->add(new html_form_button('resend','Resend'));

$js_emails = array();
foreach ($emails_to_send as $email) {
	$js_emails[] = 'new Array("'.$email['fullname'].'","'.$email['email'].'")';
}

$breadcrumbs = array('Home'=>'./', $_SECTION=>'newsletter_subscriber.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_newsletter.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<div class="page_subtitle">Sending Newsleeter</div>
		<div class="info">
			<table cellpadding="0" cellspacing="0" border="0">
				<tr>
					<td class="attribute_label">Total Addresses:</td>
					<td class="attribute_value"><?=count($emails_to_send)?></td>
				</tr>
				<tr>
					<td class="attribute_label">Completed:</td>
					<td class="attribute_value"><input type="text" id="completed" value="0" style="border:0px;width:100px" /></td>
				</tr>
				<tr>
					<td class="attribute_label">Failed:</td>
					<td class="attribute_value">
						<input type="text" id="failed" value="0" style="border:0px;width:50px;" />
						<div name="divResend" id="divResend" style="display:none;padding-left:10px">
							<?=$form_resend->output_open()?>
							<?=$form_resend->output('id')?>
							<?=$form_resend->output('mode')?>
							<?=$form_resend->output('resend')?>
							<?=$form_resend->output_close()?>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<td class="attribute_label">Actions:</td>
					<td class="attribute_value">
						<iframe name="mconsole" width="500" height="300"></iframe><br/><br/>
						<input type="checkbox" id="autoscroll" checked="checked" /> Auto Scroll Console Screen
					</td>
				</tr>
			</table>
		</div>
		<hr />
		<div class="padded_row">
			<input type="button" value="Finish" onclick="javascript:window.location.href='newsletter_newsletters.php'" />
		</div>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>
<script type="text/javascript" language="javascript">
<!--

	var emails_to_send = Array(<?=implode(',', $js_emails)?>);
	var total = emails_to_send.length;
	var sent = 0;
	var failed = 0;
	var i = 0;
	var http_request = false;

	window.mconsole.document.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html><body style="background-color: #000; font-family: Courier New, Courier, mono; font-size: 12px; color: #FFF;">');

	sendEngine();

	function sendEngine() {
		email = emails_to_send[i];
		window.mconsole.document.write("Sending to " + email[1] + "... ");
		url = "action_newsletter_newsletter_send_engine.php?id=<?=$newsletter->newsletter_id?>&fullname=" + email[0] + "&email=" + email[1];
		makeRequest(url);
	}

	function makeRequest(url) {
		if (window.XMLHttpRequest) { // Mozilla, Safari,...
            http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType) {
                http_request.overrideMimeType('text/xml');
                // See note below about this line
            }
        } else if (window.ActiveXObject) { // IE
            try {
                http_request = new ActiveXObject("Msxml2.XMLHTTP");
            } catch (e) {
                try {
                    http_request = new ActiveXObject("Microsoft.XMLHTTP");
                } catch (e) {}
            }
        }

        if (!http_request) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }

        http_request.onreadystatechange = alertContents;
        http_request.open('GET', url, true);
        http_request.send(null);

    }

    function alertContents() {
        if (http_request.readyState == 4) {
            if (http_request.status == 200) {
            	xmldoc = http_request.responseXML;
            	request_status = xmldoc.getElementsByTagName('status').item(0).firstChild.nodeValue;

            	result_string = request_status;
            	if (request_status == 'Success') {
            		document.getElementById('completed').value ++;
            	} else {
            		document.getElementById('failed').value ++;
            		result_string += " - " + xmldoc.getElementsByTagName('error').item(0).firstChild.nodeValue;
					//display resend
					document.getElementById('divResend').style.display = "inline";
				}
            	result_string += "<br/>";

				window.mconsole.document.write(result_string);

                if (i<emails_to_send.length-1) {
	                i++;
	                sendEngine();
                } else {
                	window.mconsole.document.write("<b>Newsletter sending completed!</b>");
                	window.mconsole.document.close();
                }
            }
			else {
				window.mconsole.document.write("An error has occured");
				//display resend
				document.getElementById('divResend').style.display = "inline";
				
            }
        }
    }

-->
</script>
<script type="text/javascript" language="javascript">
<!--
	var step = 40;
	var tid;
	var oldPageYOffset;

	function scrollIframe () {
	  if (window.mconsole != null && window.mconsole.scrollBy && document.getElementById("autoscroll").checked) {
		var ifr = window.mconsole;
		ifr.oldPageYOffset = ifr.pageYOffset;
		ifr.scrollBy(0, step);
	  }
	}

	setInterval('scrollIframe()', 100);
-->
</script>