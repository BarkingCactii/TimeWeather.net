<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: buddy_list.php.t,v 1.37 2005/03/05 18:46:59 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function buddy_add($user_id, $bud_id)
{
	q('INSERT INTO fud26_buddy (bud_id, user_id) VALUES ('.$bud_id.', '.$user_id.')');
	return buddy_rebuild_cache($user_id);
}

function buddy_delete($user_id, $bud_id)
{
	q('DELETE FROM fud26_buddy WHERE user_id='.$user_id.' AND bud_id='.$bud_id);
	return buddy_rebuild_cache($user_id);
}

function buddy_rebuild_cache($uid)
{
	$q = uq('SELECT bud_id FROM fud26_buddy WHERE user_id='.$uid);
	while ($ent = db_rowarr($q)) {
		$arr[$ent[0]] = 1;
	}

	if (isset($arr)) {
		q('UPDATE fud26_users SET buddy_list=\''.addslashes(serialize($arr)).'\' WHERE id='.$uid);
		return $arr;
	}
	q('UPDATE fud26_users SET buddy_list=NULL WHERE id='.$uid);
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

	if (!_uid) {
		std_error('login');
	}

	if (isset($_POST['add_login'])) {
		if (!($buddy_id = q_singleval("SELECT id FROM fud26_users WHERE alias='".addslashes(char_fix(htmlspecialchars($_POST['add_login'])))."'"))) {
			error_dialog('Unable to add user', 'The user you tried to add to your buddy list was not found.');
		}
		if ($buddy_id == _uid) {
			error_dialog('Info', 'You cannot add yourself to your buddy list');
		}

		if (!empty($usr->buddy_list)) {
			$usr->buddy_list = unserialize($usr->buddy_list);
		}

		if (!isset($usr->buddy_list[$buddy_id])) {
			$usr->buddy_list = buddy_add(_uid, $buddy_id);
		} else {
			error_dialog('Info', 'You already have this user on your buddy list');
		}
	}

	/* incomming from message display page (add buddy link) */
	if (isset($_GET['add']) && ($_GET['add'] = (int)$_GET['add'])) {
		if (!sq_check(0, $usr->sq)) {
			check_return($usr->returnto);
		}

		if (!empty($usr->buddy_list)) {
			$usr->buddy_list = unserialize($usr->buddy_list);
		}

		if (($buddy_id = q_singleval('SELECT id FROM fud26_users WHERE id='.$_GET['add'])) && !isset($usr->buddy_list[$buddy_id])) {
			buddy_add(_uid, $buddy_id);
		}
		check_return($usr->returnto);
	}

	if (isset($_GET['del']) && ($_GET['del'] = (int)$_GET['del'])) {
		if (!sq_check(0, $usr->sq)) {
			check_return($usr->returnto);
		}

		buddy_delete(_uid, $_GET['del']);
		/* needed for external links to this form */
		if (isset($_GET['redr'])) {
			check_return($usr->returnto);
		}
	}

	ses_update_status($usr->sid, 'Browsing own buddy list');

if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
	}$tabs = '';
