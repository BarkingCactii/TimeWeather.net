<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: ip.php.t,v 1.9 2005/03/05 18:46:59 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function check_return($returnto)
{
	if ($GLOBALS['FUD_OPT_2'] & 32768 && !empty($_SERVER['PATH_INFO'])) {
		if (!$returnto || !strncmp($returnto, '/er/', 4)) {
			header('Location: http://timeweather.net/forum/index.php/i/'._rsidl);
		} else if ($returnto[0] == '/') { /* unusual situation, path_info & normal themes are active */
			header('Location: http://timeweather.net/forum/index.php'.$returnto);
		} else {
			header('Location: http://timeweather.net/forum/index.php?'.$returnto);
		}
	} else if (!$returnto || !strncmp($returnto, 't=error', 7)) {
		header('Location: http://timeweather.net/forum/index.php?t=index&'._rsidl);
	} else if (strpos($returnto, 'S=') === false && $GLOBALS['FUD_OPT_1'] & 128) {
		header('Location: http://timeweather.net/forum/index.php?'.$returnto.'&S='.s);
	} else {
		header('Location: http://timeweather.net/forum/index.php?'.$returnto);
	}
	exit;
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

	/* permissions check, this form is only allowed for moderators & admins unless public
	 * IP display is allowed
	 */
	if (!($usr->users_opt & (524288|1048576)) && !($FUD_OPT_1 & 134217728)) {
		invl_inp_err();
	}

function __fud_whois($ip, $whois_server='whois.arin.net')
{
	$er = error_reporting(0);

	if (!$sock = fsockopen($whois_server, 43, $n, $e, 20)) {
		error_reporting($er);
		return;
	}
	fputs($sock, $ip."\n");
	$buffer = '';
	do {
		$buffer .= fread($sock, 10240);
	} while (!feof($sock));
	fclose($sock);

	return $buffer;
}

function fud_whois($ip)
{
	$result = __fud_whois($ip);

	/* check if Arin can handle the request or if we need to
	 * request information from another server.
	 */
	if (($p = strpos($result, 'ReferralServer: whois://')) !== false) {
		$p += strlen('ReferralServer: whois://');
		$e = strpos($result, "\n", $p);
		$whois = substr($result, $p, ($e - $p));
		if ($whois) {
			$result = __fud_whois($ip, $whois);
		}
	}

	return ($result ? $result : 'Whois information for <b>'.$ip.'</b> is not available.');
}

if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
	}

	if (isset($_POST['ip'])) {
		$_GET['ip'] = $_POST['ip'];
	}
	$ip = isset($_GET['ip']) ? long2ip(ip2long($_GET['ip'])) : '';
	if (isset($_POST['user'])) {
		$_GET['user'] = $_POST['user'];
	}
	if (isset($_GET['user'])) {
		if (($user_id = (int) $_GET['user'])) {
			$user = q_singleval("SELECT alias FROM fud26_users WHERE id=".$user_id);
		} else {
			list($user_id, $user) = db_saq("SELECT id, alias FROM fud26_users WHERE alias='".addslashes(char_fix(htmlspecialchars($_GET['user'])))."'");
		}
	} else {
		$user = '';
	}

	$TITLE_EXTRA = ': IP Browser';

	if ($ip) {
		if (substr_count($ip, '.') == 3) {
			$cond = "m.ip_addr='".$ip."'";
		} else {
			$cond = "m.ip_addr LIKE '".$ip."%'";
		}

		$o = uq("SELECT DISTINCT(m.poster_id), u.alias from fud26_msg m INNER JOIN fud26_users u ON m.poster_id=u.id WHERE ".$cond);
		$user_list = '';
		$i = 0;
		while ($r = db_rowarr($o)) {
			$user_list .= '<tr><td class="'.alt_var('ip_alt','RowStyleA','RowStyleB').'">'.++$i.'. <a href="index.php?t=usrinfo&amp;id='.$r[0].'&amp;'._rsid.'">'.$r[1].'</a></td></tr>';
		}
		$page_data = '<table cellspacing="2" cellpadding="2" class="MiniTable">
<tr>
	<td class="vt"><table cellspacing="0" cellpadding="2" class="ContentTable">
		<tr><th>User(s) using &#39;'.$ip.'&#39; IP address</th></tr>'.$user_list.'
	</table></td>
	<td width="50"> </td>
	<td class="vt"><b>ISP Information</b><br /><div class="ip"><pre>'.fud_whois($ip).'</pre></div></td>
</tr>
</table>';
	} else if ($user) {
		$o = uq("SELECT DISTINCT(ip_addr) FROM fud26_msg WHERE poster_id=".$user_id);
		$ip_list = '';
		$i = 0;
		while ($r = db_rowarr($o)) {
			$ip_list .= '<tr><td class="'.alt_var('ip_alt','RowStyleA','RowStyleB').'">'.++$i.'. <a href="index.php?t=ip&amp;ip='.$r[0].'&amp;'._rsid.'">'.$r[0].'</a></td></tr>';
		}
		$page_data = '<table cellspacing="2" cellpadding="2" class="MiniTable">
<tr><th>All IP(s) used by &#39;'.$user.'&#39;</th></tr>
'.$ip_list.'
</table>';
	} else {
		$page_data = '';
	}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $GLOBALS['FORUM_TITLE'].$TITLE_EXTRA; ?></title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<BASE HREF="http://timeweather.net/forum/">
