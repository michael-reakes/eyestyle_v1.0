<?php
$_ACCESS = 'order';
$_SECTION = 'Order Management';
$_PAGE = 'Notification Setting';

require_once('inc.php');

http::register_path();

$breadcrumbs = array('Home'=>'./', 'Order Management'=>'order_order.php', $_PAGE=>'');

$to = new dbo('preference', 'email_notification_to');

$form = new html_form('form_order_notification', 'action_order_notification.php');

$form->add(new html_form_text('value', true, $to->value, '', false, 40));
$form->add(new html_form_button('submit', 'Save', '', 'submit', true));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_order.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$form->output_open()?>
		<div class="info">
			<p>Please enter the email address where Order Notification will go to:</p>
			<?=$form->output('value')?>
		</div>
		<hr/>
		<div class="padded_row"><?=$form->output('cancel')?> <?=$form->output('submit')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>