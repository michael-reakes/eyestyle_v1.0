<?php
$_ACCESS = 'staff.group';
$_SECTION = 'Staff Management';
$_PAGE = 'User Groups';

require_once('inc.php');

http::register_path();

$group_list = new dbo_list('staff_group');
$len = $group_list->count();
$breadcrumbs = array('Home'=>'./', $_SECTION=>'staff_staff.php', $_PAGE=>'');

$pager = new html_pager($group_list, array('name'=>'Group Name'));

$form = new html_form('form_staff_groups', 'action_staff_groups.php');
$form->add(new html_form_checkbox('check_all', '', 'checkbox', false, "javascript:checkAll('".$form->name."','check_all[]','checked_id[]');"));
foreach ($pager->get_page() as $group) {
	$form->add(new html_form_checkbox('checked_id', $group->staff_group_id, 'checkbox', false, "javascript:checkAllTicked('".$form->name."','checked_id[]','check_all[]');"));
}
$form->add(new html_form_button('submit_add', 'Add Group', '', 'submit', true));
$form->add(new html_form_button('submit_delete', 'Delete', '', 'submit', true));
$form->register();

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_staff.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$pager->show()?>
		<?=$form->output_open()?>
		<div class="band"><?=$form->output('submit_add')?> <?=$len > 0 ? $form->output('submit_delete') : ''?></div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
			<tr class="table_heading">
				<th width="10px"><?=$form->output('check_all')?></th>
				<?=$pager->column('name')?>
				<th>Action</th>
			</tr>
			<?php if ($len == 0) { ?>
			<tr class="table_row">
				<td colspan="3" class="align_center">There are no staff groups.</td>
			</tr>
			<?php } else {
					foreach ($pager->get_page() as $group) {
			?>
				<tr class="table_row">
					<td><?=$group->staff_group_id != 1 ? $form->output('checked_id', $group->staff_group_id) : '&nbsp;'?></td>
					<td><a href="staff_group_edit.php?id=<?=$group->staff_group_id?>" title="Edit User Group"><?=$group->name?></a></td>
					<td align="center">
						<a href="staff_group_edit.php?id=<?=$group->staff_group_id?>" title="Edit User Group"><img src="images/icon_edit.gif"/></a>
						<?php if ($group->staff_group_id != 1) { ?>
							<a href="staff_group_delete.php?id=<?=$group->staff_group_id?>" title="Delete User Group"><img src="images/icon_delete.gif"/></a>
						<?php } ?>
					</td>
				</tr>
			<?php	}
				}
			?>
		</table>
		<div class="band"><?=$form->output('submit_add')?> <?=$len > 0 ? $form->output('submit_delete') : ''?></div>
		<?=$form->output_close()?>
		<?=$pager->show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>