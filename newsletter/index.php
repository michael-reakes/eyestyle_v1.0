<?
	require_once('../inc.php');
	$_PAGE = 'newsletter';
	
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
	}else{
		$id = 1;	
	}
	$newsletter = new dbo('newsletter',$id);
	//body -> get rid of {CUSTOMER}
	$body = $newsletter->body;
	$body = str_replace('{CUSTOMER}','customer',$body);
	$body = str_replace('{LINK}','please <a href="mailto:info@eyestyle.com.au">email us</a>',$body);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<meta content="" name="Keywords" />
<meta content="" name="Description" />
<body style="width:601px;margin:0px auto;">
<?=$body?>
</body>
</html>