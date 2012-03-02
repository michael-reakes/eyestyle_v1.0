<?php
$_ACCESS = 'product.category';

require_once('inc.php');

$form = html_form::get_form('form_product_category_add');

if (!$form->validate()) {
	$form->set_failure();
}

$category_list = new dbo_list('category');
$max_sort = $category_list->max('sort_order');

$category = new dbo('category');
$category->parent_id = 0;
$category->name = $form->get('name');
$category->alias = webpulse::create_alias($category->name, 'category');
$category->sort_order = $max_sort + 10;

$images = array('image_men', 'image_women');
foreach($images as $key) {
	if (isset($_FILES[$key])) {
		$delete = isset($_POST[$key.'_delete']);
		$uploaded = is_uploaded_file($_FILES[$key]['tmp_name']);
		if ( ($delete || $uploaded)
				&& is_file('../'.$category->$key) ) {
			unlink('../'.$category->$key);
		}

		if ($delete) {
			$category->$key= '';
		} else if ($uploaded) {
			$fileinfo = pathinfo($_FILES[$key]['name']);
			$dirName = 'images/category/';
			$basename = $dirName.$fileinfo['basename'];
			$i = 1;
			while(is_file('../'.$basename)) {
				$basename = $dirName.$fileinfo['filename'].$i.'.'.$fileinfo['extension'];
				$i++;
			}
			move_uploaded_file($_FILES[$key]["tmp_name"], '../'.$basename);

			$category->$key = $basename;
		}
	}
}

if ($category->insert()) {
	html_message::add('Category created successfully.', 'info');
} else {
	$form->set_failure('Sorry the category could not be added. The server is either down or busy at the moment. Please try again later.');
}

http::redirect('product_categories.php');