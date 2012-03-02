<?php
define('BEGIN_TRANSACTION', 1);
define('END_TRANSACTION', 2);

include_once("db/db_class.php");

$_DB = new sql_db($_CONFIG['db']['host'], $_CONFIG['db']['username'], $_CONFIG['db']['password'], $_CONFIG['db']['db'], $_CONFIG['db']['persistent']);
if(!$_DB->db_connect_id)
{
   die("Could not connect to the database");
}

include_once("db/db_schema.php");
include_once("db/db_utils.php");
include_once("db/db_object.php");
?>