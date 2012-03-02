<?php
$_ACCESS = 'delivery';
$_SECTION = 'Delivery';
$_PAGE = 'Add Zone';

require_once('inc.php');

$breadcrumbs = array('Home'=>'./', 'Delivery'=>'delivery_matrix.php', 'Delivery Zones'=>'delivery_zones.php', $_PAGE=>'');

$mode = 'add';
$type = 'domestic';
if (isset($_GET['id'])) {
	$zone = new dbo('zone', $_GET['id']);
	$domestic_zone_list = new dbo_list('post_zone', 'WHERE `zone_id` = "'.$zone->zone_id.'"');
	$selected_d_zones = array();
	foreach ($domestic_zone_list->get_all() as $d_zone) {
		$selected_d_zones[] = $d_zone->post_zone_id;
	}
	$country_list = new dbo_list('country', 'WHERE `zone_id` = "'.$zone->zone_id.'"');
	$selected_countries = array();
	foreach ($country_list->get_all() as $country) {
		$selected_countries[] = $country->code;
	}
	$type = $zone->type;
	$mode = 'edit';
} else{
	$selected_countries = array();
	$selected_d_zones = array();
}

$domestic_zone_list = new dbo_list('post_zone', '', 'post_zone_id');
$d_zones = $domestic_zone_list->get_all();
$country_list = new dbo_list('country', 'WHERE `code` != "AU"', 'name');
$country_arr = $country_list->get_all();


$form = new html_form('form_delivery_zone', 'action_delivery_zone_add_edit.php');
$form->add(new html_form_hidden('mode',$mode));
if ($mode == 'edit') $form->add(new html_form_hidden('id',$_GET['id']));
$form->add(new html_form_text('name', true, ($mode == 'edit') ? $zone->name : '', '', false, 80));

$form->add(new html_form_radio('type', 'domestic', '', ($mode == 'edit') ? $zone->type : true, "javascript:toggleVisibility('domestic');toggleVisibility('international');"));
$form->add(new html_form_radio('type', 'international', '', ($mode == 'edit') ? $zone->type : false, "javascript:toggleVisibility('domestic');toggleVisibility('international');"));


foreach ($d_zones as $d_zone) {
	$form->add(new html_form_checkbox('d_zone_id', $d_zone->post_zone_id, '', $selected_d_zones));
}
foreach ($country_arr as $country) {
	$form->add(new html_form_checkbox('country', $country->code, '', $selected_countries));
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
			<table>
				<tr>
					<td class="attribute_label">Zone Type:</td>
					<td class="attribute_value"><?=$form->output('type', 'domestic')?> Domestic &nbsp;&nbsp;&nbsp;&nbsp; <?=$form->output('type', 'international')?> International</td>
				</tr>
			</table>
			
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="attribute_label">Zone Name:</td>
					<td class="attribute_value"><?=$form->output('name')?></td>
				</tr>
				<tr>
					<td class="attribute_label">&nbsp;</td>
					<td class="attribute_value">
						<div id="domestic" style="display:<?=$type=='domestic'?'block':'none'?>;">
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
						<div id="international" style="display:<?=$type == 'international'?'block':'none'?>;">
							<h5>Country Mapping:</h5>
								<div class="info">
									<table border="0" cellpadding="0" cellspacing="5">
									<?php
									$i = 0;
									foreach ($country_arr as $country) {
										if ($i%4 == 0 ) {
									?>
											<tr>
									<?php } ?>
												<td><?=$form->output('country', $country->code)?></td>
												<td><?=$country->name?></td>
									<?php if ($i%4 == 3) { ?>
											</tr>
									<?php }
										$i++;
									}
									?>
									</table>
								</div>
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