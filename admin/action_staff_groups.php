<?php
$_ACCESS = 'staff.group';

require_once('inc.php');

$form = html_form::get_form('form_staff_groups');

if ($form->clicked('submit_add')) {
	http::redirect('staff_group_add.php');
} elseif ($form->clicked('submit_delete')) {
	$ids = $form->get('checked_id');

	if (empty($ids)) {
		$form->set_failure('Please select at least one user group.');
	}

	$url = 'staff_group_delete.php?'.http::build_query(array('id'=>$ids));

	http::redirect($url);
}
?>