<?php
/******************************
	
	Filename: index.php
	Created: December 12, 2002 
	Author: Brad Touesnard
	Copyright: Copyright © 2002 Zenutech.com

	Last Modified: 
	Last Modified By: 

 ******************************/
require("../tab_view.php");
?>
<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
<title>TabView Demo</title>

<link rel="stylesheet" href="../stylesheet.php" type="text/css">

</head>

<body bgcolor="#FFFFFF" text="#000000" link="#FF0000" alink="#FF0000" vlink="#FFCCCC" onLoad="javascript: window.self.focus();">

<table width="98%" cellpadding="0" cellspacing="0">
<tr>
	<td><img src="../../../../../images/spacer.gif" width="1" height="4"></td>
</tr>
<tr>
	<td>

		<?php
		$TabView = new TabView();

		$Tab =& $TabView->Add("1","Welcome","","","Welcome to the TabView Demo.");
		$Tab->Image = "../images/icon_fav.gif";

		$Tab =& $TabView->Add("2","Forms","","","Using forms with TabView.");
		$Tab->Image = "../images/edit.gif";

		$Tab =& $TabView->Add("3","Frames","","","Using frames with TabView.");
		$Tab->Image = "../images/book.gif";

		$Tab =& $TabView->Add("4","DHTML","","","This tab is DHTML enabled.");
		$Tab->Image = "../images/search.gif";
		$Tab->TabWidth = 100;	
		$Tab->ForceDHTML = true;
		$Tab->DHTML = "href=\"javascript: alert('Current Page: \\'#PAGE#\\'\\nCurrent Querystring: \\'#QUERYSTRING#\\'\\nThis tab #: \\'#TAB#\\'')\"";

		if ($_GET['color'] == "red") {
			$TabView->ImagePath = "../images/red";
			$TabView->QueryString .= "color=red&";
			$TabView->BackColor = "#FF6666";
			$TabView->SelectedBackColor = "#FF0000";
		}
		else {
			$TabView->ImagePath = "../images/blue";
		}
			
		$TabView->StartTab = "1";
		$TabView->Class = "tabviewDemo";
		if ($_GET['right'] == 1) {
			$TabView->Orientation = 1;
			$TabView->QueryString .= "right=1";
		}

		$TabView->Show();
		?>

	</td>
</tr>
<tr>
	<td>
		<table width="100%" border="1" bordercolor="<?=$TabView->SelectedBackColor?>" cellpadding="8" cellspacing="0">		
		<tr>
			<td bgcolor="FAFAFA">
				<?php if ($TabView->TB == "2") { ?>
				
				<h4>Forms Demo</h4>
				<p>One of the most useful applications of TabView is to implement an incremental 
				form submissions. That is, instead of using one large form on a single page, TabView 
				allows you to split the form up between several pages.</p>
				<p><a href="forms/index.php">Click here to launch the forms demo.</a></p>
				
				<?php } elseif ($TabView->TB == "3") { ?>
				
				<h4>Frames Demo</h4>
				<p>TabView can be very useful when implemented with frames.</p>
				<p><a href="frames/index.php">Click here to launch the frames demo.</a></p>
				
				<?php } else { ?>
				
				<h4>Welcome to the TabView Demo,</h4>
				<p>This is a general demo of TabView, however there are many useful 
				applications of TabView.</p>			
				<p>Select one of the tabs above for demos of specific applications for TabView.</p>
				
				<p>
				<b>Change Tab Alignment:</b><br>
				<a href="?right=0&color=<?=$_GET['color']?>&TB=<?=$_GET['TB']?>">$TabView->Orientation = 0; (Left)</a><br>
				<a href="?right=1&color=<?=$_GET['color']?>&TB=<?=$_GET['TB']?>">$TabView->Orientation = 1; (Right)</a>
				</p>

				<p>
				<b>Change Tab Color:</b><br>
				<table cellpadding="4" cellspacing="0" border="1" bordercolor="#CCCCCC">
				<tr>
					<td><b>Red</b></td>
					<td>
						<a href="?right=<?=$_GET['right']?>&color=red&TB=<?=$_GET['TB']?>">
						$TabView->ImagePath = "../images/red";<br>
						$TabView->BackColor = "#FF6666";<br>
						$TabView->SelectedBackColor = "#FF0000";</a>
					</td>
				</tr>
				<tr>
					<td><b>Blue</b></td>
					<td>
						<a href="?right=<?=$_GET['right']?>&TB=<?=$_GET['TB']?>">
						$TabView->ImagePath = "../images/blue";<br>
						$TabView->BackColor = "#EEEEFF";<br>
						$TabView->SelectedBackColor = "#CCCCFF";</a>
					</td>
				</tr>
				</p>
				
				<?php } ?>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<br>
