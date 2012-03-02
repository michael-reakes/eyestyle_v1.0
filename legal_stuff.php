<?
	require_once('inc.php');
	$_PAGE = 'legal stuff';

	$page = new dbo('page', 3);
?>	<?php require_once('inc_header.php') ?>
	<div id="content">
		<div class="breadcrumb"><ul><li><a href="./">home</a></li> <li>/</li> <li class="selected"><?=$_PAGE?></li></ul></div>
		<div class="panel_utility">
			<?=$page->content?>			
		</div>
	</div>
	<?php require_once('inc_footer.php'); ?>