<?php
$_CONFIG['site']['application'] = 'frontend';	// unique application ID, required (frontend, admin, etc)

$_CONFIG['http']['error_page']	= 'error.php';	// relative to current executing script

$_CONFIG['html']['breadcrumb_id']				= 'breadcrumb';
$_CONFIG['html']['breadcrumb_selected_class']	= 'selected';
$_CONFIG['html']['breadcrumb_seperator']		= '&gt;';

$_CONFIG['html_form']['error_class']		= 'form_error';
$_CONFIG['html_form']['required_error_msg']	= ' * Required';
$_CONFIG['html_form']['required_class']		= 'required';

$_CONFIG['html_message']['template']	= 'templates/html_message.tpl';

$_CONFIG['html_pager']['max_number']				= 12;	// odd number only
$_CONFIG['html_pager']['template']					= 'templates/html_pager.tpl';
$_CONFIG['html_pager']['column_selected_class']		= 'selected';
$_CONFIG['html_pager']['column_arrow_asc']			= 'images/icon/arrow_left.gif';
$_CONFIG['html_pager']['column_arrow_desc']			= 'images/icon/arrow_right.gif';