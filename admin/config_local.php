<?php
$_CONFIG['site']['application']	= 'admin';	// unique application ID, required (frontend, admin, etc)
$_CONFIG['site']['name'] 		= 'Eyestyle Administration System';

$_CONFIG['access']['login_page']	= 'login.php';

$_CONFIG['access_code']['upload']			= 'Upload files';
$_CONFIG['access_code']['staff']			= 'All staff management';
$_CONFIG['access_code']['staff.account']	= 'Manage staff accounts';
$_CONFIG['access_code']['staff.group']		= 'Manage user groups';
$_CONFIG['access_code']['storefront']		= 'Manage store front';
$_CONFIG['access_code']['content']			= 'Manage website content';
$_CONFIG['access_code']['product']			= 'Manage product';
$_CONFIG['access_code']['order']			= 'Manage order';
$_CONFIG['access_code']['delivery']			= 'Manage delivery';
$_CONFIG['access_code']['customer']			= 'Manage customer';
$_CONFIG['access_code']['newsletter']		= 'Manage newsletter';

$_CONFIG['company']['name'] = "Eyestyle";

$_CONFIG['http']['error_page']	= 'error.php';	// relative to current executing script

$_CONFIG['html']['breadcrumb_id']				= 'breadcrumb';
$_CONFIG['html']['breadcrumb_selected_class']	= 'selected';
$_CONFIG['html']['breadcrumb_seperator']		= '&gt;';

$_CONFIG['html_form']['error_class']		= 'form_error';
$_CONFIG['html_form']['required_error_msg']	= ' * Required';
$_CONFIG['html_form']['required_class']		= 'required';

$_CONFIG['html_message']['template']	= 'templates/html_message.tpl';

$_CONFIG['html_pager']['max_number']				= 15;	// odd number only
$_CONFIG['html_pager']['template']					= 'templates/html_pager.tpl';
$_CONFIG['html_pager']['column_selected_class']		= 'column_selected';
$_CONFIG['html_pager']['column_arrow_asc']			= 'images/arrow_asc.gif';
$_CONFIG['html_pager']['column_arrow_desc']			= 'images/arrow_desc.gif';

$_CONFIG['fckeditor']['image_delete_depth'] = 8;

?>