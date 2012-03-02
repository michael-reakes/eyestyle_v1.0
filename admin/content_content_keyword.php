<?php
$_ACCESS = 'content';
$_SECTION = 'Content';
$_PAGE = 'Meta Keywords';

require_once('inc.php');

http::register_path();

http::halt_if(!isset($_GET['id']));

$id = $_GET['id'];

if ($id != 'splash' && $id != 'men' && $id != 'women') {
	http::halt();
}

if ($id == 'splash') {
	$page_name = 'Splash Page';
}elseif ($id == 'men') {
	$page_name = 'Men Index';
}elseif ($id == 'women') {
	$page_name = 'Women Index';
}

$keywords_dbo = new dbo('preference','meta_keywords_'.$id);
$description_dbo = new dbo('preference','meta_description_'.$id);

$form = new html_form('form_keywords', 'action_content_content_keyword.php?'.http::build_query($_GET));
$form->add(new html_form_textarea('description',true,$description_dbo->value,'',80,3,false));
$form->add(new html_form_textarea('keywords',true,$keywords_dbo->value,'',80,4,false));
$form->add(new html_form_button('submit','submit'));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Store Front Management'=>'store_front.php', $_PAGE=>'');


require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_content.php')?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title">Keywords Management for <?=$page_name?></div>
		<?=html_message::show()?>
		<div class="info">
			<?=$form->output_open()?>
			
			<table cellspacing="0" cellpadding="0" border="0" class="table_product">
			<tr class="table_heading">
				<th colspan=3>Meta Description</th>
			</tr>
			<tr class="table_row"><td><?=$form->output('description')?></td></tr>
			</table>
			<br />
			<div style="float:left">
				<table cellspacing="0" cellpadding="0" border="0" class="table_product">
					<tr class="table_heading">
						<th colspan=3>Meta Keywords</th>
					</tr>
					<tr class="table_row"><td><?=$form->output('keywords')?></tr>
				</table>
			</div>

			<div style="float:left;margin-left:20px;border: solid 1px #666; padding: 10px;width:100px">
				<em>Use comma seperated words for example:
				eyestyle, eye style australia, premium sunglasses</em>
			</div>
			<div style="clear:both"></div>
		</div>
		<hr />
		<div class="info"><?=$form->output('submit')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>

