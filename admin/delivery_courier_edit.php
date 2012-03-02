<?php
$_ACCESS = 'delivery';
$_SECTION = 'Delivery';
$_PAGE = 'Edit Courier';

require_once('inc.php');

if (isset($_GET['id'])) {
	$courier = new dbo('courier', $_GET['id']);
} else {
	http::halt();
}

$breadcrumbs = array('Home'=>'./', $_SECTION=>'delivery_matrix.php', $_PAGE=>'');

$form = new html_form('form_delivery_delivery_courier_edit', 'action_delivery_courier_edit.php?id='.$courier->courier_id);
$form->add(new html_form_text('name', true, $courier->name, '', false, 80));
$form->add(new html_form_textarea('contact', true, $courier->contact, '', 80));
$form->add(new html_form_button('submit', 'Save', '', 'submit', true));
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
					<td class="attribute_label">Courier Name:</td>
					<td class="attribute_value"><?=$form->output('name')?></td>
				</tr>
				<tr>
					<td class="attribute_label">Contact Details:</td>
					<td class="attribute_value"><?=$form->output('contact')?></td>
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