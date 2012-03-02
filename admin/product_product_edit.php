<?php
$_ACCESS = 'product.product';
$_SECTION = 'Product';
$_PAGE = 'Edit Product';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));
http::register_path();

$product = new dbo('product', $_GET['id']);

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
$category_list = new dbo_list('category','','name');
$category_options = array();
foreach($category_list->get_all() as $category) {
	$category_options[$category->category_id] = $category->name;
}
$gender_options = array();
$gender_options[0] = 'Both Genders';
$gender_options[1] = 'Mens';
$gender_options[2] = 'Womens';



$form = new html_form('form_product_product_edit', 'action_product_product_edit.php?'.http::build_query($_GET));
$form->add(new html_form_text('name', true,$product->name,'full'));
$form->add(new html_form_select('category_id_1',$category_options,'-- select a category--',true,false,'',$product->category_id_1));
$form->add(new html_form_select('category_id_2',$category_options,'-- select a category--',false,false,'',$product->category_id_2));
$form->add(new html_form_select('brand_id',$brand_options,'',true, false,'',$product->brand_id));
$form->add(new html_form_select('delivery_id',$delivery_options,'',true, false,'',$product->delivery_class_id));
$form->add(new html_form_select('parent_category',$gender_options,'',true, false,'',$product->parent_category));
$form->add(new html_form_text('sub_heading', false,$product->sub_heading,'full'));
$form->add(new html_form_text('price', true,$product->price,'','',10));
//$form->add(new html_form_text('weight', true,$product->weight,'','',10));
$form->add(new html_form_textarea('features', true,$product->features,'full',40,8));

$form->add(new html_form_file('image_1',false));
for($i=2; $i<=7; $i++) {
	$form->add(new html_form_file('image_'.$i));
	$form->add(new html_form_checkbox('img_delete', $i, 'checkbox'));
}

$form->add(new html_form_file('image_rollover', false));

$colour_list = $product->load_children('colour');
foreach ($colour_list as $colour){
	$form->add(new html_form_text('colour_'.$colour->colour_id,true,$colour->name,'full'));
	$lens_list = $colour->load_children('lens');
	foreach ($lens_list as $lens){
		$form->add(new html_form_text('code_'.$colour->colour_id.'_'.$lens->lens_id, false,$lens->code,'',false,25));
		$form->add(new html_form_text('lens_'.$colour->colour_id.'_'.$lens->lens_id, false,$lens->name,'',false,10));
	}
	//For adding a new lens on a colour
	$form->add(new html_form_text('new_code_'.$colour->colour_id,false,'','',false,25));
	$form->add(new html_form_text('new_lens_'.$colour->colour_id,false,'','',false,10));
}

$form->add(new html_form_text('new_colour',false,'','',false,30));




///////// aus_only checking

if ($product->aus_only) {
	$aus_only_checked = true;
}
else {
	$aus_only_checked = false;	
}

$form->add(new html_form_checkbox('aus_only',1,'',$aus_only_checked, ''));

///////////////////////



//Adding a whole new colour? how can it be doen?


$form->add(new html_form_button('submit', 'Save'));
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
					while($i <= 7) {
						$img = 'image_'.$i;
						if ((($i-1) % 2) == 0) {
				?>
				<tr>
					<?php } ?>
					<td class="attribute_label" width="80">Image <?=$i?>: <?=html::check_required($form,$img)?></td>
					<td class="attribute_value" width="150"><?php if (!empty($product->$img)) { ?>
						<img src="../thumb.php?nocache=true&f=<?=$product->$img?>&mw=150&mh=150" alt="Image <?=$i?>" class="bordered" /><br />
						<?=$form->output($img)?><br />
							<?php if ($i != 1) { ?>
							<div class="img_delete"><?=$form->output('img_delete',$i)?> Delete Image</div>
							<?php } ?>
						<?php } else { ?>
						<?=$form->output($img)?>
						<?php } ?>
					</td>
					<?php
						$i++;
						if ((($i-1) % 2) == 0) {
					?>
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
					<td class="attribute_value" width="150"><?php if (!empty($product->image_rollover)) { ?>
						<img src="../thumb.php?nocache=true&f=<?=$product->image_rollover?>&mw=150&mh=150" alt="Rollover Image" class="bordered" /><br />
						<?=$form->output('image_rollover')?><br />
						<?php } else { ?>
						<?=$form->output('image_rollover')?>
						<?php } ?>
					</td>
					<td width="80"></td>
					<td width="250"></td>
				</tr>
			</table>
			<br />
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td colspan="2"><div class="page_subtitle">Stock Management</div></td>
				</tr>
				<tr>
					<td>
						Add new colour:	<?=$form->output('new_colour')?>
						
						<br><br>
						<? $colour_list = $product->load_children('colour');
							foreach ($colour_list as $colour) { ?>
								<table cellpadding="0" cellspacing="0" border="0" width="60%" class="table_product">
								<tr class="table_heading">
									<td align=left>Colour :</td>
									<td align=left><?=$form->output('colour_'.$colour->colour_id)?></td>
									<td>
										<a href="action_product_colour_delete.php?id=<?=$colour->colour_id?>"><img src="images/icon_delete.gif" />Delete this colour</th></a>
									</td>
								</tr>
								<tr class="table_heading">
									<th><b>Product Code</b></th>
									<th><b>Lens Type</b></th>
									<th><b>Action</b></th>
								</tr>
								<?  $lens_list = $colour->load_children('lens');
									foreach ($lens_list as $lens) { ?>
									<tr class="table_row">
										<td align="center"><?=$form->output('code_'.$colour->colour_id.'_'.$lens->lens_id)?></td>
										<td align="center"><?=$form->output('lens_'.$colour->colour_id.'_'.$lens->lens_id)?></td>
										<td align="center"><a href="action_product_lens_delete.php?id=<?=$lens->lens_id?>">
											<img src="images/icon_delete.gif">Delete this lens</a>
										</td>
									</tr>
								<? } ?>
								<tr class="table_row">
									<td align="left" colspan="4">Add new lens for this colour:</td>
								</tr>
								<tr class="table_row">
									<td align="center"><?=$form->output('new_code_'.$colour->colour_id)?></td>
									<td align="center"><?=$form->output('new_lens_'.$colour->colour_id)?></td>
									<td></td>
								</tr>
								</table>
								<br><br>
						<? } ?>

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
