<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: index.php.t,v 1.85 2005/02/17 00:16:52 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function draw_user_link($login, $type, $custom_color='')
{
	if ($custom_color) {
		return '<span style="color: '.$custom_color.'">'.$login.'</span>';
	}

	switch ($type & 1572864) {
		case 0:
		default:
			return $login;
		case 1048576:
			return '<span class="adminColor">'.$login.'</span>';
		case 524288:
			return '<span class="modsColor">'.$login.'</span>';
	}
}

function reload_collapse($str)
{
	if (!($tok = strtok($str, '_'))) {
		return;
	}
	do {
		$t = explode(':', $tok);
		if ((int) $t[0]) {
			$GLOBALS['collapse'][(int) $t[0]] = isset($t[1]) ? (int) $t[1] : 0;
		}
	} while (($tok = strtok('_')));
}

function url_tog_collapse($id, $c)
{
	if (!isset($GLOBALS['collapse'][$id])) {
		return;
	}

	if (!$c) {
		return $id . ':'.(empty($GLOBALS['collapse'][$id]) ? '1' : '0');
	} else {
		$c_status = (empty($GLOBALS['collapse'][$id]) ? 1 : 0);

		if (isset($GLOBALS['collapse'][$id]) && ($p = strpos('_' . $c, '_' . $id . ':' . (int)!$c_status)) !== false) {
			$c[$p + strlen($id) + 1] = $c_status;
			return $c;
		} else {
			return $c . '_' . $id . ':' . $c_status;
		}
	}
}

	if (isset($_GET['c'])) {
		$cs = $_GET['c'];
		if (_uid && $cs != $usr->cat_collapse_status) {
			q("UPDATE fud26_users SET cat_collapse_status='".addslashes($cs)."' WHERE id="._uid);
		}
		reload_collapse($cs);
	} else if (_uid && $usr->cat_collapse_status) {
		$cs = $usr->cat_collapse_status;
		reload_collapse($cs);
	} else {
		$cs = '';
	}

	$cat_id = !empty($_GET['cat']) ? (int) $_GET['cat'] : 0;

	ses_update_status($usr->sid, 'Browsing the <a href="index.php?t=index">forum list</a>');

	require $FORUM_SETTINGS_PATH . 'idx.inc';

