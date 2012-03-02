<?php
$_ACCESS = 'staff.group';
$_SECTION = 'Staff Management';
$_PAGE = 'Delete User Group(s)';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$ids = is_array($_GET['id']) ? $_GET['id'] : array($_GET['id']);

$groups = array();
foreach ($ids as $id) {
	$groups[] = new dbo('staff_group', $id);
}

$form = new html_form('form_staff_group_delete', 'action_staff_group_delete.php?'.http::build_query($_GET));

$form->add(new html_form_button('delete', 'Delete'));
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
		<div class="info">
			<h4>Are you sure?</h4>
			<p>Are you sure you want to delete the following user group(s)?</p>
			<ul>
				<?php foreach ($groups as $group) {?>
				<li><?=$group->name?></li>
				<?php } ?>
			</ul>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?>&nbsp;<?=$form->output('delete')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>