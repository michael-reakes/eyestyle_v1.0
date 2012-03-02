<?php
$_ACCESS = 'all';
$_SECTION = 'Home';
$_PAGE = 'Change Password';

require_once('inc.php');

http::register_path();

$form = new html_form('form_change_password', 'action_change_password.php');
$form->add(new html_form_password('password', true,'','full'));
$form->add(new html_form_password('new_password', true,'','full'));
$form->add(new html_form_password('new_password_confirm', true,'','full'));
$form->add(new html_form_button('submit', 'Change Password'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_home.php')?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$form->output_open()?>
		<div class="info">Security notice: please ensure your password has at least 6 characters and consists of both numbers and letters.</div>
		<div class="info">
			<table border="0" cellpadding="0" cellspacing="0" width="400px">
				<tr>
					<td class="attribute_label" width="180px">Current Password:</td>
					<td class="attribute_value" width="220px"><?=$form->output('password')?></td>
				</tr>
				<tr>
					<td class="attribute_label">New Password:</td>
					<td class="attribute_value"><?=$form->output('new_password')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Confirm New Password:</td>
					<td class="attribute_value"><?=$form->output('new_password_confirm')?></td>
				</tr>
			</table>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?>&nbsp;<?=$form->output('submit')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>