<?php
require_once('inc.php');
http::halt_if(!isset($_GET['alias']));
$alias = $_GET['alias'];
$page_list = new dbo_list('page','WHERE `alias` = "'.$alias.'"');
http::halt_if(!($page = $page_list->get_first()));

$_PAGE = strtolower($page->title);

$content = $page->content;
?>
<?php require_once('inc_header.php') ?>
	<div id="content">
		<div class="breadcrumb"><ul><li><a href="./">home</a></li> <li>/</li> <li class="selected"><?=$_PAGE?></li></ul></div>
		<div class="panel_utility">
			<?=$content?>
		</div>
	</div>
	<?php require_once('inc_footer.php'); ?>