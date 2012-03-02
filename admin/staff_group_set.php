<?php
$_ACCESS = 'staff.account';
$_SECTION = 'Staff Management';
$_PAGE = 'Set User Group';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

$staff_array = array();
foreach ($ids as $id) {
	$staff_array[] = new dbo('staff', $id);
}

$group_list = new dbo_list('staff_group');
$group_options = array();
foreach ($group_list->get_all() as $group) {
	$group_options[$group->staff_group_id] = $group->name;
}

$form = new html_form('form_staff_group_set', 'action_staff_group_set.php?'.http::build_query($_GET));
$form->add(new html_form_select('group_id', $group_options, '', true));
$form->add(new html_form_button('submit', 'Set User Group'));
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
		<?=$form->output_open()?>
		<div class="page_subtitle">Please Select a User Group</div>
		<div class="info"><?=$form->output('group_id')?></div>
		<div class="page_subtitle">The following users will be set to the selected User Group:</div>
		<div class="info">
			<ul>
				<?php foreach ($staff_array as $staff) {?>
				<li><?=$staff->staff_id?> (<?=$staff->fullname?>)</li>
				<?php } ?>
			</ul>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?>&nbsp;<?=$form->output('submit')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>