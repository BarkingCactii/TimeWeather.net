<?php
/******************************
	
	Filename: index.php
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
<title>TabView Forms Demo</title>

<link rel="stylesheet" href="../../stylesheet.php" type="text/css">

<script type="text/javascript">
<!--
function ftnSubmit(strPage, intTab) {
	document.form1.action = strPage+"?TB="+intTab;
	document.form1.submit();
}
//-->
</script>

</head>

<body bgcolor="#FFFFFF" text="#000000" link="#FF0000" alink="#FF0000" vlink="#FFCCCC" onLoad="javascript: window.self.focus();">

<table width="98%" cellpadding="0" cellspacing="0">
<tr>
	<td><img src="../../../../../../images/spacer.gif" width="1" height="4"></td>
</tr>
<tr>
	<td>

		<?php
		$TabView = new TabView();

		$Tab =& $TabView->Add("1","Personal Info","","","Personal Information");
		$Tab->Image = "../../images/edit.gif";

		$Tab =& $TabView->Add("2","Address Info","","","Address Information");
		$Tab->Image = "../../images/book.gif";

		$Tab =& $TabView->Add("3","Payment Info","","","Payment Information");
		$Tab->Image = "../../images/icon_fav.gif";

		$Tab =& $TabView->Add("4","Confirmation","","","Confirm Information Accuracy");
		$Tab->Image = "../../images/search.gif";

		$TabView->ImagePath = "../../images/blue";
		$TabView->ForceDHTML = true;
		$TabView->DHTML = "href=\"javascript: ftnSubmit('#PAGE#', '#TAB#');\"";
		$TabView->StartTab = "1";
		$TabView->Class = "tabviewDemo";

		$TabView->Show();
		?>

	</td>
</tr>
<tr>
	<td>
		<table width="100%" border="1" bordercolor="#CCCCFF" cellpadding="8" cellspacing="0">		
		<tr>
			<td bgcolor="FAFAFA">
				<form name="form1" method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<input type="hidden" name="salute" value="<?=$_POST['salute']?>">
				<input type="hidden" name="fname" value="<?=$_POST['fname']?>">
				<input type="hidden" name="lname" value="<?=$_POST['lname']?>">
				<input type="hidden" name="email" value="<?=$_POST['email']?>">
				<input type="hidden" name="phone" value="<?=$_POST['phone']?>">
				<input type="hidden" name="address1" value="<?=$_POST['address1']?>">
				<input type="hidden" name="address2" value="<?=$_POST['address2']?>">
				<input type="hidden" name="address3" value="<?=$_POST['address3']?>">
				<input type="hidden" name="city" value="<?=$_POST['city']?>">
				<input type="hidden" name="state" value="<?=$_POST['state']?>">
				<input type="hidden" name="country" value="<?=$_POST['country']?>">
				<input type="hidden" name="zip" value="<?=$_POST['zip']?>">
				<input type="hidden" name="cc_type" value="<?=$_POST['cc_type']?>">
				<input type="hidden" name="cc_name" value="<?=$_POST['cc_name']?>">
				<input type="hidden" name="cc_num" value="<?=$_POST['cc_num']?>">
				<input type="hidden" name="cc_exp" value="<?=$_POST['cc_exp']?>">

				<?php if ($TabView->TB == "2") { ?>
				
				<table cellpadding="4" cellspacing="0" border="0">
				<tr>
					<td valign="top">Address:</td>
					<td>
						<input type="text" name="address1" value="<?=htmlentities($_POST['address1'])?>" size="20"><br>
						<input type="text" name="address2" value="<?=htmlentities($_POST['address2'])?>" size="20"><br>
						<input type="text" name="address3" value="<?=htmlentities($_POST['address3'])?>" size="20">
					</td>
				</tr>
				<tr>
					<td>City:</td>
					<td><input type="text" name="city" value="<?=htmlentities($_POST['city'])?>" size="20"></td>
				</tr>
				<tr>
					<td>State/Province:</td>
					<td><input type="text" name="state" value="<?=htmlentities($_POST['state'])?>" size="20"></td>
				</tr>
				<tr>
					<td>Country:</td>
					<td><input type="text" name="country" value="<?=htmlentities($_POST['country'])?>" size="20"></td>
				</tr>
				<tr>
					<td>Zip/Postal Code:</td>
					<td><input type="text" name="zip" value="<?=htmlentities($_POST['zip'])?>" size="20"></td>
				</tr>
				</table>
				
				<?php } elseif ($TabView->TB == "3") { ?>
				
				<table cellpadding="4" cellspacing="0" border="0">
				<tr>
					<td>Credit Card:</td>
					<td>
						<select name="cc_type">
						<?php
						$arrOptions = array("", "Visa", "Mastercard");
						for ($i = 0; $i < count($arrOptions); $i++) {
							if ($_POST['cc_type'] == $arrOptions[$i]) {
								printf("<option value=\"%s\" selected>%s</option>", $arrOptions[$i], $arrOptions[$i]);
							}
							else {
								printf("<option value=\"%s\">%s</option>", $arrOptions[$i], $arrOptions[$i]);
							}
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td>Name on Card:</td>
					<td><input type="text" name="cc_name" value="<?=htmlentities($_POST['cc_name'])?>" size="20"></td>
				</tr>
				<tr>
					<td>Card Number:</td>
					<td><input type="text" name="cc_num" value="<?=htmlentities($_POST['cc_num'])?>" size="20"></td>
				</tr>
				<tr>
					<td>Expiry Date (MM/YY):</td>
					<td><input type="text" name="cc_exp" value="<?=htmlentities($_POST['cc_exp'])?>" size="20"></td>
				</tr>
				</table>
				
				<?php } elseif ($TabView->TB == "4") { ?>
				
				<table cellpadding="3" cellspacing="1" border="0">
				<tr>
					<td bgcolor="#EEEEEE">Name:</td>
					<td><?=$_POST['salute']." ".$_POST['fname']." ".$_POST['lname']?></td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">E-mail:</td>
					<td><?=$_POST['email']?></td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">Phone:</td>
					<td><?=$_POST['phone']?></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">Address:</td>
					<td><?=$_POST['address1']."<br>".$_POST['address2']."<br>".$_POST['address3']?></td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">City:</td>
					<td><?=$_POST['city']?></td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">State/Province:</td>
					<td><?=$_POST['state']?></td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">Country:</td>
					<td><?=$_POST['country']?></td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">Zip/Postal Code:</td>
					<td><?=$_POST['zip']?></td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">Credit Card:</td>
					<td><?=$_POST['cc_type']?></td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">Name on Card:</td>
					<td><?=$_POST['cc_name']?></td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">Card Number:</td>
					<td><?=$_POST['cc_num']?></td>
				</tr>
				<tr>
					<td bgcolor="#EEEEEE">Expiry Date (MM/YY):</td>
					<td><?=$_POST['cc_exp']?></td>
				</tr>
				</table>
				
				<br>
				<input type="button" name="butsubmit" value="Submit Information">
				
				<?php } else { ?>
				
				<table cellpadding="4" cellspacing="0" border="0">
				<tr>
					<td>Salutation:</td>
					<td>
						<select name="salute">
						<?php
						$arrSalutes = array("", "Mr.", "Mrs.", "Ms.");
						for ($i = 0; $i < count($arrSalutes); $i++) {
							if ($_POST['salute'] == $arrSalutes[$i]) {
								printf("<option value=\"%s\" selected>%s</option>", $arrSalutes[$i], $arrSalutes[$i]);
							}
							else {
								printf("<option value=\"%s\">%s</option>", $arrSalutes[$i], $arrSalutes[$i]);
							}
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td>First Name:</td>
					<td><input type="text" name="fname" value="<?=htmlentities($_POST['fname'])?>" size="20"></td>
				</tr>
				<tr>
					<td>Last Name:</td>
					<td><input type="text" name="lname" value="<?=htmlentities($_POST['lname'])?>" size="20"></td>
				</tr>
				<tr>
					<td>E-mail:</td>
					<td><input type="text" name="email" value="<?=htmlentities($_POST['email'])?>" size="20"></td>
				</tr>
				<tr>
					<td>Phone:</td>
					<td><input type="text" name="phone" value="<?=htmlentities($_POST['phone'])?>" size="20"></td>
				</tr>
				</table>
				
				<p><b>Click another tab to contine...</b></p>

				<?php } ?>
				</form>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
<br>
