FCKEditor 2.3 S3 Modified:
- To set the path to absolute or relative see this example to iniate a fckeditor:
PHP version:
<?php
	require_once('inc.php');
	include("include/fckeditor/fckeditor.php") ;
?>
	<?php
		$oFCKeditor = new FCKeditor('html') ;
		$oFCKeditor->BasePath = 'include/fckeditor/';
		$oFCKeditor->ToolbarSet = 'S3Group';
		$config = array();
		$config['AbsolutePath'] = true;
		$oFCKeditor->Config = $config;
		$oFCKeditor->Create() ;
	?>
?>

Javascript version:
<script type="text/javascript" src="include/fckeditor/fckeditor.js"></script>
<script type="text/javascript">
	var oFCKeditor = new FCKeditor('html') ;
	var Config = new Array()
	Config['AbsolutePath'] = false;
	oFCKeditor.BasePath	= 'include/fckeditor/' ;
	oFCKeditor.Config = Config;
	oFCKeditor.Height = 600;
	oFCKeditor.ReplaceTextarea() ;
</script>

- Edit the inc.file on include/fckeditor/inc.php, the file is self explanatory