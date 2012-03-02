<?php
$_ACCESS = 'staff.group';
$_SECTION = 'Staff Management';
$_PAGE = 'Add User Group';

require_once('inc.php');

$form = new html_form('form_staff_group_add', 'action_staff_group_add.php');

$form->add(new html_form_text('name', true,'','full'));
foreach ($_CONFIG['access_code'] as $access=>$desc) {
	$form->add(new html_form_checkbox('access', $access, 'checkbox', false, "javascript:checkRight('".$access."')"));
}
$form->add(new html_form_button('submit', 'Create User Group'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'staff_staff.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_staff.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$form->output_open()?>
		<div class="info">
			<table border="0" cellpadding="0" cellspacing="0" width="450px">
				<tr>
					<td class="attribute_label" width="120px">Group Name:</td>
					<td class="attribute_value"><?=$form->output('name')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Access Rights:</td>
					<td class="attribute_value">
						<div class="access">
						<?php
						foreach ($_CONFIG['access_code'] as $access=>$desc) {
							$level = count(explode('.', $access));
						?>
							<div class="level<?=$level?>"><?=$form->output('access', $access)?> <?=$desc?></div>
						<?php } ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?>&nbsp;<?=$form->output('submit')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<script type="text/javascript" language="javascript">
<!--
	function checkRight(right) {
		var access = document.forms["form_staff_group_add"]["access[]"];
		var checked = document.getElementById("access_"+right).checked;
		for (var i=0; i < access.length; i++) {
			if (access[i].value.indexOf(right) == 0) {
				access[i].checked = checked;
			}
		}

		while (right.lastIndexOf('.') != -1) {
			right = right.substring(0, right.lastIndexOf('.'));
			all_checked = true;
			for (var i=0; i < access.length; i++) {
				if (access[i].value != right && access[i].value.indexOf(right) == 0) {
					if (!access[i].checked) {
						all_checked = false;
					}
				}
			}
			document.getElementById("access_"+right).checked = all_checked;
		}
	}
-->
</script>
<?php
require_once('inc_footer.php');
?>