if (_uid) {
	$tablist = array(
'User CP'=>'uc',
'Settings'=>'register',
'Subscriptions'=>'subscribed',
'Referrals'=>'referals',
'Buddy List'=>'buddy_list',
'Ignore List'=>'ignore_list');

	if (!($FUD_OPT_2 & 8192)) {
		unset($tablist['Referrals']);
	}

	if (isset($_POST['mod_id'])) {
		$mod_id_chk = $_POST['mod_id'];
	} else if (isset($_GET['mod_id'])) {
		$mod_id_chk = $_GET['mod_id'];
	} else {
		$mod_id_chk = null;
	}

	if (!$mod_id_chk) {
		if ($FUD_OPT_1 & 1024) {
			$tablist['Private Messaging'] = 'pmsg';
		}
		$pg = ($_GET['t'] == 'pmsg_view' || $_GET['t'] == 'ppost') ? 'pmsg' : $_GET['t'];

		foreach($tablist as $tab_name => $tab) {
			$tab_url = 'index.php?t='.$tab.'&amp;S='.s;
			if ($tab == 'referals') {
				if (!($FUD_OPT_2 & 8192)) {
					continue;
				}
				$tab_url .= '&amp;id='._uid;
			}
			$tabs .= $pg == $tab ? '<td class="tabON"><div class="tabT"><a class="tabON" href="'.$tab_url.'">'.$tab_name.'</a></div></td>' : '<td class="tabI"><div class="tabT"><a href="'.$tab_url.'">'.$tab_name.'</a></div></td>';
		}

		$tabs = '<table cellspacing=1 cellpadding=0 class="tab">
<tr>'.$tabs.'</tr>
</table>';
	}
}

	$c = uq('SELECT b.bud_id, u.id, u.alias, u.join_date, u.bday, (u.users_opt & 32768), u.posted_msg_count, u.home_page, u.last_visit AS time_sec
		FROM fud26_buddy b INNER JOIN fud26_users u ON b.bud_id=u.id WHERE b.user_id='._uid);

	$buddies = '';
	/* Result index
	 * 0 - bud_id	1 - user_id	2 - login	3 - join_date	4 - bday	5 - users_opt	6 - msg_count
	 * 7 - home_page	8 - last_visit
	 */

	if (($r = @db_rowarr($c))) {
		$dt = getdate(__request_timestamp__);
		$md = sprintf("%02d%02d", $dt['mon'], $dt['mday']);

		do {
			if ((!($r[5] & 32768) && $FUD_OPT_2 & 32) || $is_a) {
				$online_status = (($r[8] + $LOGEDIN_TIMEOUT * 60) > __request_timestamp__) ? '<img src="theme/default/images/online.png" title="'.$r[2].' is currently online" alt="'.$r[2].' is currently online" />' : '<img src="theme/default/images/offline.png" title="'.$r[2].' is currently offline" alt="'.$r[2].' is currently offline" />';
			} else {
				$online_status = '';
			}

			if ($r[4] && substr($r[4], 4) == $md) {
				$age = $dt['year'] - substr($r[4], 0, 4);
				$bday_indicator = '<img src="blank.gif" alt="" width=10 height=1 /><img src="theme/default/images/bday.gif" alt="" />Today '.$r[2].' turns '.$age;
			} else {
				$bday_indicator = '';
			}

			$buddies .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'">
	<td class="ac">'.$online_status.'</td>
	<td class="GenText wa">'.($FUD_OPT_1 & 1024 ? '<a href="index.php?t=ppost&amp;'._rsid.'&amp;toi='.urlencode($r[0]).'">'.$r[2].'</a>' : '<a href="index.php?t=email&amp;toi='.$r[1].'&amp;'._rsid.'">'.$r[2].'</a>' ) .'&nbsp;<span class="SmallText">(<a href="index.php?t=buddy_list&amp;'._rsid.'&amp;del='.$r[0].'&amp;SQ='.$GLOBALS['sq'].'">remove</a>)</span>&nbsp;'.$bday_indicator.'</td>
	<td class="ac">'.$r[6].'</td>
	<td class="ac nw">'.strftime("%a, %d %B %Y %H:%M", $r[3]).'</td>
	<td class="GenText nw"><a href="index.php?t=usrinfo&amp;id='.$r[1].'&amp;'._rsid.'"><img src="theme/default/images/msg_about.gif" alt="" /></a>&nbsp;<a href="index.php?t=showposts&amp;'._rsid.'&amp;id='.$r[1].'"><img src="theme/default/images/show_posts.gif" alt="" /></a> '.($r[7] ? '<a href="'.$r[7].'" target="_blank"><img src="theme/default/images/homepage.gif" alt="" /></a>' : '' ) .'</td>
</tr>';
		} while (($r = db_rowarr($c)));
		$buddies = '<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th>Status</th><th>My Buddies</th><th class="nw ac">Message Count</th><th class="ac nw">Registered On</th><th class="ac nw">Action</th></tr>
'.$buddies.'
</table>';
	}

if ($FUD_OPT_2 & 2 || $is_a) {
	$page_gen_end = gettimeofday();
	$page_gen_time = sprintf('%.5f', ($page_gen_end['sec'] - $PAGE_TIME['sec'] + (($page_gen_end['usec'] - $PAGE_TIME['usec'])/1000000)));
	$page_stats = $FUD_OPT_2 & 2 ? '<br /><div class="SmallText al">Total time taken to generate the page: '.$page_gen_time.' seconds</div>' : '<br /><div class="SmallText al">Total time taken to generate the page: '.$page_gen_time.' seconds</div>';
} else {
	$page_stats = '';
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
<?php echo $tabs; ?>
<?php echo $buddies; ?>
<br /><br />
<form name="buddy_add" action="index.php?t=buddy_list" method="post"><?php echo _hs; ?><div class="ctb">
<table cellspacing="1" cellpadding="2" class="MiniTable">
<tr><th nowrap>Add Buddy</th></tr>
<tr class="RowStyleA">
<td class="GenText nw Smalltext">Enter the login of the user you wish to add.<?php echo (($FUD_OPT_1 & 8388608 || (_uid && $FUD_OPT_1 & 4194304)) ? '<br>Or use the <a href="javascript://" onClick="javascript: window_open(\'http://timeweather.net/forum/index.php?t=pmuserloc&amp;'._rsid.'&amp;js_redr=buddy_add.add_login&amp;overwrite=1\', \'user_list\', 325,250);">Find User</a> feature to find a person.' : ''); ?><p>
<input type="text" tabindex="1" name="add_login" value="" maxlength=100 size=25> <input tabindex="2" type="submit" class="button" name="submit" value="Add"></td></tr>
</table></div></form>
<script>
<!--
document.buddy_add.add_login.focus();
//-->
</script>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>