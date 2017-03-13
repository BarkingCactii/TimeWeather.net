<?php

function ReadCounter ( &$hits, &$pageID, $url )
{
	$query = 'SELECT * FROM webpages where name like "'.$url.'"';
//echo $query.$url;

	$result = mysql_query($query );


	if ( !$result )
		return false;

	$numRows =  mysql_num_rows ( $result );
	if ( $numRows == 0 )
		return false;

//	$row = mysql_fetch_array ( $result );
//	$row = mysql_result ( $result, 0, 'pageID' );
	$row = mysql_fetch_object ( $result );
	$pageID = $row->pageID;

	// have page ID, now go to hitcounters table and retrieve the count
	$query = 'SELECT * FROM hitcounters where pageID like '.$row->pageID;

	$result = mysql_query($query );

	if ( !$result )
		return false;

	$num_results = mysql_num_rows ( $result );

	if ( $num_results == 1 )
		$row = mysql_fetch_object ( $result );
	else
		return false;

	$hits = $row->hits;
	return true;
}

function IncrementCounter ( $hits, $pageID )
{
	$hits = $hits + 1;		
	$query = 'UPDATE hitcounters SET hits='.$hits.' WHERE pageID='.$pageID;
	mysql_query ( $query );


	
        system ("c:\php\beep.exe 500 150 >nul");
//        systen ("dir");
}

function getIP() 
{
	$ip;
	if (getenv("HTTP_CLIENT_IP")) $ip = getenv("HTTP_CLIENT_IP");
	else if(getenv("HTTP_X_FORWARDED_FOR")) $ip = getenv("HTTP_X_FORWARDED_FOR");
	else if(getenv("REMOTE_ADDR")) $ip = getenv("REMOTE_ADDR");
	else $ip = "UNKNOWN";
	return $ip;
} 

// main


//$url = $HTTP_SERVER_VARS['URL'];

//
//	get the name of the body php, not template.php
//

	
if ( $_SERVER['SCRIPT_NAME'] == "/default.php" )
{
	$url = 'default.php';
}
else
{
	$temp = $_SERVER['argv'];
	$temp = split ( '=', $temp[0] );
	$url = $temp[1];
	$url = $body;
//	echo $body;
}

@ $db = mysql_pconnect('localhost', 'webuser', 'goose1604');


if (!$db)
{
	echo 'Error: Could not connect to database.';
	exit;
}

mysql_select_db ( 'web' );

$hits = 0;
$pageID = 0;


if ( ReadCounter ( $hits, $pageID, $url ))
{

	IncrementCounter ( $hits, $pageID );


//$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
//echo $hostname;

	echo '<p align="center"><font size = \'1\' color = #000000>Page Hits: '.$hits; //'<br>Source: '.$_SERVER['REMOTE_ADDR'];

	echo " Last modified: ". date ("F d Y H:i:s.", getlastmod());
	echo '</font></p>';

//	echo getIP();
//	echo gethostbyaddr( $_SERVER['REMOTE_ADDR'] );
}


// Save referrer information into database, except for the webserver
if ( $_SERVER['REMOTE_ADDR'] != '192.168.1.9' && $_SERVER['REMOTE_ADDR'] != '192.168.1.2')
{
	$query = "insert into visitlog values ('".$pageID."','".$_SERVER['REMOTE_ADDR']."','".$_SERVER['HTTP_REFERER']."','".date('Y-m-d').' '.date('H:i:00')."','".gethostbyaddr( $_SERVER['REMOTE_ADDR'] )."' )";
	$result = mysql_query ( $query );
	if (!$result )
		echo 'Logging failed';
}


mysql_close ( $db );



/*
  mysql_select_db('books');
  $query = "insert into books values 
            ('".$isbn."', '".$author."', '".$title."', '".$price."')"; 
  $result = mysql_query($query);
  if ($result)
	echo  mysql_affected_rows().' book inserted into database.'; 



@ $fp = fopen ( $counterFile, 'r' );

//echo $counterFile;
if ( !$fp )
{
	// file doesnt exist
	// create it with a default start number
	IncrementCounterFile ( $counterFile, "0" );
}
else
{
	// file exists
	// increment counter
	$counter = fgets ( $fp, 99 );
	fclose ( $fp );

	IncrementCounterFile ( $counterFile, $counter );
}

echo "Last modified: ";// . date ("F d Y H:i:s.", getlastmod());
//echo '<p><font size = \'1\' color = #000000>Page Hits: '.$counter.'<br>Source: '.$_SERVER['REMOTE_ADDR'].'</font></p>';


// Save the referrer for reporting
//
$refererFile = '../Counters'.$HTTP_SERVER_VARS['URL'].'.referer';

//echo $fileURL;

@ $fp = fopen ( $refererFile, 'a' );
if ( $fp )
{
	fwrite ( $fp, date('Y/m/d H:i ').$_SERVER['REMOTE_ADDR'].' ['.$_SERVER['HTTP_REFERER']."]\n");
	fclose ( $fp );
}
*/
//echo '['.$_SERVER["HTTP_REFERER"].']';

?>
