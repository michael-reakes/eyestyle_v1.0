<?php
require('inc.php');

http::halt_if(!isset($_GET['url']));
$url = str_replace("../","",$_GET['url']);
http::redirect($url);
?>