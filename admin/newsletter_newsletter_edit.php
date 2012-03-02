<?php
$_ACCESS = 'newsletter.newsletter';
$_SECTION = 'Newsletter Management';
$_PAGE = 'Edit Newsletter';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$newsletter = new dbo('newsletter', $_GET['id']);

$form = new html_form('form_newsletter_newsletter_edit', 'action_newsletter_newsletter_edit.php?'.http::build_query($_GET));
$form->add(new html_form_text('name', true,$newsletter->name,'',false,60));
$form->add(new html_form_text('from_address', true,$newsletter->from_address,'',false,40));
$form->add(new html_form_text('from_name', false,$newsletter->from_name,'',false,40));
$form->add(new html_form_text('subject', true,$newsletter->subject,'full'));
$form->add(new html_form_textarea('body', true,$newsletter->body,'full',40,20));
$form->add(new html_form_button('submit_save', 'Save', '', 'submit', true));
$form->add(new html_form_button('submit_send', 'Save, Preview & Send', '', 'submit', true));
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
		<div class="info">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="attribute_label" width="170px">Newsletter Name: <?=$form->output_required('name')?></td>
					<td class="attribute_value">
						<?=$form->output('name')?><br/>
						<em>Internal reference, not shown in emails</em>
					</td>
				</tr>
				<tr>
					<td class="attribute_label" width="170px">Sender Email Address: <?=$form->output_required('from_address')?></td>
					<td class="attribute_value"><?=$form->output('from_address')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Sender Name: <?=$form->output_required('from_name')?></td>
					<td class="attribute_value"><?=$form->output('from_name')?> <em>Optional</em></td>
				</tr>
				<tr>
					<td class="attribute_label">Subject: <?=$form->output_required('subject')?></td>
					<td class="attribute_value"><?=$form->output('subject')?></td>
				</tr>
				<tr>
					<td class="attribute_label" width="170px">
						Email Body: <?=$form->output_required('body')?><br/>
						<br /><br />
						<div class="help" style="border:solid 1px #ccc;padding:5px;font-size:0.8em">
							<p>Available Tags:</p>
							<ul>
							<li>
								<b>{CUSTOMER}</b><br />
								<p>
									Example:<br />
									Email body in right box:<br/>
									&nbsp;&nbsp;<em>Hi {CUSTOMER},</em><br />
									Email received by John Citizen reads:<br/>
									&nbsp;&nbsp;<em>Hi John Citizen,</em>
								</p>
							</li>
							<li>
								<b>{LINK}</b><br />
								<p>
									Example: <br />
									Email body in right box: <br />
									<em>Please {LINK} to view the newsletter</em><br />
									Email received by subscribers reads:<br/>
									<em>Please <a href="http://www.eyestyle.com.au/newsletter/">click here</a> to view newsletter</em>
								</p>
							</li>
							</ul>
						</div>
					</td>
					<td class="attribute_value"><?=$form->output('body')?></td>
				</tr>
			</table>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?> <?=$form->output('submit_save')?> <?=$form->output('submit_send')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>
<script type="text/javascript" src="../include/fckeditor/fckeditor.js"></script>
<script type="text/javascript">
	var oFCKeditor = new FCKeditor('body') ;
	oFCKeditor.BasePath	= '../include/fckeditor/' ;
	var Config = new Array()
	Config['AbsolutePath'] = true;
	oFCKeditor.Height = 600;
	oFCKeditor.Config = Config;
	oFCKeditor.ReplaceTextarea() ;
</script>
