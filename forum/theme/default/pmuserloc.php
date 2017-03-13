<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: pmuserloc.php.t,v 1.23 2005/03/05 18:46:59 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

	define('plain_form', 1);

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function alt_var($key)
{
	if (!isset($GLOBALS['_ALTERNATOR_'][$key])) {
		$args = func_get_args(); unset($args[0]);
		$GLOBALS['_ALTERNATOR_'][$key] = array('p' => 2, 't' => func_num_args(), 'v' => $args);
		return $args[1];
	}
	$k =& $GLOBALS['_ALTERNATOR_'][$key];
	if ($k['p'] == $k['t']) {
		$k['p'] = 1;
	}
	return $k['v'][$k['p']++];
}

	if (empty($_GET['js_redr'])) {
		exit;
	}

	if (!_uid) {
		std_error('login');
	} else if (!($FUD_OPT_1 & (8388608|4194304))) {
		std_error('disabled');
	}



	$usr_login = isset($_GET['usr_login']) ? trim($_GET['usr_login']) : '';
	$overwrite = isset($_GET['overwrite']) ? (int)$_GET['overwrite'] : 0;

	$js_redr = $_GET['js_redr'];
	switch ($js_redr) {
		case 'post_form.msg_to_list':
		case 'groupmgr.gr_member':
		case 'buddy_add.add_login':
			break;
		default:
			exit;
	}

	$find_user_data = '';
	if ($usr_login) {
		$c = uq("SELECT alias FROM fud26_users WHERE alias LIKE '".addslashes(char_fix(htmlspecialchars(str_replace('\\', '\\\\', $usr_login))))."%' AND id>1");
		$i = 0;
		while ($r = db_rowarr($c)) {
			if ($overwrite) {
				$retlink = 'javascript: window.opener.document.'.$js_redr.'.value=\''.addcslashes($r[0], "'\\").'\'; window.close();';
			} else {
				$retlink = 'javascript:
						if (!window.opener.document.'.$js_redr.'.value) {
							window.opener.document.'.$js_redr.'.value = \''.addcslashes($r[0], "'\\").'\';
						} else {
							window.opener.document.'.$js_redr.'.value = window.opener.document.'.$js_redr.'.value + \'; \' + \''.addcslashes($r[0], "'\\").'; \';
						}
					window.close();';
			}
			$find_user_data .= '<tr class="'.alt_var('pmuserloc_alt','RowStyleA','RowStyleB').'"><td><a href="'.$retlink.'">'.$r[0].'</a></td></tr>';
			$i++;
		}
		if (!$find_user_data) {
			$find_user_data = '<tr><td colspan=2>No Result</td>';
		}
	}


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
<form name="pmuserloc" action="index.php" method="get"><?php echo _hs; ?>
<table cellspacing=0 cellpadding=3 class="dashed">
<tr>
	<td class="fb">Login:</td>
	<td><input tabindex="1" type="text" name="usr_login" value="<?php echo char_fix(htmlspecialchars($usr_login)); ?>"></td>
	<td><input tabindex="2" type="submit" class="button" name="btn_submit" value="Submit"></td>
</tr>
</table>
<input type="hidden" name="js_redr" value="<?php echo $js_redr; ?>">
<input type="hidden" name="overwrite" value="<?php echo $overwrite; ?>">
<input type="hidden" name="t" value="pmuserloc">
</form>
<script>
<!--
document.pmuserloc.usr_login.focus();
//-->
</script>
<br />
<table cellspacing=0 cellpadding=3 class="dashed wa">
<tr><th>User</td></tr>
<?php echo $find_user_data; ?>
</table>

</td></tr></table></body></html>