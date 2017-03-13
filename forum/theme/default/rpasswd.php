<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: rpasswd.php.t,v 1.15 2005/02/23 02:16:25 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

	define('plain_form', 1);

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function logaction($user_id, $res, $res_id=0, $action=null)
{
	q('INSERT INTO fud26_action_log (logtime, logaction, user_id, a_res, a_res_id)
		VALUES('.__request_timestamp__.', '.strnull($action).', '.$user_id.', '.strnull($res).', '.(int)$res_id.')');
}

	if (!__fud_real_user__) {
		std_error('login');
	}

	if (isset($_POST['btn_submit'])) {
		if (__fud_real_user__ != q_singleval("SELECT id FROM fud26_users WHERE login='".addslashes($usr->login)."' AND passwd='".md5($_POST['cpasswd'])."'")) {
			$rpasswd_error_msg = 'Invalid Password';
		} else if ($_POST['passwd1'] !== $_POST['passwd2']) {
			$rpasswd_error_msg = 'Passwords do not match';
		} else if (strlen($_POST['passwd1']) < 6 ) {
			$rpasswd_error_msg = 'Password must be at least 6 characters long';
		} else {
			q("UPDATE fud26_users SET passwd='".md5($_POST['passwd1'])."' WHERE id=".__fud_real_user__);
			logaction(__fud_real_user__, 'CHANGE_PASSWD', 0, get_ip());
			exit('<html><script>window.close();</script></html>');
		}

		$rpasswd_error = '<tr><td class="rpasswdE" colspan=2>'.$rpasswd_error_msg.'</td></tr>';
	} else {
		$rpasswd_error = '';
	}

	$TITLE_EXTRA = ': Change Password Form';



?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<title><?php echo $GLOBALS['FORUM_TITLE'].$TITLE_EXTRA; ?></title>
<BASE HREF="http://timeweather.net/forum/">
<script language="JavaScript" src="lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="theme/default/forum.css" type="text/css">
</head>
<body>
<table class="wa" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<form method="post" action="index.php?t=rpasswd"><div align="center">
<table cellspacing="1" cellpadding="2" class="MiniTable">
<?php echo $rpasswd_error; ?>
<tr><th colspan=2>Change Password</th></tr>
<tr class="RowStyleB"><td>Login</td><td><?php echo htmlspecialchars($usr->login); ?></td></tr>
<tr class="RowStyleB"><td>Current Password:</td><td><input type="password" name="cpasswd" value=""></td></tr>
<tr class="RowStyleB"><td>New Password:</td><td><input type="password" name="passwd1" value=""></td></tr>
<tr class="RowStyleB"><td>Confirm Password:</td><td><input type="password" name="passwd2" value=""></td></tr>
<tr class="RowStyleB"><td align=right colspan=2><input type="submit" class="button" value="Go" name="btn_submit"></td></tr>
</table></div><?php echo _hs; ?></form>
</td></tr></table></body></html>