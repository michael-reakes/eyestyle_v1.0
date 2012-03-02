<?
	require_once("inc.php");
	$subscriber = new dbo_list('subscriber','WHERE status = "active"');
	$active_len = $subscriber->count();
	$subscriber = new dbo_list('subscriber','WHERE status = "inactive"');
	$inactive_len = $subscriber->count();

	$member_list = new dbo_list('customer');
	$email = array();
	foreach($member_list->get_all() as $member){
		$email[] = $member->email;
	}
	$email_list = '\''.implode('\',\'',$email).'\'';
	$select_clause = "SELECT * FROM subscriber";
	$where_clause = "WHERE `status` = \"active\" AND subscriber.email NOT IN ($email_list)";
	$subscriber_list = new record_list($select_clause,$where_clause);
	$non_member_len = $subscriber_list->count();
?>



<div class="panel">
	<div class="title">Newsletter Management</div>
	<?php if (access::verify($_STAFF->access, 'newsletter.account')) {?>
		<div class="subtitle">Subscriber Management</div>
		<div class="list">
			<a href="newsletter_subscriber.php?view=all">All Subscriber List (<?=$active_len?>)</a>
			<div class="sublist" style="padding-left:15px"><a href="newsletter_subscriber.php?view=member">Customer List (<?=$active_len - $non_member_len?>)</a></div>
			<div class="sublist" style="padding-left:15px"><a href="newsletter_subscriber.php?view=nonmember">Non-customer List (<?=$non_member_len?>)</a></div>
			<a href="newsletter_subscriber.php?view=inactive">Unsubscribed List (<?=$inactive_len?>)</a>
		</div>
	<?php } ?>
	<?php if (access::verify($_STAFF->access, 'newsletter.newsletter')) {?>
		<div class="subtitle">Newsletter Management</div>
		<div class="list">
			<a href="newsletter_newsletters.php">Newsletter List</a>
		</div>
	<?php } ?>
</div>
