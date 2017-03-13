<?php

//define ( PRODUCT, 2 );
//	arg 0 - all products
//	arg > 0 - specific product
//	arg -1 - no products
//define ( COUNT, 0 );
//	arg 0 - no limit
//	arg > 0 - specific count
//	arg -1 - no count
//

function GetProductName ( $whichProduct )
{
	mysql_select_db ( 'products' );
	$query = 'SELECT * FROM products where ID like '.$whichProduct;

	$result = mysql_query($query );

	if ( $result )
	{
		$num_results = mysql_num_rows ( $result );
		if ( $num_results == 1 )
		{
			$row = mysql_fetch_object ( $result );
			$productName = $row->name;
		}
		else
			$productName = 'General';
	}

	return $productName;
}

function DisplayNews ( $whichProduct, $count, $startFrom )
{
//echo PRODUCT;

//	$query = "SELECT DATE_FORMAT(date, '%D %M %Y') as date, productID, description FROM news ";
	$query = "SELECT * from news ";

	if ( $whichProduct != 0 )
		$query = $query."where productID = ".$whichProduct;

	$query = $query." order by date desc "; // limit 2";

	// a count of 0 means get all matching records
//	if ( $count > 0 )
//		$query = $query.' limit '.$count;

	$result = mysql_query( $query );
	
	$num_rows = mysql_num_rows ( $result );

	if ( $num_rows == 0 )
		return false;

	$sqlStart = 0;

	for ( $i = 0; $i < $num_rows; $i++ )
	{
	
		$row = mysql_fetch_object ( $result );
		
		$date = $row->date;
//		echo $date;
//		echo strtotime($date);
//		echo date ('d F, Y', strtotime($date));
//		$date = FORMAT_DATE ( $date, '%D %M %Y');
		$date = date ('d F, Y', strtotime($date));
		$description = $row->description;
		$title = $row->title;

		
		if ( $whichProduct == 0 || $whichProduct == $row->productID )
		{
//		echo 'salstart'.$sqlStart.'startfrom'.$startFrom.'count'.$count;\\ $count = 0 )
			if (( $sqlStart >= $startFrom && $sqlStart < $startFrom + $count ) || $count == 0 )
			{
				echo '<img border="0" src="TabView/images/info.gif" width="16" height="16">  ';
				
				echo ' <font class="heading"><b>'.$title.'</b></font><br>';
				echo '<font class="smalltext">Submitted on: '.$date.'</font>  ';
				
				if ( $whichProduct == 0 )
				{
					$productName = GetProductName ( $row->productID );
					echo ' <font class="smalltext">('.$productName.')</font>';
				}
				//echo '</b>';	
				echo '<p>'.$description.'</p>';
//				echo '<IMG SRC="/images/day_night2.gif" HEIGHT=16 WIDTH=100% ALT=" day and night line">';
				echo '<hr>';
			
			}	
			
			$sqlStart++;
		}
		
	}

	return true;

}


// main

include ('connect.php');
/*
@ $db = mysql_pconnect('localhost', 'webuser', 'goose1604');

if (!$db)
{
	echo 'Error: Could not connect to database.';
	exit;
}

mysql_select_db ( 'news' );
*/
/*
echo '<b>';
echo '<a href="template.php?TB=news.php?product='.$product.'?count='.$count.'?start=0">Top of News</a>';
echo '  |  ';
$start= $start + 5;
echo '<a href="template.php?TB=news.php?product='.$product.'?count='.$count.'?start='.$start.'">More News</a>';
echo '</b><br><br>';
*/
DisplayNews ( $product, $count, $start );
//DisplayNews ( 0, 0 ); //PRODUCT, COUNT );

mysql_close ( $db );

if ($count > 0 )
{
echo '<b>';
echo '<a href="template.php?TB=news.php?product='.$product.'?count='.$count.'?start=0">Top of News</a>';
echo '  |  ';
$start= $start + 5;
echo '<a href="template.php?TB=news.php?product='.$product.'?count='.$count.'?start='.$start.'">More News</a>';
echo '</b>';
}
?>