<script language="javascript" src="lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="theme/default/forum.css" type="text/css" media="screen" title="Default FUDforum Theme">
</head>
<body>

<table width="98%" cellpadding="0" cellspacing="0" class="tbright_cell">
<tr valign="top" bgcolor="#EEEEFF">
<img border="0" src="/images/banner.jpg" width="730" height="100">

<img border="0" src="/images/line14.gif" width="100%" height="25">
</tr>



<tr>

<h2><a href="http://timeweather.net">Jump to Website</a></h2>
<table class="wa" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">


</tr>
<div class="UserControlPanel"><?php echo $private_msg; ?> <?php echo (($FUD_OPT_1 & 8388608 || (_uid && $FUD_OPT_1 & 4194304) || $usr->users_opt & 1048576) ? '<a class="UserControlPanel" href="index.php?t=finduser&amp;btn_submit=Find&amp;'._rsid.'"><img src="theme/default/images/top_members.png" alt="Members" /> Members</a>&nbsp;&nbsp;' : ''); ?> <?php echo ($FUD_OPT_1 & 16777216 ? '<a class="UserControlPanel" href="index.php?t=search&amp;'._rsid.'"><img src="theme/default/images/top_search.png" alt="Search" /> Search</a>&nbsp;&nbsp;' : ''); ?> <a class="UserControlPanel" accesskey="h" href="index.php?t=help_index&amp;<?php echo _rsid; ?>"><img src="theme/default/images/top_help.png" alt="FAQ" /> FAQ</a> <?php echo (__fud_real_user__ ? '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=uc&amp;'._rsid.'"><img src="theme/default/images/top_profile.png" title="Click here to access user control panel" alt="User CP" /> User CP</a>' : '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=register&amp;'._rsid.'"><img src="theme/default/images/top_register.png" alt="Register" /> Register</a>'); ?> <?php echo (__fud_real_user__ ? '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=login&amp;'._rsid.'&amp;logout=1&amp;SQ='.$GLOBALS['sq'].'"><img src="theme/default/images/top_logout.png" alt="Logout" /> Logout [ '.$usr->alias.' ]</a>' : '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=login&amp;'._rsid.'"><img src="theme/default/images/top_login.png" alt="Login" /> Login</a>'); ?>&nbsp;&nbsp; <a class="UserControlPanel" href="index.php?t=index&amp;<?php echo _rsid; ?>"><img src="theme/default/images/top_home.png" alt="Home" /> Home</a> <?php echo ($is_a ? '&nbsp;&nbsp;<a class="UserControlPanel" href="adm/admglobal.php?S='.s.'&amp;SQ='.$GLOBALS['sq'].'"><img src="theme/default/images/top_admin.png" alt="Admin Control Panel" /> Admin Control Panel</a>' : ''); ?></div>

<div class="ctb"><table cellspacing=0 cellpadding=0 class="MiniTable"><tr><td><fieldset>
	<legend>Search users by IP</legend>
<form method="post" action="index.php?t=ip"><?php echo _hs; ?>
<span class="SmallText">Supported syntax: 1.2.3.4, 1.2.3, 1.2, 1<br /></span>
<input type="text" name="ip" value="<?php echo $ip; ?>" length="20" maxlength="15"> <input type="submit" value="Search">
</form>
</fieldset></td><td width="50"> </td><td><fieldset>
	<legend>Analyze IP usage</legend>
<form method="post" action="index.php?t=ip"><?php echo _hs; ?>
<span class="SmallText">Please specify the user&#39;s exact login.<br /></span>
<input type="text" name="user" value="<?php echo $user; ?>" length="20"> <input type="submit" value="Search">
</form>
</fieldset></td>
</tr></table>
<br /><br />
<?php echo $page_data; ?>
</div>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>
