<?php
$_ACCESS = 'staff.account';
$_SECTION = 'Staff Management';
$_PAGE = 'Edit Staff Account';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$staff = new dbo('staff', $_GET['id']);

$form = new html_form('form_staff_staff_edit', 'action_staff_staff_edit.php?'.http::build_query($_GET));

$group_list = new dbo_list('staff_group');
$group_options = array();
foreach ($group_list->get_all() as $group) {
	$group_options[$group->staff_group_id] = $group->name;
}

$form->add(new html_form_text('fullname', true, $staff->fullname,'full'));
$form->add(new html_form_password('new_password', false,'','full'));
$form->add(new html_form_password('new_password_confirm', false,'','full'));
$form->add(new html_form_select('group_id', $group_options, '', false, false, '', $staff->group_id, 'javascript:checkGroup()'));
foreach ($_CONFIG['access_code'] as $access=>$desc) {
	$rights = explode('|',$staff->access);
	$checked = false;
	foreach ($rights as $right) {
		if (strpos($access, $right) === 0) {
			$checked = true;
		}
	}
	$form->add(new html_form_checkbox('access', $access, 'checkbox', $checked, "javascript:checkRight('".$access."')"));
}
$form->add(new html_form_button('submit', 'Save'));
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
					<td class="attribute_label" width="150px">Username:</td>
					<td class="attribute_value"><?=$staff->staff_id?></td>
				</tr>
				<tr>
					<td class="attribute_label">Full Name:</td>
					<td class="attribute_value"><?=$form->output('fullname')?></td>
				</tr>
				<tr>
					<td class="attribute_label">New Password:</td>
					<td class="attribute_value"><?=$form->output('new_password')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Confirm New Password:</td>
					<td class="attribute_value"><?=$form->output('new_password_confirm')?></td>
				</tr>
				<tr>
					<td class="attribute_label">User Group:</td>
					<td class="attribute_value"><?=$form->output('group_id')?></td>
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
	function checkGroup() {
		var access = document.forms["form_staff_staff_edit"]["access[]"];
		var group_id = document.forms["form_staff_staff_edit"]["group_id"].value;
		var groups = new Object();
		<?php
		foreach ($group_list->get_all() as $group) {
			$rights = explode('|', $group->access);
			$quoted = array();
			foreach ($rights as $right) {
				$quoted[] = '"'.$right.'"';
			}
		?>
		groups[<?=$group->staff_group_id?>] = [<?=implode(',',$quoted)?>];
		<?php } ?>

		for (var i=0; i<access.length; i++) {
			access[i].checked = false;
		}

		for (var i=0; i<groups[group_id].length; i++) {
			document.getElementById("access_"+groups[group_id][i]).checked = true;
			checkRight(groups[group_id][i]);
		}
	}

	function checkRight(right) {
		var access = document.forms["form_staff_staff_edit"]["access[]"];
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