<?php
$_ACCESS = 'public';
$_SECTION = 'Home';
$_PAGE = 'Login';

require_once('inc.php');

$form = new html_form('form_login', 'action_login.php');
$form->add(new html_form_text('username', true));
$form->add(new html_form_password('password', true));
$form->add(new html_form_button('submit', 'Log In', 'submit'));
$form->register();

require_once('inc_header.php');
?>
<tr>
	<td id="login" colspan="2">
		<?=$form->output_open()?>
		<table align="center" cellpadding="0" cellspacing="10" border="0">
			<tr>
				<td colspan="2"><?=html_message::show()?></td>
			</tr>
			<tr>
				<td width="100px" align="right">Username:</td>
				<td width="220px" align="left"><?=$form->output('username')?></td>
			</tr>
			<tr>
				<td width="100px" align="right">Password:</td>
				<td width="220px" align="left"><?=$form->output('password')?></td>
			</tr>
			<tr>
				<td width="100px" ></td>
				<td width="220px" align="left"><?=$form->output('submit')?></td>
			</tr>
		</table>
		<?=$form->output_close()?>
	</td>
</tr>
<?php
require_once('inc_footer.php');
?>