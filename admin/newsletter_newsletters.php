<?php
$_ACCESS = 'newsletter.newsletter';
$_SECTION = 'Newsletter Management';
$_PAGE = 'Newsletters';

require_once('inc.php');

http::register_path();

$newsletter_list = new dbo_list('newsletter');

$len = $newsletter_list->count();
$pager = new html_pager($newsletter_list, array('date_created'=>'Date Created', 'name'=>'Newsletter Name', 'subject'=>'Subject', 'date_last_sent'=>'Last Sent'),'d');

$form = new html_form('form_newsletter_newsletters', 'action_newsletter_newsletters.php');
$form->add(new html_form_checkbox('check_all', '', 'checkbox', false, "javascript:checkAll('".$form->name."','check_all[]','checked_id[]');"));
foreach ($pager->get_page() as $newsletter) {
	$form->add(new html_form_checkbox('checked_id', $newsletter->newsletter_id, 'checkbox', false, "javascript:checkAllTicked('".$form->name."','checked_id[]','check_all[]');"));
}
$form->add(new html_form_button('submit_add', 'Add Newsletter', '', 'submit', true));
$form->add(new html_form_button('submit_duplicate', 'Duplicate', '', 'submit', true));
$form->add(new html_form_button('submit_delete', 'Delete', '', 'submit', true));
$form->register();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'newsletter_subscriber.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_newsletter.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$pager->show()?>
		<?=$form->output_open()?>
		<div class="band"><?=$form->output('submit_add')?> <?=$form->output('submit_duplicate')?> <?=$form->output('submit_delete')?></div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
			<tr class="table_heading">
				<th width="10px"><?=$form->output('check_all')?></th>
				<?=$pager->column('date_created')?>
				<?=$pager->column('name')?>
				<?=$pager->column('subject')?>
				<?=$pager->column('date_last_sent')?>
				<th>Send</th>
				<th>Action</th>
			</tr>
			<?php if ($len == 0 ) { ?>
			<tr class="table_row">
				<td colspan="7" align="center">There are no newsletters</td>
			</tr>
			<?php } else {
				foreach ($pager->get_page() as $newsletter) {
			?>
				<tr class="table_row">
					<td><?=$form->output('checked_id', $newsletter->newsletter_id)?></td>
					<td align="center"><a href="newsletter_newsletter_edit.php?id=<?=$newsletter->newsletter_id?>" title="Edit Newsletter"><?=utils_time::date($newsletter->date_created)?></a></td>
					<td><a href="newsletter_newsletter_edit.php?id=<?=$newsletter->newsletter_id?>" title="Edit Newsletter"><?=$newsletter->name?></a></td>
					<td><a href="newsletter_newsletter_edit.php?id=<?=$newsletter->newsletter_id?>" title="Edit Newsletter"><?=$newsletter->subject?></a></td>
					<td align="center"><a href="newsletter_newsletter_edit.php?id=<?=$newsletter->newsletter_id?>" title="Edit Newsletter"><?=utils_time::date($newsletter->date_last_sent)?></a></td>
					<td align="center"><a href="newsletter_newsletter_preview.php?id=<?=$newsletter->newsletter_id?>" title="Send Newsletter"><b>Send Now</b></a></td>
					<td align="center">
						<a href="newsletter_newsletter_edit.php?id=<?=$newsletter->newsletter_id?>" title="Edit Newsletter"><img src="images/icon_edit.gif"/></a>
						<a href="newsletter_newsletter_delete.php?id=<?=$newsletter->newsletter_id?>" title="Delete newsletter"><img src="images/icon_delete.gif"/></a>
					</td>
				</tr>
			<?php }
				}
			?>
		</table>
		<div class="band"><?=$form->output('submit_add')?> <?=$form->output('submit_duplicate')?> <?=$form->output('submit_delete')?></div>
		<?=$form->output_close()?>
		<?=$pager->show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>