<?php
$_ACCESS = 'storefront';

require_once('inc.php');

$form = html_form::get_form('form_store_front');
if (!$form->validate()) $form->set_failure();

for ($i=1; $i<=6; $i++) {
	$key = 'featurebanner_image_'.$i;
	if (isset($_FILES[$key])) {
		$pref = new dbo('preference', $key);

		$delete = isset($_POST[$key.'_delete']);
		$uploaded = is_uploaded_file($_FILES[$key]['tmp_name']);
		if ( ($delete || $uploaded)
				&& is_file('../'.$pref->value) ) {
			unlink('../'.$pref->value);
		}

		if ($delete) {
			$pref->value = '';
			$pref->update();
		} else if ($uploaded) {
			$fileinfo = pathinfo($_FILES[$key]['name']);
			$dirName = 'images/storefront/';
			$basename = $dirName.$fileinfo['basename'];
			$i = 1;
			while(is_file('../'.$basename)) {
				$basename = $dirName.$fileinfo['filename'].$i.'.'.$fileinfo['extension'];
				$i++;
			}
			move_uploaded_file($_FILES[$key]["tmp_name"], '../'.$basename);

			$pref->value = $basename;
			$pref->update();
		}
	}
	$pref = new dbo('preference');
	$pref->name = 'featurebanner_url_'.$i;
	$pref->value = $form->get('featurebanner_url_'.$i);
	$pref->update();
}
html_message::add('Store front\'s feature products updated successfully.', 'info');
http::redirect('store_front.php');