<?php
$_ACCESS = 'delivery';
$_SECTION = 'Delivery';
$_PAGE = 'Delivery Price Table';

require_once('inc.php');

http::register_path();

$breadcrumbs = array('Home'=>'./', $_SECTION=>'delivery_matrix.php', $_PAGE=>'');

$class_list = new dbo_list('delivery_class', '');
$classes = $class_list->get_all();

$zone_list = new dbo_list('zone');
$zones = $zone_list->get_all();

$form = new html_form('form_system_delivery_matrix', 'action_delivery_matrix.php');

foreach ($classes as $class) {
	foreach ($zones as $zone) {
		$matrix_list = new dbo_list('delivery_matrix', 'WHERE `delivery_class_id` = "'.$class->delivery_class_id.'" AND `zone_id` = "'.$zone->zone_id.'"');
		if(($matrix = $matrix_list->get_first()) === false) {
			$av = array();
		} else {
			$av = array('true');
		}
		$form->add(new html_form_text('price_'.$class->delivery_class_id.'_'.$zone->zone_id, false, $matrix?$matrix->price:'', '', false, 6));
	}
}
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
			<table border="0" cellpadding="0" cellspacing="1" width="100%" class="data_table">
				<tr class="table_heading">
					<th>Zone \ Class</th>
					<?php foreach ($classes as $class) {?>
					<th><?=$class->name?></th>
					<?php } ?>
				</tr>

				<?php
				foreach ($zones as $zone) {
				?>
					<tr class="table_row">
						<td><?=$zone->name?></td>
						<?php
						foreach ($classes as $class) {
						$matrix_list = new dbo_list('delivery_matrix', 'WHERE `delivery_class_id` = "'.$class->delivery_class_id.'" AND `zone_id` = "'.$zone->zone_id.'"');
						$matrix = $matrix_list->get_first();
						?>
						<td align="center">
							$ <?=$form->output('price_'.$class->delivery_class_id.'_'.$zone->zone_id)?>
						</td>
						<?php } ?>
					</tr>
				<?php } ?>
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