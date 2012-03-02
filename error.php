<?php
require_once('inc.php');
header('Status: 404');
$_PAGE = 'page not found';
?>
<?php require_once('inc_header.php') ?>
	<div id="content">
		<div class="breadcrumb"><ul><li><a href="./">home</a></li> <li>/</li> <li class="selected"><?=$_PAGE?></li></ul></div>
		<div class="panel_utility">
			<p>Sorry, the page that you are looking for could not be found.</p>
			<br />
			<a href="<?=$_ROOT?>">Back to Eyestyle Homepage</a>
		</div>
	</div>
	<?php require_once('inc_footer.php'); ?>