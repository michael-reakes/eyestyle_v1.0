<?php
	require_once('inc.php');

	$_SECTION = 'Recommend Us';
	$_CATEGORY = '';
	$_PAGE = 'Recommend Us';


	$_KEYWORDS = "";

	http::register_path();

	if (isset($_GET['id'])) {
		$customer = new dbo('customer');
		if (!$customer->load($_GET['id'])) {
			unset($customer);
		}
	}

	$form = new html_form('form_recommend','action_recommend.php');
	$form->add(new html_form_text('name',true,'','full'));
	$form->add(new html_form_text('email',true,isset($customer) ? $customer->email : '','full'));
	$form->add(new html_form_text('email1',true,'','full'));
	$form->add(new html_form_text('email2',false,'','full'));
	$form->add(new html_form_text('email3',false,'','full'));
	$form->add(new html_form_textarea('message',true,'','full',30,5));
	$form->add(new html_form_image_button('btn_submit','images/btn/submit.gif','Submit','no_border'));
	$form->set_validator('email1',array('utils_validation','email'),'Please enter a valid email');
	$form->set_validator('email2',array('utils_validation','email'),'Please enter a valid email');
	$form->set_validator('email3',array('utils_validation','email'),'Please enter a valid email');
	$form->register();

	$breadcrumb = array('Home'=>'./',$_SECTION=>'');

	require_once('inc_header.php');
?>
	<?php require_once('inc_banner.php'); ?>
	<div id="content">
		<div id="breadcrumb"><a href="home.php">home</a> / <span class="selected"><?=$_PAGE?></span></div>
		<div class="page_title"><img src="images/layout/title/recommend.gif" alt="Recommend to a friend" /></div>
		<div class="panel_utility">
			<h6>Thank you for recommending EYESTYLE.</h6><br />
			<p class="emphasis">* denotes mandatory fields</p>
			<?=html_message::show()?>
			<?=$form->output_open()?>
			<table border="0" cellpadding="0" cellspacing="0" width="400px">
				<tr>
					<td><p>
						Your Name <span class="emphasis">*</span><br />
						<?=$form->output('name')?>
					</p></td>
				</tr>
				<tr>
					<td><p>
						Your Email <span class="emphasis">*</span><br />
						<?=$form->output('email')?>
					</p><br /></td>
				</tr>
				<tr>
					<td>
						<p>Please type in email addresses below to tell your friends about EYESTYLE. </p>
					</td>
				</tr>
				<?php
					for ($i=1; $i<=3; $i++) {
						$email = 'email'.$i;
				?>
				<tr>
					<td>
					<p>
						Recipient Email <?=$i?><?=$i == 1 ? ' <span class="emphasis">*</span>' : ''?><br />
						<?=$form->output($email)?>
					</p></td>
				</tr>
				<?php } ?>
				<tr>
					<td>
					<p>
						Please type in a personalised message of your own in the text field below.
					</p>
					
					<p>
						Personal Message <span class="emphasis">*</span><br />
						<?=$form->output('message')?>
					</p></td>
				</tr>
				<tr>
					<td><?=$form->output('btn_submit')?></td>
				</tr>
			</table>
			<?=$form->output_close()?>

		</div>
	</div>

<?php require_once('inc_footer.php'); ?>