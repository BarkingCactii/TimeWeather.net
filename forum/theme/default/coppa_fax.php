<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: coppa_fax.php.t,v 1.15 2004/11/24 19:53:34 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}

	/* this form is for printing, therefore it lacks any advanced layout */
	if (!__fud_real_user__) {
		if ($FUD_OPT_2 & 32768) {
			header('Location: http://timeweather.net/forum/index.php/i/'._rsidl);
		} else {
			header('Location: http://timeweather.net/forum/index.php?t=index&'._rsidl);
		}
		exit;
	}
	$name = q_singleval("SELECT name FROM fud26_users WHERE id=".__fud_real_user__);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head><title> </title></head>
<body bgcolor="#ffffff">
<b>Instructions for a Parent or Guardian</b><br /><br />
Please print this page, sign it, and fax it to:
<pre>
<?php echo @file_get_contents($FORUM_SETTINGS_PATH."coppa_maddress.msg"); ?>
</pre>
<table border=1 cellspacing=1 cellpadding=3>
<tr><td colspan=2>Registration Form</td></tr>
<tr><td>Login</td><td><?php echo $usr->login; ?></td></tr>
<tr><td>Password</td><td>&lt;HIDDEN&gt;</td></tr>
<tr><td>E-mail</td><td><?php echo $usr->email; ?></td></tr>
<tr><td>Name</td><td><?php echo $name; ?></td></tr>
<tr><td colspan=2>
Please sign the form below and send it to us<br />
I have reviewed the information my child has supplied and I have read the Privacy Policy for the web site. I understand that the profile information may be changed using a password. I understand that I may ask that this registration profile be removed entirely from the forum.
</td></tr>
<tr><td>Sign here if you give permission</td><td><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td></tr>
<tr><td>Sign here if you would like the account to be deleted</td><td><u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></td></tr>
<tr><td>Parent/Guardian Full Name:</td><td>&nbsp;</td></tr>
<tr><td>Relation to Child:</td><td>&nbsp;</td></tr>
<tr><td>Telephone:</td><td>&nbsp;</td></tr>
<tr><td>E-mail Address:</td><td>&nbsp;</td></tr>
<tr><td>Date:</td><td>&nbsp;</td></tr>
<tr><td colspan=2>Please contact <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>"><?php echo $GLOBALS['ADMIN_EMAIL']; ?></a> with any questions</td></tr>
</table>
</body>
</html>