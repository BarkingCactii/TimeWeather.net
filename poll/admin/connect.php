<?PHP
$db = mysql_connect("localhost", "webadmin", "goose1601") or die("Could not connect.");
if(!$db) 
	die("no db");
if(!mysql_select_db("web",$db))
 	die("No database selected.");

?>