<?php
$_ACCESS = 'newsletter.newsletter';

require_once('inc.php');

http::halt_if(!isset($_GET['id']));

$newsletter = new dbo('newsletter', $_GET['id']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<body>
<?=$newsletter->body?>
</body>
</html>