if (_uid) {
	$admin_cp = $accounts_pending_approval = $group_mgr = $reported_msgs = $custom_avatar_queue = $mod_que = $thr_exch = '';

	if ($usr->users_opt & 524288 || $is_a) {
		if ($is_a) {
			if ($FUD_OPT_1 & 32 && ($avatar_count = q_singleval("SELECT count(*) FROM fud26_users WHERE users_opt>=16777216 AND (users_opt & 16777216) > 0"))) {
				$custom_avatar_queue = '| <a href="adm/admapprove_avatar.php?S='.s.'&amp;SQ='.$GLOBALS['sq'].'">Custom Avatar Queue</a> <span class="GenTextRed">('.$avatar_count.')</span>';
			}
			if ($report_count = q_singleval('SELECT count(*) FROM fud26_msg_report')) {
				$reported_msgs = '| <a href="index.php?t=reported&amp;'._rsid.'">Reported Messages</a> <span class="GenTextRed">('.$report_count.')</span>';
			}

			if ($thr_exchc = q_singleval('SELECT count(*) FROM fud26_thr_exchange')) {
				$thr_exch = '| <a href="index.php?t=thr_exch&amp;'._rsid.'">Topic Exchange</a> <span class="GenTextRed">('.$thr_exchc.')</span>';
			}

			if ($FUD_OPT_2 & 1024 && ($accounts_pending_approval = q_singleval("SELECT count(*) FROM fud26_users WHERE users_opt>=2097152 AND (users_opt & 2097152) > 0"))) {
				$accounts_pending_approval = '| <a href="adm/admaccapr.php?S='.s.'&amp;SQ='.$GLOBALS['sq'].'">Accounts Pending Approval</a> <span class="GenTextRed">('.$accounts_pending_approval.')</span>';
			}

			$q_limit = '';
		} else {
			if ($report_count = q_singleval('SELECT count(*) FROM fud26_msg_report mr INNER JOIN fud26_msg m ON mr.msg_id=m.id INNER JOIN fud26_thread t ON m.thread_id=t.id INNER JOIN fud26_mod mm ON t.forum_id=mm.forum_id AND mm.user_id='._uid)) {
				$reported_msgs = '| <a href="index.php?t=reported&amp;'._rsid.'">Reported Messages</a> <span class="GenTextRed">('.$report_count.')</span>';
			}

			if ($thr_exchc = q_singleval('SELECT count(*) FROM fud26_thr_exchange te INNER JOIN fud26_mod m ON m.user_id='._uid.' AND te.frm=m.forum_id')) {
				$thr_exch = '| <a href="index.php?t=thr_exch&amp;'._rsid.'">Topic Exchange</a> <span class="GenTextRed">('.$thr_exchc.')</span>';
			}

			$q_limit = ' INNER JOIN fud26_mod mm ON f.id=mm.forum_id AND mm.user_id='._uid;
		}

		if ($approve_count = q_singleval("SELECT count(*) FROM fud26_msg m INNER JOIN fud26_thread t ON m.thread_id=t.id INNER JOIN fud26_forum f ON t.forum_id=f.id ".$q_limit." WHERE m.apr=0 AND (f.forum_opt>=2 AND (f.forum_opt & 2) > 0)")) {
			$mod_que = '<a href="index.php?t=modque&amp;'._rsid.'">Moderation Queue</a> <span class="GenTextRed">('.$approve_count.')</span>';
		}
	}
	if ($is_a || $usr->group_leader_list) {
		$group_mgr = '| <a href="index.php?t=groupmgr&amp;'._rsid.'">Group(s) Manager</a>';
	}

	if ($thr_exch || $accounts_pending_approval || $group_mgr || $reported_msgs || $custom_avatar_queue || $mod_que) {
		$admin_cp = '<br /><span class="GenText fb">Admin:</span> '.$mod_que.' '.$reported_msgs.' '.$thr_exch.' '.$custom_avatar_queue.' '.$group_mgr.' '.$accounts_pending_approval.'<br />';
	}
} else {
	$admin_cp = '';
}if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
	}if (!isset($th)) {
	$th = 0;
}
if (!isset($frm->id)) {
	$frm->id = 0;
}
	$TITLE_EXTRA = ': Welcome to the forum';

	$cbuf = $forum_list_table_data = $cat_path = '';

	if ($cat_id) {
		$cid = $cat_id;
		while (($cid = $cidxc[$cid][4]) > 0) {
			$cat_path = '&nbsp;&raquo; <a href="index.php?t=i&amp;cat='.$cid.'&amp;'._rsid.'">'.$cidxc[$cid][1].'</a>' . $cat_path;
		}
		$cat_path = '<br/><a href="index.php?t=i&amp;'._rsid.'">Home</a>'.$cat_path.'&nbsp;&raquo; <b>'.$cidxc[$cat_id][1].'</b>';
	}

	/* List of fetched fields & their ids
	  0	msg.subject,
	  1	msg.id AS msg_id,
	  2	msg.post_stamp,
	  3	users.id AS user_id,
	  4	users.alias
	  5	forum.cat_id,
	  6	forum.forum_icon
	  7	forum.id
	  8	forum.last_post_id
	  9	forum.moderators
	  10	forum.name
	  11	forum.descr
	  12	forum.post_count
	  13	forum.thread_count
	  14	forum_read.last_view
	  15	is_moderator
	  16	read perm
	  17	is the category using compact view
	*/
	$c = uq('SELECT
				m.subject, m.id, m.post_stamp,
				u.id, u.alias,
				f.cat_id, f.forum_icon, f.id, f.last_post_id, f.moderators, f.name, f.descr, f.post_count, f.thread_count,
				'.(_uid ? 'fr.last_view, mo.id, CASE WHEN g2.group_cache_opt IS NULL THEN g1.group_cache_opt ELSE g2.group_cache_opt END AS group_cache_opt' : '0,0,g1.group_cache_opt').',
				c.cat_opt & 4
			FROM fud26_fc_view v
			INNER JOIN fud26_cat c ON c.id=v.c
			INNER JOIN fud26_forum f ON f.id=v.f
			INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? 2147483647 : 0).' AND g1.resource_id=f.id
			LEFT JOIN fud26_msg m ON f.last_post_id=m.id
			LEFT JOIN fud26_users u ON u.id=m.poster_id '.
			(_uid ? ' LEFT JOIN fud26_forum_read fr ON fr.forum_id=f.id AND fr.user_id='._uid.' LEFT JOIN fud26_mod mo ON mo.user_id='._uid.' AND mo.forum_id=f.id LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id' : '').
			((!$is_a || $cat_id) ?  ' WHERE ' : '') .
			($is_a ? '' : (_uid ? ' mo.id IS NOT NULL OR (CASE WHEN g2.group_cache_opt IS NULL THEN g1.group_cache_opt ELSE g2.group_cache_opt END' : ' (g1.group_cache_opt').' & 1)>0') .
			($cat_id ? ($is_a ? '' : ' AND ') . ' v.c IN('.implode(',', ($cf = $cidxc[$cat_id][5])).') ' : '').' ORDER BY v.id');

	$post_count = $thread_count = $last_msg_id = $cat = 0;
	while ($r = db_rowarr($c)) {
		/* increase thread & post count */
		$post_count += $r[12];
		$thread_count += $r[13];

		$cid = (int) $r[5];

		if ($cat != $cid) {
			if ($cbuf) { /* if previous category was using compact view, print forum row */
				if (empty($collapse[$i[4]])) { /* only show if parent is not collapsed as well */
					$forum_list_table_data .= '<tr class="RowStyleB"><td colspan="6">Available Forums:'.$cbuf.'</td></tr>';
				}
				$cbuf = '';
			}

			while (list($k, $i) = each($cidxc)) {
				if ($cat_id && !isset($cf[$k])) {
					continue;
				}

				/* if parent category is collapsed, hide child category */
				if ($i[4] && !empty($collapse[$i[4]])) {
					$collapse[$k] = 1;
					$cat = $k;
					if ($k == $cid) {
						break;
					} else {
						continue;
					}
				}

				if ($i[3] & 1 && $k != $cat_id && !($i[3] & 4)) {
					if (!isset($collapse[$k])) {
						$collapse[$k] = !($i[3] & 2);
					}
					$forum_list_table_data .= '<tr><td colspan="6" class="CatDesc" style="padding-left: '.($i[0] ? $i[0] * 20 : '0').'px"><a href="index.php?t=index&amp;cat='.$cat_id.'&amp;c='.url_tog_collapse($k, $cs).'&amp;'._rsid.'" class="CatLink" title="'.(!empty($collapse[$k]) ? 'Maximize Category' : 'Minimize Category' ) .'">'.(!empty($collapse[$k]) ? '<img src="theme/default/images/max.png" alt="" />' : '<img src="theme/default/images/min.png" alt="" />' ) .'</a> <a href="index.php?t=index&amp;cat='.$k.'&amp;c='.$cs.'&amp;'._rsid.'" class="CatLink">'.$i[1].'</a> '.$i[2].'</td></tr>';
				} else {
					if ($i[3] & 4) {
						++$i[0];
					}
					$forum_list_table_data .= '<tr><td class="CatDesc CatLockPad" colspan="6" style="padding-left: '.($i[0] ? $i[0] * 20 : '0').'px"><span class="CatLockedName"><a href="index.php?t=index&amp;cat='.$k.'&amp;c='.$cs.'&amp;'._rsid.'" class="CatLink">'.$i[1].'</a></span> '.$i[2].'</td></tr>';
				}
			
				if ($k == $cid) {
					break;
				}
			}
			$cat = $cid;
		}

		/* compact view check */
		if ($r[17]) {
			$cbuf .= '&nbsp;&nbsp;<a href="index.php?t='.t_thread_view.'&amp;frm_id='.$r[7].'&amp;'._rsid.'">'.$r[10].'</a>';
			continue;
		}

		if (!empty($collapse[$cid]) && $cat_id != $cid) {
			continue;
		}

		if (!($r[16] & 2) && !$is_a && !$r[15]) { /* visible forum with no 'read' permission */
			$forum_list_table_data .= '<tr>
	<td class="RowStyleA" colspan=6>'.$r[10].($r[11] ? '<br />'.$r[11] : '').'</td>
</tr>';
			continue;
		}

		/* code to determine the last post id for 'latest' forum message */
		if ($r[8] > $last_msg_id) {
			$last_msg_id = $r[8];
		}

		if (!_uid) { /* anon user */
			$forum_read_indicator = '<img title="Only registered forum members can track read &amp; unread messages" src="theme/default/images/existing_content.png" alt="Only registered forum members can track read &amp; unread messages" />';
		} else if ($r[14] < $r[2] && $usr->last_read < $r[2]) {
			$forum_read_indicator = '<img title="New messages" src="theme/default/images/new_content.png" alt="New messages" />';
		} else {
			$forum_read_indicator = '<img title="No new messages" src="theme/default/images/existing_content.png" alt="No new messages" />';
		}

		if ($r[9] && ($mods = unserialize($r[9]))) {
			$moderators = '';
			foreach($mods as $k => $v) {
				$moderators .= '<a href="index.php?t=usrinfo&amp;id='.$k.'&amp;'._rsid.'">'.$v.'</a> &nbsp;';
			}
			$moderators = '<div class="TopBy"><b>Moderator(s):</b> '.$moderators.'</div>';
		} else {
			$moderators = '&nbsp;';
		}

		$forum_list_table_data .= '<tr>
	<td class="RowStyleA wo">'.($r[6] ? '<img src="'.$r[6].'" alt="Forum Icon" />' : '&nbsp;' ) .'</td>
	<td class="RowStyleB wo">'.$forum_read_indicator.'</td>
	<td class="RowStyleA wa"><a href="index.php?t='.t_thread_view.'&amp;frm_id='.$r[7].'&amp;'._rsid.'" class="big">'.$r[10].'</a>'.($r[11] ? '<br />'.$r[11] : '').$moderators.'</td>
	<td class="RowStyleB ac">'.$r[12].'</td>
	<td class="RowStyleB ac">'.$r[13].'</td>
	<td class="RowStyleA ac nw">'.($r[8] ? '<span class="DateText">'.strftime("%a, %d %B %Y", $r[2]).'</span><br />By: '.($r[3] ? '<a href="index.php?t=usrinfo&amp;id='.$r[3].'&amp;'._rsid.'">'.$r[4].'</a>' : $GLOBALS['ANON_NICK'].'' ) .' <a href="index.php?t='.d_thread_view.'&amp;goto='.$r[8].'&amp;'._rsid.'#msg_'.$r[8].'"><img title="'.$r[0].'" src="theme/default/images/goto.gif" alt="'.$r[0].'" /></a>' : 'n/a' ) .'</td>
</tr>';
	}

	if ($cbuf) { /* if previous category was using compact view, print forum row */
		$forum_list_table_data .= '<tr class="RowStyleB"><td colspan="6">Available Forums:'.$cbuf.'</td></tr>';
	}

