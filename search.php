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

function SearchProducts ( $str )
{
	$keywords = split ( ' ', $str );
	$numKeywords = count ($keywords);

	$query = 'SELECT * from product_details where ';

	for ( $i = 0; $i < $numKeywords; $i++ )
	{
		if ( $i == 0 )
			$query = $query.' description like "%'.$keywords[$i].'%" ';
		else
			$query = $query.' or description like "%'.$keywords[$i].'%" ';
	}

	$query = $query." order by date desc "; // limit 2";


	$result = mysql_query( $query );
	$num_rows = mysql_num_rows ( $result );

	if ( $num_rows == 0 )
		return false;

	echo '<p class="heading">Products<hr></p>';	
	for ( $i = 0; $i < $num_rows; $i++ )
	{
	
		$row = mysql_fetch_object ( $result );

		$date = $row->date;
		$date = date ('d F, Y', strtotime($date));


				echo '<img border="0" src="TabView/images/products.gif" width="16" height="16">  ';
				
				echo ' <font class="heading"><b>'.$row->name.'</b></font><br>';
				//echo '<font class="smalltext">Submitted on: '.$date.'</font>  ';

//					$productName = GetProductName ( $row->productID );
//					echo ' <font class="smalltext">('.$productName.')</font>';

				//echo '</b>';	
				echo '<p>'.$row->description.'</p>';
				echo '<hr>';
/*
		echo '<img border="0" src="TabView/images/products.gif" width="16" height="16">  ';
		echo '
		echo '<b>'.$date;

		echo '</b>';	

		echo '<p>'.$description.'</p>';
		*/
	}

	return true;

}


function SearchNews ( $str )
{
//echo PRODUCT;
$keywords = split ( ' ', $str );
$numKeywords = count ($keywords);
//echo 'keywords='.$numKeywords;


//	$query = "SELECT DATE_FORMAT(date, '%D %M %Y') as date, productID, description FROM news ";
//	$query = 'SELECT * from news where description like "%'.$str.'%"';
	$query = 'SELECT * from news where ';

	for ( $i = 0; $i < $numKeywords; $i++ )
	{
		if ( $i == 0 )
			$query = $query.' description like "%'.$keywords[$i].'%" ';
		else
			$query = $query.' or description like "%'.$keywords[$i].'%" ';
//		echo $keywords[$i];
	}

	$query = $query." order by date desc "; // limit 2";

//$query = "select * from news";
	// a count of 0 means get all matching records
//	if ( $count > 0 )
//		$query = $query.' limit '.$count;

//echo $query;
	$result = mysql_query( $query );
//echo $result;	
	$num_rows = mysql_num_rows ( $result );

//echo $num_rows;
	if ( $num_rows == 0 )
		return false;

	echo '<p class="heading">News<hr></p>';	
	for ( $i = 0; $i < $num_rows; $i++ )
	{
	
		$row = mysql_fetch_object ( $result );

		$date = $row->date;
		$date = date ('d F, Y', strtotime($date));


				echo '<img border="0" src="TabView/images/info.gif" width="16" height="16">  ';
				
				echo ' <font class="heading"><b>'.$row->title.'</b></font><br>';
				echo '<font class="smalltext">Submitted on: '.$date.'</font>  ';

					$productName = GetProductName ( $row->productID );
					echo ' <font class="smalltext">('.$productName.')</font>';

				//echo '</b>';	
				echo '<p>'.$row->description.'</p>';
				echo '<hr>';




/*
		$date = $row->date;
//		echo $date;
//		echo strtotime($date);
//		echo date ('d F, Y', strtotime($date));
//		$date = FORMAT_DATE ( $date, '%D %M %Y');
		$date = date ('d F, Y', strtotime($date));
		$description = $row->description;

		echo '<img border="0" src="TabView/images/info.gif" width="16" height="16">  ';
		echo '<b>'.$date;
//if ( $whichProduct == 0 )
//		{
//			$productName = GetProductName ( $row->productID );
//			echo ' <font size="1">( '.$productName.' )</font>';
//		}
		echo '</b>';	

		echo '<p>'.$description.'</p>';
		//		echo '<p>'.$row->description.'</p>';
		*/
	}

	return true;

}


	// main

    //create short variable names
	$search = $HTTP_POST_VARS['search'];
 
	$DOCUMENT_ROOT = $HTTP_SERVER_VARS['DOCUMENT_ROOT'];


	include ('connect.php' );

	SearchNews ( $search );
	SearchProducts ( $search );

	mysql_close ( $db );

?>