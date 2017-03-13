<?php
  //create short variable names
  $comment = $HTTP_POST_VARS['comment'];
 
  $DOCUMENT_ROOT = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
?>
<html>
<head>
  <title>Thank you for your comment!</title>
  <link rel="stylesheet" type="text/css" href="stylesheet.CSS">
</head>
<body>
<h1>timeweather.net</h1>
<?php 
 
include ('connect.php');

$query = 'insert into comments values ( "'.date('Y-m-d').' '.date('H:i:00').'","'.PRODUCT.'","'.$_SERVER['REMOTE_ADDR'].'","'.$comment.'")';

$result = mysql_query ( $query );

if (!$result )
	echo 'Logging failed';

mysql_close ( $db );

//if ( mail("jeff.hill@tpg.com.au", "My Subject", "Line 1\nLine 2\nLine 3") != TRUE )
//echo 'mail failed';
mail ('support@timeweather.net', 'Website Comment', $comment, 'Submitted by: '.$_SERVER['REMOTE_ADDR'] );
//mail ('website@jeffs-software.com', 'Website comment', $comment, 'Submitted by: '.$_SERVER['REMOTE_ADDR'] );


  echo '<p>';
  echo 'Your comment has been sent';
  echo '<br>';
  
  echo "<A href='default.php'>Back to main page</a>";
?>
</body>
</html>