function rebuild_stats_cache($last_msg_id)
{
	$tm_expire = __request_timestamp__ - ($GLOBALS['LOGEDIN_TIMEOUT'] * 60);

	list($obj->last_user_id, $obj->user_count) = db_saq('SELECT MAX(id), count(*)-1 FROM fud26_users');

	$obj->online_users_anon	= q_singleval('SELECT count(*) FROM fud26_ses s WHERE time_sec>'.$tm_expire.' AND user_id>2000000000');
	$obj->online_users_hidden = q_singleval('SELECT count(*) FROM fud26_ses s INNER JOIN fud26_users u ON u.id=s.user_id WHERE s.time_sec>'.$tm_expire.' AND (u.users_opt & 32768) > 0');
	$obj->online_users_reg = q_singleval('SELECT count(*) FROM fud26_ses s INNER JOIN fud26_users u ON u.id=s.user_id WHERE s.time_sec>'.$tm_expire.' AND (u.users_opt & 32768)=0');
	$c = uq('SELECT u.id, u.alias, u.users_opt, u.custom_color FROM fud26_ses s INNER JOIN fud26_users u ON u.id=s.user_id WHERE s.time_sec>'.$tm_expire.' AND (u.users_opt & 32768)=0 ORDER BY s.time_sec DESC LIMIT '.$GLOBALS['MAX_LOGGEDIN_USERS']);
	$obj->online_users_text = array();
	while ($r = db_rowarr($c)) {
		$obj->online_users_text[$r[0]] = draw_user_link($r[1], $r[2], $r[3]);
	}

	q('UPDATE fud26_stats_cache SET
		cache_age='.__request_timestamp__.',
		last_user_id='.(int)$obj->last_user_id.',
		user_count='.(int)$obj->user_count.',
		online_users_anon='.(int)$obj->online_users_anon.',
		online_users_hidden='.(int)$obj->online_users_hidden.',
		online_users_reg='.(int)$obj->online_users_reg.',
		online_users_text='.strnull(addslashes(serialize($obj->online_users_text))));

	$obj->last_user_alias = q_singleval('SELECT alias FROM fud26_users WHERE id='.$obj->last_user_id);
	$obj->last_msg_subject = q_singleval('SELECT subject FROM fud26_msg WHERE id='.$last_msg_id);

	return $obj;
}

$logedin = $forum_info = '';

if ($FUD_OPT_1 & 1073741824 || $FUD_OPT_2 & 16) {
	if (!($st_obj = db_sab('SELECT sc.*,m.subject AS last_msg_subject, u.alias AS last_user_alias FROM fud26_stats_cache sc INNER JOIN fud26_users u ON u.id=sc.last_user_id INNER JOIN fud26_msg m ON m.id='.$last_msg_id.' WHERE sc.cache_age>'.(__request_timestamp__ - $STATS_CACHE_AGE)))) {
		$st_obj =& rebuild_stats_cache($last_msg_id);
	} else if ($st_obj->online_users_text) {
		$st_obj->online_users_text = unserialize($st_obj->online_users_text);
	}

	if ($FUD_OPT_1 & 1073741824) {
		if (!empty($st_obj->online_users_text)) {
			foreach($st_obj->online_users_text as $k => $v) {
				$logedin .= '<a href="index.php?t=usrinfo&amp;id='.$k.'&amp;'._rsid.'">'.$v.'</a> ';
			}
		}
		$logedin = '<tr><th class="wa">Logged in users list '.($FUD_OPT_1 & 536870912 ? '[<a href="index.php?t=actions&amp;'._rsid.'" class="thLnk">show what people are doing</a>] [<a href="index.php?t=online_today&amp;'._rsid.'" class="thLnk">Today&#39;s Visitors</a>]' : '' ) .'</th></tr>
<tr><td class="RowStyleA">
<span class="SmallText">There are <b>'.$st_obj->online_users_reg.'</b> members(s), <b>'.$st_obj->online_users_hidden.'</b> invisible members and <b>'.$st_obj->online_users_anon.'</b> guest(s) visiting this board.&nbsp;&nbsp;&nbsp;<span class="adminColor">[Administrator]</span>&nbsp;&nbsp;<span class="modsColor">[Moderator]</span></span><br />
'.$logedin.'
</td></tr>';
	}
	if ($FUD_OPT_2 & 16) {
		$forum_info = '<tr><td class="RowStyleB SmallText">
Our users have posted a total of <b>'.$post_count.'</b> messages inside <b>'.$thread_count.'</b> topics.<br />
We have <b>'.$st_obj->user_count.'</b> registered user(s).<br />
The newest registered user is <a href="index.php?t=usrinfo&amp;id='.$st_obj->last_user_id.'&amp;'._rsid.'"><b>'.$st_obj->last_user_alias.'</b></a>'.($last_msg_id ? '<br />Last message on the forum: <a href="index.php?t='.d_thread_view.'&amp;goto='.$last_msg_id.'&amp;'._rsid.'#msg_'.$last_msg_id.'"><b>'.$st_obj->last_msg_subject.'</b></a>' : '' ) .'</td></tr>';
	}
}if ($FUD_OPT_2 & 2 || $is_a) {
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
<?php echo (_uid ? '<span class="GenText">Welcome <b>'.$usr->alias.'</b>, your last visit was on '.strftime("%a, %d %B %Y %H:%M", $usr->last_visit).'</span><br />' : ''); ?>
<span class="GenText fb">Show:</span> <a href="index.php?t=selmsg&amp;date=today&amp;<?php echo _rsid; ?>&amp;frm_id=<?php echo $frm->id; ?>&amp;th=<?php echo $th; ?>" title="Show all messages that were posted today">Today's Messages</a>&nbsp;<?php echo (_uid ? '<b>::</b> <a href="index.php?t=selmsg&amp;unread=1&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all unread messages">Unread Messages</a>&nbsp;' : '' ) .(!$th ? '<b>::</b> <a href="index.php?t=selmsg&amp;reply_count=0&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all messages, which have no replies">Unanswered Messages</a>' : ''); ?> <b>::</b> <a href="index.php?t=polllist&amp;<?php echo _rsid; ?>">Show Polls</a> <b>::</b> <a href="index.php?t=mnav&amp;<?php echo _rsid; ?>">Message Navigator</a><br /><img src="blank.gif" alt="" height=2 /><?php echo $admin_cp; ?>
<?php echo $cat_path; ?>
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr>
	<th colspan=3 class="wa">Forum</th>
	<th nowrap>Messages</th>
	<th nowrap>Topics</th>
	<th nowrap>Last message</th>
</tr>
<?php echo $forum_list_table_data; ?>
</table>
<?php echo (_uid ? '<div class="SmallText ar">[<a href="index.php?t=markread&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'&amp;cat='.$cat_id.'" title="All your unread messages will be marked as read">mark all messages read</a>]</div>' : ''); ?>
<?php echo (__fud_real_user__ ? '' : '<table class="wa" border=0 cellspacing=0 cellpadding=0><tr><td align="right">
<form name="quick_login_form" method="post" action="index.php?t=login"'.($GLOBALS['FUD_OPT_3'] & 256 ? ' autocomplete="off"' : '').'>'._hs.'
<table border=0 cellspacing=0 cellpadding=3>
<tr class="SmallText">
	<td>Login<br /><input class="SmallText" type="text" name="quick_login" size=18></td>
	<td>Password<br /><input class="SmallText" type="password" name="quick_password" size=18></td>
	'.($FUD_OPT_1 & 128 ? '<td>&nbsp;<br /><input type="checkbox" checked name="quick_use_cookies" value="1"> Use Cookies? </td>' : '' )  .'
	<td>&nbsp;<br /><input type="submit" class="button" name="quick_login_submit" value="Login"></td>
</tr>
</table></form></td></tr></table>'); ?>
<?php echo ($logedin || $forum_info ? '<br />
<table cellspacing="1" cellpadding="2" class="ContentTable">
'.$logedin.'
'.$forum_info.'
</table>' : ''); ?>
<br /><fieldset>
<legend>Legend</legend>
<img src="theme/default/images/new_content.png" alt="New messages since last read" /> New messages since last read&nbsp;&nbsp;
<img src="theme/default/images/existing_content.png" alt="No new messages since last read" /> No new messages since last read
</fieldset>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>