<?php
$_ACCESS = 'newsletter.subscriber';
$_SECTION = 'Newsletter Management';
$_PAGE = 'Subscriber Accounts';

require_once('inc.php');

http::register_path();

$view_mode = 'active';
$key = '';
if (isset($_GET['key'])) {
	$view_mode = 'search';
	$key = $_GET['key'];
}
if (isset($_GET['view'])){
	switch($_GET['view']){
		case 'member':
			$view_mode = "member";break;
		case 'nonmember':
			$view_mode = "nonmember"; break;
		case 'inactive':
			$view_mode = "inactive"; break;
	}
}

switch ($view_mode) {
	case 'active':
		$select_clause = "SELECT subscriber.*,customer.fullname AS fullname FROM subscriber subscriber LEFT JOIN customer customer ON subscriber.email = customer.email";
		$where_clause = "WHERE subscriber.status = \"active\"";
		$subscriber_list = new record_list($select_clause,$where_clause);
		break;
	case 'search':
		$select_clause = "SELECT subscriber.*,customer.fullname AS fullname FROM subscriber subscriber LEFT JOIN customer customer ON subscriber.email = customer.email";
		$where_clause = 'WHERE subscriber.status = "active" AND subscriber.email LIKE "%'.$key.'%"';
		$subscriber_list = new record_list($select_clause,$where_clause);
		break;
	case 'member':
		$select_clause = "SELECT subscriber.*,customer.fullname as fullname FROM subscriber subscriber INNER JOIN customer customer ON subscriber.email = customer.email";
		$where_clause = "WHERE subscriber.status = \"active\"";
		$subscriber_list = new record_list($select_clause,$where_clause);
		break;
	case 'nonmember':
		$member_list = new dbo_list('customer');
		$email = array();
		foreach($member_list->get_all() as $member){
			$email[] = $member->email;
		}
		$email_list = '\''.implode('\',\'',$email).'\'';
		$select_clause = "SELECT * FROM subscriber";
		$where_clause = "WHERE `status` = \"active\" AND subscriber.email NOT IN ($email_list)";
		$subscriber_list = new record_list($select_clause,$where_clause);
		break;
	case 'inactive':
		$select_clause = "SELECT subscriber.*,customer.fullname AS fullname FROM subscriber subscriber LEFT JOIN customer customer ON subscriber.email = customer.email";
		$where_clause = "WHERE subscriber.status = \"inactive\"";
		$subscriber_list = new record_list($select_clause,$where_clause);
		break;
}

$len = $subscriber_list->count();
if ($view_mode != 'nonmember') $pager = new html_pager($subscriber_list, array('fullname'=>'Fullname','email'=>'Email'),'a');
else $pager = new html_pager($subscriber_list, array('email'=>'Email'),'a');

$form = new html_form('form_newsletter_subscriber', 'action_newsletter_subscriber.php');
$form->add(new html_form_checkbox('check_all', '', 'checkbox', false, "javascript:checkAll('".$form->name."','check_all[]','checked_id[]');"));
$subscribers = $pager->get_page();
foreach ($subscribers as $subscriber) {
	$form->add(new html_form_checkbox('checked_id', $subscriber->subscriber_id, 'checkbox', false, "javascript:checkAllTicked('".$form->name."','checked_id[]','check_all[]');"));
}
$form->add(new html_form_button('submit_add', 'Add subscriber', '', 'submit', true));
$form->add(new html_form_button('submit_active', 'Subcribe', '', 'submit', true));
$form->add(new html_form_button('submit_delete', 'Unsubscribe', '', 'submit', true));
$form->register();

$form_search = new html_form('form_search', $_SERVER['PHP_SELF'], 'GET');
$form_search->add(new html_form_text('key', false, $key));
$form_search->add(new html_form_image_button('submit', 'images/icon_search.gif', '', 'icon_btn'));

$breadcrumbs = array('Home'=>'./', $_SECTION=>'.');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_newsletter.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title">
			<div class="search">
				Search by subscriber email:
				<?=$form_search->output_open()?>
				<?=$form_search->output('key')?>
				<?=$form_search->output('submit')?>
				<?=$form_search->output_close()?>
			</div>
			<?=$_PAGE?>
		</div>
		<?=html_message::show()?>
		<?=$pager->show()?>
		<?=$form->output_open()?>
		<? if ($view_mode != 'inactive') { ?>
			<div class="band"><?=$form->output('submit_add')?> <?=$len > 0 ? $form->output('submit_delete') : ''?></div>
		<? } else { ?>
			<div class="band"><?=$form->output('submit_active')?></div>
		<? } ?>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
			<tr class="table_heading">
				<th width="10px"><?=$form->output('check_all')?></th>
				<?=($view_mode != "nonmember")?$pager->column('fullname'):''?>
				<?=$pager->column('email')?>
				<th>Action</th>
			</tr>
			<?php if ($len == 0 ) { ?>
			<tr class="table_row">
				<td colspan=<?=($view_mode != "nonmember")?"4":"3"?> align="center">There are no subscribers</td>
			</tr>
			<?php } else {
				foreach ($subscribers as $subscriber) {
			?>
				<tr class="table_row">
					<td><?=$form->output('checked_id', $subscriber->subscriber_id)?></td>
					<? if ($view_mode != 'nonmember') { ?>
						<td><?=($subscriber->fullname == '')?'--':$subscriber->fullname?></td>
					<? } ?>
					<td><?=$subscriber->email?></td>
					<td align="center">
						<? if ($view_mode != 'inactive') { ?>
							<a href="newsletter_subscriber_delete.php?id=<?=$subscriber->subscriber_id?>" title="Delete subscriber"><img src="images/icon_delete.gif"/></a>
						<? } else { ?>
							<a href="action_newsletter_subscriber_activate.php?id=<?=$subscriber->subscriber_id?>">Change to subscribed</a>
						<? } ?>
					</td>
				</tr>
			<?php }
				}
			?>
		</table>
		<? if ($view_mode != 'inactive') { ?>
			<div class="band"><?=$form->output('submit_add')?> <?=$len > 0 ? $form->output('submit_delete') : ''?></div>
		<? } else { ?>
			<div class="band"><?=$form->output('submit_active')?></div>
		<? } ?>
		<?=$form->output_close()?>
		<?=$pager->show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>