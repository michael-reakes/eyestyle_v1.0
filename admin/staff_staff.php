<?php
$_ACCESS = 'staff.account';
$_SECTION = 'Staff Management';
$_PAGE = 'Staff Accounts';

require_once('inc.php');

http::register_path();

$staff_list = new dbo_list('staff', '', '', 'ASC', true);
$len = $staff_list->count();
$breadcrumbs = array('Home'=>'./', $_SECTION=>'staff_staff.php', $_PAGE=>'');

$pager = new html_pager($staff_list, array('fullname'=>'Full Name', 'staff_id'=>'Username', 'group_id.name'=>'User Group', 'last_login'=>'Last Login'));

$form = new html_form('form_staff_staff', 'action_staff_staff.php');
$form->add(new html_form_checkbox('check_all', '', 'checkbox', false, "javascript:checkAll('".$form->name."','check_all[]','checked_id[]');"));
foreach ($pager->get_page() as $staff) {
	$form->add(new html_form_checkbox('checked_id', $staff->staff_id, 'checkbox', false, "javascript:checkAllTicked('".$form->name."','checked_id[]','check_all[]');"));
}
$form->add(new html_form_button('submit_add', 'Add Staff', '', 'submit', true));
$form->add(new html_form_button('submit_set_group', 'Set User Group', '', 'submit', true));
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
		<div class="band"><?=$form->output('submit_add')?> <?=$len > 0 ? $form->output('submit_set_group').' '.$form->output('submit_delete') : ''?></div>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
			<tr class="table_heading">
				<th width="10px"><?=$form->output('check_all')?></th>
				<?=$pager->column('fullname')?>
				<?=$pager->column('staff_id')?>
				<?=$pager->column('group_id.name')?>
				<?=$pager->column('last_login')?>
				<th>Action</th>
			</tr>
			<?php if ($len == 0) { ?>
			<tr class="table_row">
				<td colspan="6" class="align_center">There are no staff.</td>
			</tr>
			<?php } else {
					foreach ($pager->get_page() as $staff) {
						$staff_group = $staff->link('group_id');
			?>
				<tr class="table_row">
					<td><?=$staff->staff_id != 'admin' ? $form->output('checked_id', $staff->staff_id) : '&nbsp;'?></td>
					<td><a href="staff_staff_edit.php?id=<?=$staff->staff_id?>" title="Edit Staff Account"><?=$staff->fullname?></a></td>
					<td align="center"><a href="staff_staff_edit.php?id=<?=$staff->staff_id?>" title="Edit Staff Account"><?=$staff->staff_id?></a></td>
					<td align="center"><a href="staff_staff_edit.php?id=<?=$staff->staff_id?>" title="Edit Staff Account"><?=$staff_group->name?></a></td>
					<td align="center"><a href="staff_staff_edit.php?id=<?=$staff->staff_id?>" title="Edit Staff Account"><?=utils_time::datetime($staff->last_login)?></a></td>
					<td align="center">
						<a href="staff_staff_edit.php?id=<?=$staff->staff_id?>" title="Edit Staff Account"><img src="images/icon_edit.gif"/></a>
						<?php if ($staff->staff_id != 'admin') {?>
							<a href="staff_staff_delete.php?id=<?=$staff->staff_id?>" title="Delete Staff Account"><img src="images/icon_delete.gif"/></a>
						<?php } ?>
					</td>
				</tr>
			<?php	}
				}
			?>
		</table>
		<div class="band"><?=$form->output('submit_add')?> <?=$len > 0 ? $form->output('submit_set_group').' '.$form->output('submit_delete') : ''?></div>
		<?=$form->output_close()?>
		<?=$pager->show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>