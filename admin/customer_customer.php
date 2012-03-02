<?php
$_ACCESS = 'customer';
$_SECTION = 'Customer';
$_PAGE = 'Customer Accounts';

require_once('inc.php');

http::register_path();

$view_mode = 'all';
$key = '';
if (isset($_GET['show'])) {
	$view_mode = $_GET['show'];
}
if (isset($_GET['key'])) {
	$view_mode = 'search';
	$key = $_GET['key'];
}

$breadcrumbs = array('Home'=>'./', 'Customer'=>'customer_customer.php',$_PAGE=>'');

switch ($view_mode) {
	case 'all':
		$customer_list = new dbo_list('customer');
		break;
	case 'search':
		//search by email or name 
		$customer_list = new dbo_list('customer', 'WHERE `fullname` LIKE "%'.$key.'%" OR `email` LIKE "%'.$key.'%"');
		break;
}

$len = $customer_list->count();

$pager = new html_pager($customer_list, array('fullname'=>'Name', 'email'=>'Email','date_created'=>'Date Created'), 'd');

$form_search = new html_form('form_search', $_SERVER['PHP_SELF'].'?'.http::build_query($_GET, 'key'), 'GET');
$form_search->add(new html_form_text('key', false, $key));
$form_search->add(new html_form_image_button('submit', 'images/icon_search.gif', '', 'icon_btn'));

$form = new html_form('form_customer_customer', 'action_customer_customer.php');
foreach ($pager->get_page() as $this_customer) {
	$form->add(new html_form_checkbox('checked', $this_customer->customer_id, 'checkbox', false, "javacript:checkAllTicked('form_customer_customer', 'checked[]', 'check_all');"));
}
$form->add(new html_form_button('submit_delete', 'Delete Account', '', 'submit', true));
$form->register();

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_customer.php') ?></td>
    <td id="content">
		<?=html::breadcrumb($breadcrumbs)?>
		<div id="page_title">
			<div class="search">
				Search by name or email: 
				<?=$form_search->output_open()?>
				<?=$form_search->output('key')?>
				<?=$form_search->output('submit')?>
				<?=http::hidden_fields($_GET, 'key')?>
				<?=$form_search->output_close()?>
			</div>
			<?=$_PAGE?>
		</div>
		<?=html_message::show()?>
		<div class="band">
			<?php if ($view_mode == 'all') { ?>
				Show All
			<?php } else { ?>
				<a href="<?=$_SERVER['PHP_SELF']?>?<?=http::build_query($_GET, array('show', 'key'))?>">Show All</a>
			<?php } ?>
		</div>
		<?=$pager->show()?>
		<?=$form->output_open()?>
		<!--<div class="band"><?=$form->output('submit_delete')?></div>-->
		<table width="100%" cellpadding="0" cellspacing="0" border="0" class="data_table">
			<tr class="table_heading">
				<!--<th width="10px"><input name="check_all" type="checkbox" class="checkbox" onclick="checkAll('form_customer_customer','check_all','checked[]');"/></th>-->
				<?=$pager->column('fullname')?>
				<?=$pager->column('email')?>
				<?=$pager->column('date_created')?>
				<th>Action</th>
			</tr>
			<?php if ($len == 0 ) { ?>
			<tr class="table_row">
				<td colspan="7" align="center">There are no customers</td>
			</tr>
			<?php } else {
				foreach ($pager->get_page() as $this_customer) { ?>
				<tr class="table_row">
					<!--<td><?=$form->output('checked', $this_customer->customer_id)?></td>-->
					<td align="center"><a href="customer_customer_view.php?id=<?=$this_customer->customer_id?>"><?=$this_customer->fullname?></a></td>
					<td align="center"><a href="customer_customer_view.php?id=<?=$this_customer->customer_id?>"><?=$this_customer->email?></a></td>
					<td align="center"><a href="customer_customer_view.php?id=<?=$this_customer->customer_id?>"><?=utils_time::date($this_customer->date_created)?></a></td>
					<td align="center"><a href="customer_customer_view.php?id=<?=$this_customer->customer_id?>" title="View Account"><img src="images/icon_view.gif"/></a> 
					<!-- <a href="customer_customer_delete.php?id[]=<?=$this_customer->customer_id?>" title="Delete Account"><img src="images/icon_delete.gif"/></a>-->
					</td>
				</tr>
				<?php } ?>
			<?php } ?>
		</table>
		<!--<div class="band"><?=$form->output('submit_delete')?></div>-->
		<?=$form->output_close()?>
		<?=$pager->show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>