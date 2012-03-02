<?php
$_ACCESS = 'delivery';
$_SECTION = 'Delivery';
$_PAGE = 'Add Class';

require_once('inc.php');

$breadcrumbs = array('Home'=>'./', $_SECTION=>'delivery_matrix.php', 'Delivery Class'=>'delivery_classes.php', $_PAGE=>'');

$form = new html_form('form_system_delivery_class_add', 'action_delivery_class_add.php');
$form->add(new html_form_text('name', true));
$form->add(new html_form_text('description', true, '', '', false, 80));
$form->add(new html_form_text('weight', true));
$form->add(new html_form_button('submit', 'Add Class', '', 'submit', true));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_delivery.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$form->output_open()?>
		<div class="info">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="attribute_label">Class Name:</td>
					<td class="attribute_value"><?=$form->output('name')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Description:</td>
					<td class="attribute_value"><?=$form->output('description')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Maximum Weight:</td>
					<td class="attribute_value"><?=$form->output('weight')?></td>
				</tr>
			</table>
		</div>
		<hr/>
		<div class="padded_row"><?=$form->output('cancel')?> <?=$form->output('submit')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>