<?php
require_once('inc.php');

customer::logout();

http::redirect(http::url('index.php'));

?>