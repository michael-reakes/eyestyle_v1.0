<?
require_once('inc.php');
$_PAGE = 'shopping cart';

$form_cart = new html_form('form_cart', 'action_cart_update.php');
$product_id_temp = array();
foreach($_CHECKOUT->cart as $key=>$value) {
	$form_cart->add(new html_form_text($key,true,$value,'acenter',false,4,5));
}
$form_cart->add(new html_form_button('submit','Update Cart'));
$form_cart->add(new html_form_image_button('btn_update','images/btn/update_cart.gif','update','no_border'));
$form_cart->register();



?>
<?php require_once('inc_header.php') ?>

		<div id="content">
			<div class="breadcrumb"><a href="<?=$_ROOT?>">home</a> / <span class="selected"><?=$_PAGE?></span></div>
			<?=html_message::show()?>
			<?=$form_cart->output_open()?>
			<? if ($_CHECKOUT->cart_total_items() <= 0) { ?>
				There are no items in your shopping cart. 
			<? } else { ?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" class="grid marginbottom">
				<tr>
					<th colspan="2">Product Name</th>
					<th width="50" class="acenter">Qty</th>
					<th width="100" class="aright">Subtotal</th>
					<th width="40">Remove</th>
				</tr>
				<? 
					$i = 0;
					foreach ($_CHECKOUT->cart as $key=>$value){
						//candidate of join?
						$lens = new dbo('lens',$key);
						$colour = new dbo('colour',$lens->colour_id);
						$product = new dbo('product',$colour->product_id);
						if ($i % 2 == 0) $class = "row_ab_a";
						else $class = "row_ab_b";

				?>
				<tr class="<?=$class?>">
					<td width="99"><img src="<?=$_ROOT.getSmallThumbnail($product->image_1)?>" alt="<?=$product->name?>" width="99" height="49" /></td>
					<td>
						<h6><a href="<?=url::linkProduct($product)?>"><?=$product->name?></a></h6>
						<?=$colour->name?>/<?=$lens->name?><br />
						Unit Price: <?=html_text::currency($product->price)?>
					</td>
					<td class="acenter"><?=$form_cart->output($key)?></td>
					<td class="aright"><?=html_text::currency($product->price * $value)?></td>
					<td class="acenter"><a href="<?=$_ROOT?>action_cart_remove.php?pid=<?=$lens->lens_id?>"><img src="<?=$_ROOT?>images/icons/cross.jpg" alt="Remove product" width="17" height="17" /></td>					
				</tr>
					<?
						$i++;
					}
					?>
			</table>
			Have you made any changes to your shopping bag?
			<button type="submit" value="Update Cart">Update Cart</button>
			<?=$form_cart->output_close()?>
			<? } ?>
			<div class="actionbuttons clearfix">
				<? if ($_CHECKOUT->cart_total_items() > 0) { ?>
					<div class="fright"><a class="actionbutton" href="<?=http::url('checkout.php',true)?>">Proceed to Checkout</a></div>
				<? } ?>
				<a class="actionbutton" href="<?=isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : http::url('./')?>">Continue Shopping</a>
			</div>
			</form>
		</div>



	<?php require_once('inc_footer.php'); ?>