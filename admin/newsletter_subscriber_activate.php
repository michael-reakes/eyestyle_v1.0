<?php
$_ACCESS = 'newsletter.subscriber';
$_SECTION = 'Newsletter Management';
$_PAGE = 'Subscribe subscriber(s)';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

$member_array = array();
foreach ($ids as $id) {
	$member_array[] = new dbo('subscriber', $id);
}

$form = new html_form('form_newsletter_subscriber_activate', 'action_newsletter_subscriber_activate.php?'.http::build_query($_GET));

$form->add(new html_form_button('delete', 'Subscribe'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'newsletter_subscriber.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_newsletter.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=$form->output_open()?>
		<div class="info">
			<h4>Are you sure?</h4>
			<p>Are you sure you want to subscribe the following unsubscribed member(s)?</p>
			<ul>
				<?php foreach ($member_array as $member) {?>
				<li><?=$member->email?></li>
				<?php } ?>
			</ul>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?>&nbsp;<?=$form->output('delete')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>