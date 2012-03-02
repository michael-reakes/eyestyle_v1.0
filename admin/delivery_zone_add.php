<?php
$_ACCESS = 'delivery';
$_SECTION = 'Delivery';
$_PAGE = 'Add Zone';

require_once('inc.php');

$breadcrumbs = array('Home'=>'./', 'Delivery'=>'delivery_matrix.php', 'Delivery Zones'=>'delivery_zones.php', $_PAGE=>'');

$domestic_zone_list = new dbo_list('post_zone', '', 'post_zone_id');
$d_zones = $domestic_zone_list->get_all();


$form = new html_form('form_delivery_zone_add', 'action_delivery_zone_add.php');
$form->add(new html_form_text('name', true, '', '', false, 80));

foreach ($d_zones as $d_zone) {
	$form->add(new html_form_checkbox('d_zone_id', $d_zone->post_zone_id));
}

$form->add(new html_form_button('submit', 'Add Zone', '', 'submit', true));
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
					<td class="attribute_label">Zone Name:</td>
					<td class="attribute_value"><?=$form->output('name')?></td>
				</tr>
				<tr>
					<td class="attribute_label">&nbsp;</td>
					<td class="attribute_value">
						<div id="domestic" style="display:block;">
							<h5>eParcel Zone Mapping:</h5>
							<table border="0" cellpadding="0" cellspacing="5">
								<?php
								for ($i=0; $i<count($d_zones); $i++) {
									$d_zone = $d_zones[$i];
									if ($i%3 == 0 ) {
								?>
										<tr>
								<?php
									}
								?>
										<td><?=$form->output('d_zone_id', $d_zone->post_zone_id)?></td>
										<td><?=$d_zone->post_zone_id?> - <?=$d_zone->name?></td>
								<?php
									if ($i%3 == 2 || $i == count($d_zones)) {
								?>
									</tr>
								<?php
									}
								}?>
							</table>
						</div>

					</td>
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