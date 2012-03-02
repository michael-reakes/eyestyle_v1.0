<?
$_REQUIRE_SSL = true;

require_once('inc.php');

$_PAGE = 'login';
http::register_path();


if ($_SESSION['gender'] == 'men') $cid = 1;
else $cid = 2;

$product_list = new dbo_list('product','WHERE `status` = "active" AND category_id_1 = '.$cid.' OR  category_id_2 = '.$cid,'product_id','DESC');

if ( (isset($_CUSTOMER->email)) && (isset($_CUSTOMER->password)) ){
	http::redirect('myaccount.php');
}

$form_login = new html_form('form_login','action_login.php');
$form_login->add(new html_form_hidden('state','login'));
$form_login->add(new html_form_text('email',true,'','login_input',false,60,200));
$form_login->add(new html_form_password('password',true,'','login_input',false,60,200));
$form_login->add(new html_form_image_button('btn_login','images/icon/arrow.gif','Login','no_border'));
$form_login->register();

$breadcrumb = array('home'=>'./',$_PAGE=>'');

require_once('inc_header.php')


?>
	
	
		<div id="content">
			<?=html::breadcrumb($breadcrumb)?>
			<fieldset>
			<?=html_message::show()?>
			<?=$form_login->output_open()?>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<label for="email">Email:</label>
						<?=$form_login->output('email')?>
					</td>
				</tr>
				<tr>
					<td>
						<label for="password">Password:</label> 
						<?=$form_login->output('password')?> &nbsp; 
						<a href="forgot_password.php">Forgot password?</a>
					</td>
				</tr>
			</table>
			<button type="submit" value="Login">Login</button>
			<?=$form_login->output_close()?>
			</fieldset>
			<br /><br />
			<p>First time here? <a href="signup.php">Sign up now</a>.</p>
		</div>
	
	
	
	
	
	
	
	
	<?php require_once('inc_footer.php'); ?>