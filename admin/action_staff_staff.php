<?php
$_ACCESS = 'staff.account';

require_once('inc.php');

$form = html_form::get_form('form_staff_staff');

if ($form->clicked('submit_add')) {
	http::redirect('staff_staff_add.php');
} elseif ($form->clicked('submit_delete')) {
	$ids = $form->get('checked_id');

	if (empty($ids)) {
		$form->set_failure('Please select at least one staff.');
	}

	$url = 'staff_staff_delete.php?'.http::build_query(array('id'=>$ids));
	http::redirect($url);

} elseif ($form->clicked('submit_set_group')) {
	$ids = $form->get('checked_id');

	if (empty($ids)) {
		$form->set_failure('Please select at least one staff.');
	}

	$url = 'staff_group_set.php?'.http::build_query(array('id'=>$ids));
	http::redirect($url);
}
?>