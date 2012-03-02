<?php
$_ACCESS = 'all';
$_SECTION = 'Error';

require_once('inc.php');

require_once('inc_header.php');
?>
  <tr>
    <td id="menu"><?php require_once('inc_menu_home.php')?></td>
    <td id="content">
		<div class="page_title">
			An unexpected error has occured, please contact us at <a href="mailto:ft@s3group.com.au">ft@s3group.com.au</a>
			with the details of what you are trying to do, to get the error resolved. 
		</div>
		<?=html_message::show()?>
	</td>
  </tr>
<?php
require_once('inc_footer.php');
?>

