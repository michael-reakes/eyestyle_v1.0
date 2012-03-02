<?php
$_ACCESS = 'storefront';
$_SECTION = 'Store Front';
$_PAGE = 'Store Front';

require_once('inc.php');

http::register_path();

$features = array();
$form = new html_form('form_store_front', 'action_store_front.php');
for ($i=1; $i<=6; $i++) {
	$banner = array();
	$pref = new dbo('preference');
	$form->add(new html_form_text('featurebanner_url_'.$i,false,($pref->load('featurebanner_url_'.$i) ? $pref->value : ''), '', false, 60));
	$banner['url'] = $pref->value;
	$pref = new dbo('preference');
	$form->add(new html_form_file('featurebanner_image_'.$i, false, ($pref->load('featurebanner_image_'.$i) ? $pref->value : '')));
	$banner['image'] = $pref->value;
	$features[] = $banner;
}
$form->add(new html_form_button('submit','Save Changes'));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Store Front Management'=>'store_front.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_store_front.php')?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title">Store Front Management	</div>
		<?=html_message::show()?>
		<div class="info">
			<?=$form->output_open()?>
			<p>Please upload any feature banners for the homepage.</p>
			<table cellspacing="0" cellpadding="0" border="0" class="table_product">
				<?php for ($i=1; $i<=6; $i++) { ?>
				<tr>
					<td>Feature Banner <?=$i?>:</td>
					<td><?php if (is_file('../'.$features[$i-1]['image'])): ?>
						<p><img src="../<?=$features[$i-1]['image']?>" alt="<?=basename($features[$i-1]['image'])?>" width="300" /></p>
						<?php endif; ?>
						<?=$form->output('featurebanner_image_'.$i)?>
					</td>
				</tr>
				<tr>
					<td>Feature Banner <?=$i?> URL:</td>
					<td><?=$form->output('featurebanner_url_'.$i)?></td>
				</tr>
				<?php } ?>
			</table>
			<br />
			<?=$form->output('submit')?>
			<?=$form->output_close()?>
		</div>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>