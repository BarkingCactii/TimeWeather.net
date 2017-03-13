<?php
  //create short variable names
  $email = $HTTP_POST_VARS['email'];
  $name = $HTTP_POST_VARS['name'];
  $briefdescription = $HTTP_POST_VARS['briefdescription'];
  $description = $HTTP_POST_VARS['description'];
  $sitelink = $HTTP_POST_VARS['sitelink'];
  $screenshotlink = $HTTP_POST_VARS['screenshotlink'];
  $screenshottnlink = $HTTP_POST_VARS['screenshottnlink'];
  $downloadlink = $HTTP_POST_VARS['downloadlink'];
  $buylink = $HTTP_POST_VARS['buylink'];

  $DOCUMENT_ROOT = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];
?>
<html>
<head>
  <title>Thank you for your link submission!</title>
  <link rel="stylesheet" type="text/css" href="stylesheet.CSS">
</head>
<body>
<h1>TimeWeather.net</h1>
<?php 
 

include ('connect.php');

$query = 'insert into product_details values ( "'.date('Y-m-d').' '.date('H:i:00').'","'.$name.'","'.$description.'","'.$buylink.'","'.$sitelink.'","'.$downloadlink.'","'.$screenshotlink.'","'.$briefdescription.'","'.'1'.'","'.'0'.'","'.'200'.'"," ","'.$screenshottnlink.'","1" )';

//echo $query;
$result = mysql_query ( $query );

if (!$result )
	echo 'insert failed';

mysql_close ( $db );

//if ( mail("jeff.hill@tpg.com.au", "My Subject", "Line 1\nLine 2\nLine 3") != TRUE )
//echo 'mail failed';
mail ('support@timeweather.net', 'Link submitted: '.$title, $description, 'Submitted by: '.$email.' at '.$_SERVER['REMOTE_ADDR'] );


  echo '<p>';
  echo 'Your request has been submitted.<br>';
  echo 'Your link will be validated shortly, and added to our link database';
  echo '<br>';
  echo $email.'<br>';
  echo $name.'<br>';
  echo $briefdescription.'<br>';
  echo $description.'<br>';
  echo $sitelink.'<br>';
  echo $screenshotlink.'<br>';
  echo $screenshottnlink.'<br>';
  echo $downloadlink.'<br>';
  echo $buylink.'<br>';
  
  echo "<A href='default.php'>Back to main page</a>";
?>
</body>
</html>

