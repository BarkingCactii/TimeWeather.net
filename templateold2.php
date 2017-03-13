<html>

<head>
<meta name="GENERATOR" content="Microsoft FrontPage 6.0">
<meta name="ProgId" content="FrontPage.Editor.Document">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>World Time Shareware Applications - Jeffs-Software.com</title>
<meta NAME="Classification" CONTENT="world time software, time zone software,times,zones,time zones,time zone,world time zones,international time zones,display time zones,time zone map,different time zones,world map,travel time zones,free updates,shareware,software,freeware,daylight savings,alarm clock,desktop,32-bit,Windows 95,Windows 98,Windows NT,Windows 2000,Windows XP">
<meta NAME="description" CONTENT="Shareware WorldTime2000. WhenOnEarth, WorldTime2003 View different world time zones from around the world on a map">
<meta NAME="keywords" CONTENT="world time software,times,zones,time zones,time zone,world time zones,international time zones,display time zones,time zone map,different time zones,travel time zones,world map,free updates,shareware,software,freeware,daylight savings,alarm clock,desktop">
<link rel="stylesheet" type="text/css" href="stylesheet.CSS">
</head>


<body bgcolor="#ffffff" topmargin="0">



<table border="0" cellpadding="0" style="border-collapse: collapse" bordercolor="#111111" width="880" height="61">
  <tr>
    <td width="370" background="http://users.tpg.com.au/adsld842/images/logo2.gif" height="61">&nbsp;
      
    
    </td>
    
    <td height="61" valign="top">
    
    
	<?php
	include ('search_site.php');	
	?>
	

<h4 align="left"><b><i><font size="2">World Time Shareware &amp; Custom Software Development 
<a href="http://www.jeffs-computer/xoop/html/index.php">Services</a> </font></i></b>
	</h4>
	
	</td>
	</tr>
	</table>



<table width="98%" cellpadding="0" cellspacing="0">

<tr>
<td>


<?php

include ("tabview/tab_view.php" );

$TabView = new TabView;

$TabView->ImagePath = "tabview/images/blue";
$TabView->BackColor = "#EEEEFF";
$TabView->SelectedBackColor = "#CCCCFF";  




$Tab =& $TabView->Add("1", "Home", "default.php", "", "Jump to the Home page");
//$Tab->Image = "tabview/images/house.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("news.php?product=0?count=5", "News", "template.php", "", "Jump to news");
//$Tab->Image = "tabview/images/news.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("products.php?thirdparty=0?count=99", "Products", "template.php", "", "View our products");
$Tab->Image = "tabview/images/products.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("download.php", "Download", "template.php", "", "Download our product trials");
//$Tab->Image = "tabview/images/download.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("buy.php", "Buy", "template.php", "", "Buy  a product");
$Tab->Image = "tabview/images/buy.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("6", "Forums", "bb/index.php", "", "Discussion Forum");
$Tab->ForeColor = "#4040d0";
$Tab->Image = "tabview/images/forums.gif";
$Tab =& $TabView->Add("sitemap.php", "Sitemap", "template.php", "", "View Sitemap");
$Tab->Image = "tabview/images/sitemap.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("about.php", "About Us", "template.php", "", "About Us");
$Tab->Image = "tabview/images/world.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("contact.php", "Contact", "template.php", "", "Contact Us");
$Tab->Image = "tabview/images/email.gif";
$TabView->Show(); 





	//
	// parse arguments in the url
	//
		
	// get command line
	$cmdLine = $_SERVER['argv'][0];
	// get an array of arguments
	$args = split ( '\?', $cmdLine );
	// get parameters and values

	// parse each argument to see what the parameter is
	//	
	for ( $i = 0; isset ($args[$i]); $i++ )
	{
		$parameters = split ( '=', $args[$i] );
		$arg = $parameters[0];
		$value = $parameters[1];

		if ( $arg == 'TB' )
			$body = $value;		
		if ( $arg == 'product' )
			$product = $value;
		if ( $arg == 'count' )
			$count = $value;
		if ( $arg == 'start' )
			$start = $value;			
		if ( $arg == 'thirdparty' )
			$thirdparty = $value;

	}	 




