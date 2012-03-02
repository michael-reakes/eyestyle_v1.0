<?php
$_ACCESS = 'newsletter.subscriber';
$_SECTION = 'Newsletter Management';
$_PAGE = 'Edit Subscriber';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$subscriber = new dbo('subscriber', $_GET['id']);

$form = new html_form('form_newsletter_subscriber_edit', 'action_newsletter_subscriber_edit.php?'.http::build_query($_GET));
$form->add(new html_form_text('email', true, $subscriber->email, 'full'));
$form->add(new html_form_button('submit', 'Save', '', 'submit', true));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$fields = array('email'=>'Email');

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
			<table border="0" cellpadding="0" cellspacing="0" width="580px">
			<?php foreach($fields as $field=>$label) { ?>
				<tr>
					<td class="attribute_label" width="160px"><?=$label?>: <?=$form->output_required($field)?></td>
					<td class="attribute_value" width="420px"><?=$form->output($field)?></td>
				</tr>
			<?php } ?>
			</table>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?> <?=$form->output('submit')?></div>
		<br />
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>