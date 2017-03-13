<?php

//
//	move offsite
//

define ( PRODUCT_IMAGE, "TabView/images/products.gif" );
define ( INFO_IMAGE, "TabView/images/info.gif" );
define ( BUY_IMAGE, "TabView/images/buy.gif" );
define ( DOWNLOAD_IMAGE, "TabView/images/download.gif" );
define ( SCREENSHOT_IMAGE, "TabView/images/search2.gif" );
define ( BUG_IMAGE, "TabView/images/edit.gif" );
define ( FORUM_IMAGE, "TabView/images/forums.gif" );

function DisplayProducts2 ( $count, $thirdparty, $productname )
{
	if ( isset ( $productname ))
	{
		$query = 'SELECT * from product_details where id = "'.$productname.'"';
	}
	else
	{
//	$query = "SELECT * from product_details order by date desc ";
	$query = 'SELECT * from product_details where 3rdparty like '.$thirdparty;
	$query = $query.' order by date desc';
	}

	$result = mysql_query( $query );
	
	$num_rows = mysql_num_rows ( $result );

	if ( $num_rows == 0 )
		return false;

	for ( $i = 0; $i < $num_rows; $i++ )
	{
		if ( $count > 0 )
		{
			if ( $i >= $count )
				return true;
		}
	
		$row = mysql_fetch_object ( $result );
		
		$date = $row->date;
		$date = date ('d F, Y', strtotime($date));

		echo '<table border="0" width="100%" id="table2" bgcolor="#CCCCFF">';
		echo '<tr>';
		echo '<td>';
		//	<img height="16" src="http://jeffs-software.com/TabView/images/products.gif" width="16" border="0">
		//	<b><u>WorldTime2000 </u></b></td>
		//	<td>&nbsp;</td>



		echo '<img border="0" src='.PRODUCT_IMAGE.' width="16" height="16">  ';
//		echo '<b><u>'.$row->name.' - '.$date.'</u></b>';
		echo '<b><u>'.$row->name.'</u></b>';
		
			echo '</tr>';
			echo '</table>';
			
		if ( strlen ( $row->screenshotlink ))
		{
			echo $row->screenshotlink;
		}
		
		echo '<p>'.$row->description.'</p>';

//		echo '<p align=right>';		
		// site link
		if ( strlen ( $row->sitelink ))
		{
			echo '<img border="0" src='.INFO_IMAGE.' width="16" height="16">  '; 
			echo '<a href='.$row->sitelink.'>Read more</a>       ';		
		}
		// buy link
		if ( strlen ( $row->buylink ))
		{
			echo '<img border="0" src='.BUY_IMAGE.' width="16" height="16">  '; 
			echo '<a href='.$row->buylink.'>Purchase</a>       ';	
		}
		// download link
		if ( strlen ( $row->downloadlink ))
		{
			echo '<img border="0" src='.DOWNLOAD_IMAGE.' width="16" height="16">  '; 
			echo '<a href='.$row->downloadlink.'>Download</a>       ';	
		}
//		echo '</p>';
		echo '<hr>';
	
	}	

	return true;
}

function DisplayProducts ( $count, $thirdparty, $productname )
{
	if ( isset ( $productname ))
	{
		$query = 'SELECT * from product_details where id = "'.$productname.'"';
	}
	else
	{
//	$query = "SELECT * from product_details order by date desc ";
	$query = 'SELECT * from product_details where 3rdparty like '.$thirdparty;
	$query = $query.' order by date desc';
	}

//echo $query;
	$result = mysql_query( $query );
	
	$num_rows = mysql_num_rows ( $result );

	if ( $num_rows == 0 )
		return false;

	for ( $i = 0; $i < $num_rows; $i++ )
	{
		if ( $count > 0 )
		{
			if ( $i >= $count )
				return true;
		}
	
		$row = mysql_fetch_object ( $result );
		
		if ( $row->pendingapproval == 1 )
			continue;
		
		$date = $row->date;

		if ( $date == '9999-09-00 00:00:00')
            $date = '2000-01-01 00:00:00';
		$date = date ('d F, Y', strtotime($date));

		echo '<table border="0" width="100%" id="table2" class="heading" >'; //bgcolor="#CCCCFF">';
		echo '<tr>';
		echo '<td>';
		//	<img height="16" src="http://jeffs-software.com/TabView/images/products.gif" width="16" border="0">
		//	<b><u>WorldTime2000 </u></b></td>
		//	<td>&nbsp;</td>



		echo '<img border="0" src='.PRODUCT_IMAGE.' width="16" height="16">  ';
//		echo '<b><u>'.$row->name.' - '.$date.'</u></b>';
		echo $row->name;
		if ( strlen ( $row->requirments ))
			echo '<font class="smalltext">       Requirments:: '.$row->requirments.'</font>  ';
		echo '</tr>';
		echo '</table>';
			
			
		if ( strlen ( $row->screenshotlink ))
		{
//			echo 'test'.strlen ($row->screenshotlink).'aaa';
//			echo '<a href="'.$row->screenshotlink.'">Screenshot</a>';
			echo '<a href="'.$row->screenshotlink.'"><img border="0" align = "right" src="'.$row->screenshottnlink.'" width="'.$row->thumbsize.'"></a>';
			
			
		}
		
		echo '<p>'.$row->description.'</p>';

		echo '<table width=100%><td class="sbright_cell">';
		
//		echo '<p align=right>';		
		// site link
		if ( strlen ( $row->sitelink ))
		{
			echo '<img border="0" src='.INFO_IMAGE.' width="16" height="16">  '; 
			echo '<a href='.$row->sitelink.'>Read more</a>       ';		
		}
		// screenshot link
		if ( strlen ( $row->screenshotlink ))
		{
			echo '<img border="0" src='.SCREENSHOT_IMAGE.' width="16" height="16">  '; 
			echo '<a href="'.$row->screenshotlink.'">Screenshot</a>       ';
		}
		
		// buy link
		if ( strlen ( $row->buylink ))
		{
			echo '<img border="0" src='.BUY_IMAGE.' width="16" height="16">  '; 
			echo '<a href='.$row->buylink.'>Purchase</a>       ';	
		}
		// download link
		if ( strlen ( $row->downloadlink ))
		{
			echo '<img border="0" src='.DOWNLOAD_IMAGE.' width="16" height="16">  '; 
			echo '<a href='.$row->downloadlink.'>Download</a>       ';	
		}
		if ( $row->thirdparty == 0 && $i != 0 )
		{
			echo '<img border="0" src='.BUG_IMAGE.' width="16" height="16">  '; 
			echo '<a href="template.php?TB=contact.php">Request/Report Bug</a>       ';	

			echo '<img border="0" src='.FORUM_IMAGE.' width="16" height="16">  '; 
			echo '<a href="forum/index.php">Discuss</a>       ';	

		}
//		echo '</p>';
		echo '</td></table>';
		echo '<IMG SRC="/images/day_night.gif" HEIGHT=16 WIDTH=100% ALT=" day and night line">';
//		echo '<hr>';
	
	}	

	return true;
}


// main

include ('connect.php');

//$productname = "worldtime2000";
DisplayProducts ( $count, $thirdparty, $product );

mysql_close ( $db );


?>
