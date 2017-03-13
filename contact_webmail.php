<?php
  //create short variable names
  $comment = $HTTP_POST_VARS['comment'];
  $subject = $HTTP_POST_VARS['subject'];
  $email = $HTTP_POST_VARS['email'];

 
  $DOCUMENT_ROOT = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
?>
<html>
<head>
  <title>Thank you for your comment!</title>
  <link rel="stylesheet" type="text/css" href="stylesheet.CSS">
</head>
<body>
<h1>TimeWeather.net</h1>
<?php 
 
include ('connect.php');

$query = 'insert into comments values ( "'.date('Y-m-d').' '.date('H:i:00').'","'.PRODUCT.'","'.$_SERVER['REMOTE_ADDR'].'","'.$subject.$comment.'")';

$result = mysql_query ( $query );

if (!$result )
	echo 'Logging failed';

mysql_close ( $db );

//if ( mail("jeff.hill@tpg.com.au", "My Subject", "Line 1\nLine 2\nLine 3") != TRUE )
//echo 'mail failed';
mail ('support@timeweather.net', $subject, $comment, 'Submitted by: '.$email.' at '.$_SERVER['REMOTE_ADDR'] );


  echo '<p>';
  echo 'Your request has been submitted';
  echo '<br>';
  echo $subject.'<br>';
  echo $email.'<br>';
  echo $comment.'<br>';
  
  echo "<A href='default.php'>Back to main page</a>";
?>
</body>
</html>

