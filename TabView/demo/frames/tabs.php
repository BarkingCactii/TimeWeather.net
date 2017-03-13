<?php
/******************************
	
	Filename: tabs.php
	Created: December 12, 2002 
	Author: Brad Touesnard
	Copyright: Copyright © 2002 Zenutech.com

	Last Modified: 
	Last Modified By: 

 ******************************/
require("../../tab_view.php");
?>
<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>TabView Frames Demo</title>

<link rel="stylesheet" href="../../stylesheet.php" type="text/css">

</head>

<body bgcolor="#FFFFFF" text="#000000" link="#FF0000" alink="#FF0000" vlink="#FFCCCC" marginheight="4" topmargin="4" marginwidth="0" leftmargin="0">

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>
		<?php
		$TabView = new TabView();

		$Tab =& $TabView->Add("1","Frame 1","frame.php","main_frame","");
		$Tab->Image = "../../images/edit.gif";
		$Tab->TabWidth = 100;

		$Tab =& $TabView->Add("2","Frame 2","frame.php","main_frame","");
		$Tab->Image = "../../images/book.gif";
		$Tab->TabWidth = 100;

		$Tab =& $TabView->Add("3","Frame 3","frame.php","main_frame","");
		$Tab->Image = "../../images/icon_fav.gif";
		$Tab->TabWidth = 100;

		$TabView->ImagePath = "../../images/blue";
		$TabView->DHTML = "onClick=\"javascript:document.location.href='?TB=#TAB#'\"";
		$TabView->StartTab = "1";
		$TabView->Class = "tabviewDemo";
		//$TabView->Orientation = 1;

		$TabView->Show();
		?>

	</td>
</tr>
<tr>
	<td bgcolor="CCCCFF"><img src="../../../../../../images/spacer.gif" width="1" height="2"></td>
</tr>
</table>
<br>
