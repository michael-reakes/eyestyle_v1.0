<?php
$_ACCESS = 'delivery';
$_SECTION = 'Delivery';
$_PAGE = 'Delete Zone(s)';

require_once('inc.php');

if (isset($_GET['id']) && is_array($_GET['id'])) {
	$zone_array = array();
	foreach ($_GET['id'] as $id) {
		$zone_array[] = new dbo('zone', $id);
	}
} else {
	http::halt();
}

$form = new html_form('form_delivery_zone_delete', 'action_delivery_zone_delete.php?'.http::build_query($_GET));

$form->add(new html_form_button('delete', 'Delete'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Delivery'=>'delivery_matrix.php', 'Delivery Zones'=>'delivery_zones.php',$_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_delivery.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=$form->output_open()?>
		<div class="info">
			<h4>Are you sure?</h4>
			<p>Are you sure you want to delete the following zone(s)?</p>
			<ul>
				<?php foreach ($zone_array as $zone) {?>
				<li><?=$zone->name?></li>
				<?php } ?>
			</ul>
		</div>
		<hr />
		<div class="padded_row"><?=$form->output('cancel')?>&nbsp;<?=$form->output('delete')?></div>
		<?=$form->output_close()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>