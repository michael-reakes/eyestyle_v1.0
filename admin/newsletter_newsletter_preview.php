<?php
$_ACCESS = 'newsletter.newsletter';
$_SECTION = 'Newsletter Management';
$_PAGE = 'Send Newsletter';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$newsletter = new dbo('newsletter', $_GET['id']);

$subscribers = new dbo_list('subscriber');


$form = new html_form('form_newsletter_newsletter_preview', 'newsletter_newsletter_send.php?'.http::build_query($_GET));
$form->add(new html_form_radio('mode', 'test', '', true, "javascript:toggleEnabled(this.checked, 'test_to');toggleEnabled(!this.checked, 'check_all_true');"));
$form->add(new html_form_radio('mode', 'live', '', false, "javascript:toggleEnabled(!this.checked, 'test_to');toggleEnabled(this.checked, 'check_all_true');"));
$form->add(new html_form_text('test_to', false,'','',false,60));
$form->add(new html_form_checkbox('customer', 'true', 'checkbox'));
$form->add(new html_form_checkbox('non_customer', 'true', 'checkbox'));
$form->add(new html_form_button('submit', 'Send Now', '', 'submit', true));
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
		<?=html_message::show()?>
		<?=$form->output_open()?>
		<div class="page_subtitle">Newsletter Detail</div>
		<div class="info">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="attribute_label" width="100px">From:</td>
					<td class="attribute_value"><?=$newsletter->from_name?> &lt;<?=$newsletter->from_address?>&gt;</td>
				</tr>
				<tr>
					<td class="attribute_label">Subject:</td>
					<td class="attribute_value"><?=$newsletter->subject?></td>
				</tr>
				<tr>
					<td class="attribute_label" width="100px">To:</td>
					<td class="attribute_value">
						<?=$form->output('mode', 'test')?> <b>Test Mode</b>
						<div class="info">
							<p><em>Enter one or multiple email addresses to test the newsletter (Seperate by semi-collon)</em></p>
							<?=$form->output('test_to', NULL, $form->checked('mode', 'test')?'':'disabled')?>
						</div>
						<?=$form->output('mode', 'live')?> <b>Live Mode</b>
						<div class="info">
							<p><em>Please select the groups that you would like to send to:</em></p>
							<p><?=$form->output('customer')?>Customer subscribers.  
								<? if ($active_len-$non_member_len > 0) { ?> 
									There <?=($active_len-$non_member_len==1)?"is":"are"?> <?=$active_len-$non_member_len?> member/s.
								<? } ?>
							</p>
							<p><?=$form->output('non_customer')?>Non-customer subscribers. 
								<? if ($non_member_len > 0) { ?> 
									There <?=($non_member_len==1)?"is":"are"?> <?=$non_member_len?> member/s.
								<? } ?>
							</p>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<div class="page_subtitle">Preview</div>
		<div class="info">
			<iframe src="newsletter_newsletter_preview_frame.php?<?=http::build_query($_GET)?>" width="80%" height="600px"></iframe>
		</div>

		<hr />
		<div class="padded_row"><?=$form->output('cancel')?> <?=$form->output('submit')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>
