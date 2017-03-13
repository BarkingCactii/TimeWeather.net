<html>

<head>
<meta name="GENERATOR" content="Microsoft FrontPage 6.0">
<meta name="ProgId" content="FrontPage.Editor.Document">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<title>World Time Shareware Applications - TimeWeather.NET</title>
<meta NAME="Classification" CONTENT="world time software, time zone software,times,zones,time zones,time zone,world time zones,international time zones,display time zones,time zone map,different time zones,world map,travel time zones,free updates,shareware,software,freeware,daylight savings,alarm clock,desktop,32-bit,Windows 95,Windows 98,Windows NT,Windows 2000,Windows XP">
<meta NAME="description" CONTENT="Shareware WorldTime2000. WhenOnEarth, WorldTime2003 View different world time zones from around the world on a map">
<meta NAME="keywords" CONTENT="world time software,times,zones,time zones,time zone,world time zones,international time zones,display time zones,time zone map,different time zones,travel time zones,world map,free updates,shareware,software,freeware,daylight savings,alarm clock,desktop">
<link rel="stylesheet" type="text/css" href="stylesheet.CSS">
<style type="text/css">
<!--
.style1 {color: #000066}
-->
</style>
</head>


<body bgcolor="#ffffff" topmargin="0">



<table width="98%" cellpadding="0" cellspacing="0" class="tbright_cell">
<tr>
<td valign="top" bgcolor="#EEEEFF" class="heading">
<img border="0" src="images/timeweatherlogo.gif" width="100"
 height="100"><span class="style1"> TimeWeather.net </span></td>
<td bgcolor="#EEEEFF">

<?php
include ('search_site.php');
?>
</td>
</tr>
</table> 

<img border="0" src="images/line14.gif" width="100%" height="25">

<table width="98%" cellpadding="0" cellspacing="0">

<tr>
<td>

<p class="heading">
<i>
World Time Shareware and Custom Software Development Services</i>
<br>
</p> 
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
//$Tab->Image = "tabview/images/products.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("download.php", "Download", "template.php", "", "Download our product trials");
//$Tab->Image = "tabview/images/download.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("buy.php", "Buy", "template.php", "", "Buy  a product");
//$Tab->Image = "tabview/images/buy.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("6", "Forums", "forum/index.php", "", "Discussion Forum");
$Tab->ForeColor = "#4040d0";
//$Tab->Image = "tabview/images/forums.gif";
$Tab =& $TabView->Add("sitemap.php", "Sitemap", "template.php", "", "View Sitemap");
//$Tab->Image = "tabview/images/sitemap.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("about.php", "About Us", "template.php", "", "About Us");
//$Tab->Image = "tabview/images/world.gif";
$Tab->ForeColor = "#4040d0";
$Tab =& $TabView->Add("contact.php", "Contact", "template.php", "", "Contact Us");
//$Tab->Image = "tabview/images/email.gif";
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

	<table width="100%" border="1" cellpadding="8" cellspacing="0" bordercolorlight="#C0C0C0" bordercolordark="#C0C0C0">		
	<tr>
	<td width="170" valign="top" class="heading">
		<table border="0" width="100%" bordercolordark="#000000" bordercolorlight="#C0C0C0" style="border-collapse: collapse" background="images/sidebar.jpg" cellpadding="0" class="sidebar_tab">
			<tr class="sidebar_cell">
				<td colspan="2" bgcolor="#FFFFFF" bordercolor="#0000FF" align="right" style="border-style: solid; border-width: 1px">
				<p align="center"><font size="3">Quick links</font></td>
			</tr>
			<tr class="sidebar_cell">
				<td width="5%" >&nbsp;</td>
				<td class="sidebar_cell" width="77%" class="sidebar">
				<font size="4">
				<a href="template.php?TB=news.php?product=0?count=5">
				News</a></font></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar">&nbsp;</td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar">
				<font size="4">Our Products</font></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar">
				<img border="0" src="images/graydia_sm.gif" width="9" height="9"> <a href="template.php?TB=worldtime2000_body.php">WorldTime2000</a></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar">
				<img border="0" src="images/graydia_sm.gif" width="9" height="9"> <a href="template.php?TB=whenonearth_body.php">WhenOnEarth</a></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar">
				<img border="0" src="images/graydia_sm.gif" width="9" height="9"> <a href="template.php?TB=worldtime2003_body.php">WorldTime2003</a></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar">&nbsp;
				</td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar">
				<font size="4">Support</font></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar_cell">
				<img border="0" src="images/graydia_sm.gif" width="9" height="9"> <a href="forum/index.php">Forums</a></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%">&nbsp;
				</td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar_cell">
				<font size="4">Download</font><br>
				<img border="0" src="images/graydia_sm.gif" width="9" height="9"> <a href="template.php?TB=download.php">Free software</a></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%">&nbsp;
				</td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar">
				<font size="4">Buy</font><br>
				<img border="0" src="images/graydia_sm.gif" width="9" height="9"> <a href="template.php?TB=buy.php">Our products</a><br>
&nbsp;</td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%">
				<a href="template.php?TB=products.php?thirdparty=1?count=99">
				<font size="4">Other Products</font></a></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%">&nbsp;
				</td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sidebar_cell">
				<a href="template.php?TB=link_form.php"><font size="4">Link to us</font></a></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%">&nbsp;
				</td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%" class="sb_tab">
				<font size="4">Contact</font><br>
				<img border="0" src="images/graydia_sm.gif" width="9" height="9"> <a href="template.php?TB=contact.php">TimeWeather.NET</a></td>
			</tr>
			<tr>
				<td width="5%">&nbsp;</td>
				<td width="77%">&nbsp;</td>
			</tr>

		</table>
		
		
		<?php
		    echo '<p>';
   	echo '<hr>';
	include ('comments.php');		    
	
//	include ('weather/index.php');
//	include ('poll/poll.php');
	echo '<hr>';
	include ( 'counter.php' );
	
	
	echo '</p>';
    ?>
    
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<p align="center">
<input type="image" src="https://www.paypal.com/en_US/i/btn/x-click-but04.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----MIIG9QYJKoZIhvcNAQcEoIIG5jCCBuICAQExggEwMIIBLAIBADCBlDCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20CAQAwDQYJKoZIhvcNAQEBBQAEgYCRVwSd/Jjts8G295/xGOXaLPZcs2zDuQuGw0V6A6mDMSq2hXUiDShkGGpDjmIH0vyg8mwbKCXyNmrqiveUZVuo5MYzmjh+NEuUa6pGS7AfnbRBI8cYoz+xl8gLUR7BPB6vbVhv4zsNivTLTRtPMcawxHDA7uKbColn6V0Aq7aGMzELMAkGBSsOAwIaBQAwcwYJKoZIhvcNAQcBMBQGCCqGSIb3DQMHBAhA9TDnkVS6DYBQOB6LcLRG8iVu6zznudE1zIef38giVg/wQhF1m+Knr7MK6DysQzsdsYEJ2eRP5PrZQmI8s5b6BDlxJADBJJ9j/JbXgeBHDXOyWRglj0zY3PqgggOHMIIDgzCCAuygAwIBAgIBADANBgkqhkiG9w0BAQUFADCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wHhcNMDQwMjEzMTAxMzE1WhcNMzUwMjEzMTAxMzE1WjCBjjELMAkGA1UEBhMCVVMxCzAJBgNVBAgTAkNBMRYwFAYDVQQHEw1Nb3VudGFpbiBWaWV3MRQwEgYDVQQKEwtQYXlQYWwgSW5jLjETMBEGA1UECxQKbGl2ZV9jZXJ0czERMA8GA1UEAxQIbGl2ZV9hcGkxHDAaBgkqhkiG9w0BCQEWDXJlQHBheXBhbC5jb20wgZ8wDQYJKoZIhvcNAQEBBQADgY0AMIGJAoGBAMFHTt38RMxLXJyO2SmS+Ndl72T7oKJ4u4uw+6awntALWh03PewmIJuzbALScsTS4sZoS1fKciBGoh11gIfHzylvkdNe/hJl66/RGqrj5rFb08sAABNTzDTiqqNpJeBsYs/c2aiGozptX2RlnBktH+SUNpAajW724Nv2Wvhif6sFAgMBAAGjge4wgeswHQYDVR0OBBYEFJaffLvGbxe9WT9S1wob7BDWZJRrMIG7BgNVHSMEgbMwgbCAFJaffLvGbxe9WT9S1wob7BDWZJRroYGUpIGRMIGOMQswCQYDVQQGEwJVUzELMAkGA1UECBMCQ0ExFjAUBgNVBAcTDU1vdW50YWluIFZpZXcxFDASBgNVBAoTC1BheVBhbCBJbmMuMRMwEQYDVQQLFApsaXZlX2NlcnRzMREwDwYDVQQDFAhsaXZlX2FwaTEcMBoGCSqGSIb3DQEJARYNcmVAcGF5cGFsLmNvbYIBADAMBgNVHRMEBTADAQH/MA0GCSqGSIb3DQEBBQUAA4GBAIFfOlaagFrl71+jq6OKidbWFSE+Q4FqROvdgIONth+8kSK//Y/4ihuE4Ymvzn5ceE3S/iBSQQMjyvb+s2TWbQYDwcp129OPIbD9epdr4tJOUNiSojw7BHwYRiPh58S1xGlFgHFXwrEBb3dgNbMUa+u4qectsMAXpVHnD9wIyfmHMYIBmjCCAZYCAQEwgZQwgY4xCzAJBgNVBAYTAlVTMQswCQYDVQQIEwJDQTEWMBQGA1UEBxMNTW91bnRhaW4gVmlldzEUMBIGA1UEChMLUGF5UGFsIEluYy4xEzARBgNVBAsUCmxpdmVfY2VydHMxETAPBgNVBAMUCGxpdmVfYXBpMRwwGgYJKoZIhvcNAQkBFg1yZUBwYXlwYWwuY29tAgEAMAkGBSsOAwIaBQCgXTAYBgkqhkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJBTEPFw0wNTA0MjgxMTQ3MDJaMCMGCSqGSIb3DQEJBDEWBBQpRlNp5E+HPVsxmW5XyhRnSNtryzANBgkqhkiG9w0BAQEFAASBgFgF5QR1FEpl7IhF5sFxlmpZuA7zRvJPJZuH+xTd0krE/olJBhf2EDe/t+Pqj+DeZH6qZ9EImYPQZcw2AQRo16Hq8+YAOa3nokrvilRz0FlVNo2A0h8Zip6icJow4EP6u94Oog/+X58eTr+L7bSPMNfym0vf+f0lPUl6Knwi3+0j-----END PKCS7-----
">
</p>
</form> 
    
    
	</td>
		
	<td width="836" align="left" valign="top">
	
	<?php
	// only for main page
	//
	include ( BODY );

//	require_once ( '/bb/index.php' );
//    if ( defined ($body))
	include ( $body );

	
	?>
	</td>
	
	<?php
	if ( defined ( "BODY" ))
//	if ( strlen ($body) > 0 )
	{
		echo '</td>';
	echo '<td valign="top" class="sbright_cell">'; //bgcolor="#eeeeff">';
	echo '<font class="heading">Make a vote</font><br>';
		include ('poll/poll.php');
		echo '<hr>';
	$product=0;
	$count=2;
	echo '<font class="heading"><img border="0" src="images/new_small.gif">Latest News</font><hr>';
	include ('news.php' );

	echo '</td>';
	}
	?>
	
	</table>
</td>
</tr>
</table>

<p align= center>© TimeWeather.NET</p>


</body>

</html>
