<?php
$_ACCESS = 'all';
$_SECTION = 'Customer';
$_PAGE = 'Delete Customer(s)';

require_once('inc.php');

if (isset($_GET['id']) && is_array($_GET['id'])) {
	$customer_array = array();
	foreach ($_GET['id'] as $id) {
		$customer_array[] = new dbo('customer', $id);
	}
} else {
	http::halt();
}

$form = new html_form('form_customer_delete', 'action_customer_customer_delete.php?'.http::build_query($_GET));

$form->add(new html_form_button('delete', 'Delete'));
$form->add(new html_form_button('cancel', 'Cancel', '', 'button', false, 'javascript:history.back();'));
$form->register();

$breadcrumbs = array('Home'=>'./', 'Customers'=>'customer_customer_retail.php', $_PAGE=>'');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_customer.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title"><?=$_PAGE?></div>
		<?=$form->output_open()?>
		<div class="info">
			<h4>Are you sure?</h4>
			<p>Are you sure you want to delete the following customer(s)?</p>
			<ul>
				<?php foreach($customer_array as $this_customer) {?>
				<li>
					<?php 
						echo "(".$this_customer->fullname.")</li>";
					?>
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