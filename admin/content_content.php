<?php
$_ACCESS = 'content';
$_SECTION = 'Content';
$_PAGE = 'Edit Content';

require_once('inc.php');

http::register_path();

http::halt_if(!isset($_GET['id']));

$content = new dbo('page',$_GET['id']);

$title = $content->title;

$form = new html_form('form_content_content', 'action_content_content.php?id='.$content->page_id);
$form->add(new html_form_textarea('html',true,$content->content,'full',40,20));
$form->add(new html_form_button('submit', 'Save'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'content_content.php?id=1', $title=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_content.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$title?></div>
		<?=html_message::show()?>
		<?=$form->output_open()?>
		<div class="info">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="attribute_value"><?=$form->output('html')?></td>
				</tr>
			</table>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?>&nbsp;<?=$form->output('submit')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>
<script type="text/javascript" src="../include/fckeditor/fckeditor.js"></script>
<script type="text/javascript">
	var oFCKeditor = new FCKeditor('html') ;
	oFCKeditor.BasePath	= '../include/fckeditor/' ;
	oFCKeditor.Height = 400;
	oFCKeditor.ReplaceTextarea() ;
</script>