?>
</td>
</tr>
<tr>
<td>
	<table width="100%" border="1" bordercolor="<?=$TabView->SelectedBackColor?>" cellpadding="8" cellspacing="0">		
	<tr>
	<td width="170" valign="top">
		<table border="1" width="100%" id="table1" bordercolordark="#000000" bordercolorlight="#CCFFFF" style="border-collapse: collapse">
			<tr>
				<td colspan="3" bgcolor="#FFFFFF" bordercolor="#0000FF">
				<p align="center"><b>Quick links</b></td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<img border="0" src="TabView/images/edit.gif" width="16" height="16"></td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<a href="template.php?TB=awards.php">Awards</a></td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<img border="0" src="TabView/images/products.gif" width="16" height="16"></td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">Our Products</td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<img border="0" src="TabView/images/box.gif" width="11" height="11">
				<a href="template.php?TB=worldtime2000_body.php">WorldTime2000</a></td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<img border="0" src="TabView/images/box.gif" width="11" height="11"> 
				<a href="template.php?TB=whenonearth_body.php">WhenOnEarth</a></td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<img border="0" src="TabView/images/box.gif" width="11" height="11"> 
				<a href="template.php?TB=worldtime2003_body.php">WorldTime2003</a></td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<img border="0" src="TabView/images/download.gif" width="16" height="16">
				<a href="template.php?TB=download.php">Download</a></td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<img border="0" src="TabView/images/buy.gif" width="15" height="15">
				<a href="template.php?TB=buy.php">Buy</a></td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<img border="0" src="TabView/images/products.gif" width="16" height="16"></td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<a href="template.php?TB=products.php?thirdparty=1?count=99">Other Products</a></td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				&nbsp;</td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<img border="0" src="TabView/images/sitemap.gif" width="16" height="16"></td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<a href="template.php?TB=sitemap.php">Site map</a></td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<img border="0" src="TabView/images/email.gif" width="16" height="10"></td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">
				<a href="template.php?TB=contact.php">Contact</a></td>
			</tr>
			<tr>
				<td width="5%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="14%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
				<td width="77%" bgcolor="#CCCCFF" bordercolor="#CCCCFF">&nbsp;</td>
			</tr>

		</table>
		
		
		<?php
		    echo '<p>';
	include ('comments.php');		    
	
//	include ('weather/index.php');
	include ('poll/poll.php');
		include ( 'counter.php' );
		echo '</p>';
    ?>
    
	</td>
		
	<td width="836" align="left" valign="top">
	
	<?php
	
	/*
	//	echo '1'.$_GET['TB'].'1';
//	if ( isset ( $BODY ))
		include ( BODY );
//	else
	if ( isset ( $_GET['TB'] ))
	{
		//	echo 'name'.$scriptName;
		
//		include ( $_GET['TB'] );
		
		
//		echo $_GET['TB'];
//		echo $_GET['product'];
//		echo $_GET['count'];


	$temp = $_SERVER['argv'];
//	echo $temp[0].'.'.$temp[1];
	$args = split ( '\?', $temp[0] );
	
	$body = split ('=', $args[0] );
//	echo $params[1];
		$product = split ('=', $args[1] );
//	echo $params[1];
		$count = split ('=', $args[2] );
//	echo $params[1];
	
//		$arg2 = split ( '?', $temp[1] );
//			$arg3 = split ( '?', $temp[2] );
//	echo $temp[1];
	
//	echo $args[0].'|'.$args[1].'|'.$args[2];
		
		//echo $body[1];
		include ( $body[1] );
	}
	*/
	
	
	// only for main page
	//
	
//	$count = 3;
	include ( BODY );
	

/*		
	// parse arguments in the url
	//
		
	// get command line
	$cmdLine = $_SERVER['argv'][0];
	// get an array of arguments
	$args = split ( '\?', $cmdLine );
	// get parameters and values

	// parse each argument to see what the parameter is
	//	
	for ( $i = 0; isset ($args[$i]); $i++ )
	{
		$parameters = split ( '=', $args[$i] );
		$arg = $parameters[0];
		$value = $parameters[1];

		if ( $arg == 'TB' )
			$body = $value;		
		if ( $arg == 'product' )
			$product = $value;
		if ( $arg == 'count' )
			$count = $value;
		if ( $arg == 'start' )
			$start = $value;			
	}	 
	*/
	/*
	$bodys = split ('=', $args[0] );
//	echo $bodys[0].$bodys[1];
	$products = split ('=', $args[1] );
	$counts = split ('=', $args[2] );

	$body = $bodys[1];
	$product = $products[1];
	$count = $counts[1];
	
	
*/

	//echo 'body='.$body;
	include ( $body );

	
	?>
		</td>
	</table>
</td>
</tr>
</table>

</body>

</html>
