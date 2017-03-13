<?php
$db = mysql_connect("localhost", 'webuser', 'goose1604') or die("Could not connect.");
if(!$db) 
	die("no db");
if(!mysql_select_db("web",$db))
 	die("No database selected.");
?>
