<?php
$_ACCESS = 'all';
$_SECTION = 'Home';
$_PAGE = 'Home';

require_once('inc.php');

http::register_path();
$order_list = new dbo_list('order','WHERE `status` = "unconfirmed"');
$unconfirmed = $order_list->count();
$order_list = new dbo_list('order','WHERE `status` = "unconfirmed" AND date_created > '.date('Ymd'));
$today_order = $order_list->count();

$order_list = new dbo_list('order','WHERE `status` = "confirmed"');
$confirmed = $order_list->count();
$order_list = new dbo_list('order','WHERE `status` = "confirmed" AND date_created > '.date('Ymd'));
$today_confirmed = $order_list->count();

$order_list = new dbo_list('order','WHERE `status` = "processing"');
$processing = $order_list->count();

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_home.php')?></td>
    <td id="content">
		<?=html_message::show()?>
		<div class="info">
			<br/>
			<h4>Welcome <?=$_STAFF->fullname?>!</h4>
			<br />
			<p>Today's Date: <?=date('d/m/Y')?>
			</p>
			<?php if (access::verify($_STAFF->access, 'order')) {?>
			<p>
				You have <b><?=$unconfirmed > 0 ? $unconfirmed : 'no'?></b>	new order<?=$unconfirmed == 1 ? '' : 's'?> awaiting payment confirmation (<b><?=$today_order?></b> of them are received today). <?php if ($unconfirmed > 0) { ?><a href="order_order.php?status=unconfirmed">Click here to view new orders.</a><?php } ?>
			</p>
			<p>
				You have <b><?=$confirmed > 0 ? $confirmed : 'no'?></b>	confirmed order<?=$confirmed == 1 ? '' : 's'?> to be processed. (<b><?=$today_confirmed?></b> of them are received today).<?php if ($confirmed > 0) { ?><a href="order_order.php?status=confirmed">Click here to view confirmed orders.</a><?php } ?>
			</p>

			<p>
				You have <b><?=$processing > 0 ? $processing : 'no'?></b>	processing order<?=$processing == 1 ? '' : 's'?> to be dispatched. <?php if ($processing > 0) { ?><a href="order_order.php?status=processing">Click here to view processing orders.</a><?php } ?>
			<? } ?>
			</p>
		</div>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>

