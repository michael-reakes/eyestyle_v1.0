<?php
$_ACCESS = 'product.product';
$_SECTION = 'Product';
$_PAGE = 'Add Product';

require_once('inc.php');

$brand_list = new dbo_list('brand','','name');
$brand_options = array();
foreach($brand_list->get_all() as $brand) {
	$brand_options[$brand->brand_id] = $brand->name;
}
$delivery_list = new dbo_list('delivery_class','','name');
$delivery_options = array();
foreach($delivery_list->get_all() as $delivery) {
	$delivery_options[$delivery->delivery_class_id] = $delivery->name;
}
$selected_dbo = $delivery_list->get_first();
$selected_id = $selected_dbo->delivery_class_id;

$category_list = new dbo_list('category','','name');
$category_options = array();
foreach($category_list->get_all() as $category) {
	$category_options[$category->category_id] = $category->name;
}

$gender_options = array();
$gender_options[0] = 'Both Genders';
$gender_options[1] = 'Mens';
$gender_options[2] = 'Womens';

$form = new html_form('form_product_product_add', 'action_product_product_add.php');
$form->add(new html_form_text('name', true,'','',false,100));
$form->add(new html_form_select('parent_category',$gender_options,'-- select a gender --',true));
$form->add(new html_form_select('category_id_1',$category_options,'-- select a category--',true));
$form->add(new html_form_select('category_id_2',$category_options,'-- select a category--'));
$form->add(new html_form_select('brand_id',$brand_options,'-- Select a designer --',true));
$form->add(new html_form_select('delivery_id',$delivery_options,'-- Select a delivery class --',true,false,'',$selected_id));
$form->add(new html_form_select('parent_category',$gender_options,'',true));
$form->add(new html_form_text('sub_heading', false,'','',false,100));
$form->add(new html_form_text('price', true,'','',false,10));
//$form->add(new html_form_text('weight', true,'','',false,5));
$form->add(new html_form_textarea('features', true,'','full',40,8));

$form->add(new html_form_file('image_1',true));
for($i=2; $i<=7; $i++) {
	$form->add(new html_form_file('image_'.$i));
}
$form->add(new html_form_file('image_rollover', true));

$form->add(new html_form_checkbox('aus_only',1));

for ($x=1; $x<=3; $x++){
	$form->add(new html_form_text('colour_'.$x, false,'','',false,30));
	for ($i=1; $i<=3; $i++){
		$form->add(new html_form_text('code_'.$x.'_'.$i, false,'','',false,25));
		$form->add(new html_form_text('lens_'.$x.'_'.$i, false,'','',false,10));
	}
}

$form->add(new html_form_button('submit', 'Add Product'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Product Management'=>'product_product.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_product.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=html_message::show()?>
		<?=$form->output_open()?>
		<div class="info">
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td class="attribute_label" width="140">Product Name: <?=html::check_required($form,'name')?></td>
					<td class="attribute_value"><?=$form->output('name')?></td>
				</tr>

				<tr>
					<td class="attribute_label">Sub heading: <?=html::check_required($form,'sub_heading')?></td>
					<td class="attribute_value"><?=$form->output('sub_heading')?></td>
				</tr>
				<tr valign="top">
					<td class="attribute_label">Gender: <?=$form->output_required('parent_category')?></td>
					<td class="attribute_value">
						<?=$form->output('parent_category')?>
					</td>
				</tr>

				<tr valign="top">
					<td class="attribute_label">Category 1: <?=$form->output_required('category_id_1')?></td>
					<td class="attribute_value">
						<?=$form->output('category_id_1')?>
					</td>
				</tr>

				<tr valign="top">
					<td class="attribute_label">Category 2: <?=$form->output_required('category_id_2')?></td>
					<td class="attribute_value">
						<?=$form->output('category_id_2')?>
					</td>
				</tr>

				<tr>
					<td class="attribute_label">Designer: <?=html::check_required($form,'brand_id')?></td>
					<td class="attribute_value"><?=$form->output('brand_id')?></td>
				</tr>

				<tr>
					<td class="attribute_label">Delivery Class: <?=html::check_required($form,'delivery_id')?></td>
					<td class="attribute_value"><?=$form->output('delivery_id')?></td>
				</tr>
				
				<tr>
					<td class="attribute_label">Australia Only:</td>
					<td class="attribute_value"><?=$form->output('aus_only')?></td>
				</tr>

				<tr>
					<td class="attribute_label">Price: <?=html::check_required($form,'price')?></td>
					<td class="attribute_value">$ <?=$form->output('price')?></td>
				</tr>
				<?php /*
				<tr>
					<td class="attribute_label">Weight: <?=html::check_required($form,'weight')?></td>
					<td class="attribute_value"><?=$form->output('weight')?> gram</td>
				</tr>
				*/ ?>
				<tr>
					<td class="attribute_label">Product Features: <?=$form->output_required('features')?></td>
					<td class="attribute_value">
						<p><em>Each line appears as a bullet point. Please press "Enter" for a new line.</em></p>
						<?=$form->output('features')?>
					</td>
				</tr>
			</table>
			<br />
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="4"><div class="page_subtitle">Images</div></td>
				</tr>
				<?php
					$i = 1;
					while ($i <= 7) {
						$img = 'image_'.$i;
						if ((($i-1) % 2) == 0) {
				?>
				<tr>
					<?php } ?>
					<td class="attribute_label" width="80">Image <?=$i?>:<?php if ($i == 1) { html::check_required($form,'image_1'); } ?></td>
					<td class="attribute_value" width="250"><?=$form->output($img)?></td>
					<?php
						$i++;
						if ((($i-1) % 2) == 0) { ?>
				</tr>
					<?php } ?>
				<?php } ?>
			</table>
			<br />
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="4"><div class="page_subtitle">Rollover Image</div></td>
				</tr>
				<tr>
					<td class="attribute_label" width="80">Image: <?=html::check_required($form,'image_rollover')?></td>
					<td class="attribute_value" width="250"><?=$form->output('image_rollover')?></td>
					<td width="80"></td>
					<td width="250"></td>
				</tr>
			</table>
			<br />
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="2"><div class="page_subtitle">Stock Management</div><td>
				</tr>
				<tr>
					<td>
						<? for ($x=1; $x<=3; $x++) { ?>
							<table cellpadding="0" cellspacing="0" border="0" width="60%" class="table_product">
							<tr class="table_row">
								<td align="center"><B>Colour <?=$x?>:</B></td>
								<td align="center"><?=$form->output('colour_'.$x)?></td>
							</tr>
							<tr class="table_heading">
								<th align="center">Product Code</th>
								<th align="center">Lens Type</th>
							<? for ($i=1; $i<=3; $i++){ ?>
								<tr class="table_row">
									<td align="center"><?=$form->output('code_'.$x.'_'.$i)?></td>
									<td align="center"><?=$form->output('lens_'.$x.'_'.$i)?></td>
								</tr>
							<? } ?>
							</table>
						<? } ?>

						<div class="info_text" style="padding-top:10px">More colour and lens type can be added later via product edit</div>
					</td>
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
