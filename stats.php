<html>

<head>
<meta http-equiv="Content-Language" content="en-us">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<META HTTP-EQUIV="Refresh" CONTENT="60; URL=http://timeweather.net/stats.php">

<title>Please wait</title>
</head>


<body>
<?php



include ('connect.php');

echo '<table border="0" width="100%">';
echo '<font size="1">';
ReadCounter();
echo '</font>';
echo '</table>';
//for ( $i = 0; $i < 100000;$i++ )
//	$i = $i;


mysql_close ( $db );

sleep(3);
//echo $_SERVER['PHP_SELF'];
//header("Location: $_SERVER['PHP_SELF']");

//header("Location: http://jeffs-software.com/stats.php");
//sleep(5);
//exit();

function ReadCounter (  )
{
	$query = 'SELECT * FROM visitlog order by date desc';
//echo $query.$url;

	$result = mysql_query($query );


	if ( !$result )
		return false;

	$numRows =  mysql_num_rows ( $result );
	if ( $numRows == 0 )
		return false;

	echo '<h2>Last 10 visits</h2>';
	echo '<tr bgcolor="#f07070">';
		echo '<td>'.'date'.'</td>';
		echo '<td>'.'pageID'.'</td>';
		echo '<td>'.'ip'.'</td>';
		echo '<td>'.'referer'.'</td>';
		echo '<td>'.'domainname'.'</td>';
	echo '</tr>';

	for ( $i = 0; $i < 50; $i++ )
	{
		echo '<tr';
		if ( $i % 2 == 0 )
			echo ' bgcolor="#d0d0d0">';
		else
			echo '>';

		$row = mysql_fetch_object ( $result );
		echo '<td>'.$row->date.'</td>';
		echo '<td>'.$row->pageID.'</td>';
		echo '<td>'.$row->ip.'</td>';
		echo '<td>'.$row->referer.'</td>';
		echo '<td>'.$row->domainname.'</td>';
		echo '</tr>';
	}
	return true;
//	
}


?>

</body>
</html>
