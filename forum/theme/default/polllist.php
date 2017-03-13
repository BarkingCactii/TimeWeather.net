<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: polllist.php.t,v 1.33 2005/02/27 02:21:36 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

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
}function pager_replace(&$str, $s, $c)
{
	$str = str_replace(array('%s', '%c'), array($s, $c), $str);
}

function tmpl_create_pager($start, $count, $total, $arg, $suf='', $append=1, $js_pager=0)
{
	if (!$count) {
		$count =& $GLOBALS['POSTS_PER_PAGE'];
	}
	if ($total <= $count) {
		return;
	}

	if ($GLOBALS['FUD_OPT_2'] & 32768 && (!empty($_SERVER['PATH_INFO']) || strpos($arg, '?') === false)) {
		if (!$suf) {
			$suf = '/';
		}
		$upfx = '';
	} else {
		$upfx = '&amp;start=';
	}

	$cur_pg = ceil($start / $count);
	$ttl_pg = ceil($total / $count);

	$page_pager_data = '';

	if (($page_start = $start - $count) > -1) {
		if ($append) {
			$page_first_url = $arg . $upfx . $suf;
			$page_prev_url = $arg . $upfx . $page_start . $suf;
		} else {
			$page_first_url = $page_prev_url = $arg;
			pager_replace($page_first_url, 0, $count);
			pager_replace($page_prev_url, $page_start, $count);
		}

		$page_pager_data .= !$js_pager ? '&nbsp;<a href="'.$page_first_url.'" class="PagerLink">&laquo;</a>&nbsp;&nbsp;<a href="'.$page_prev_url.'" accesskey="p" class="PagerLink">&lt;</a>&nbsp;&nbsp;' : '&nbsp;<a href="javascript://" onClick="'.$page_first_url.'" class="PagerLink">&laquo;</a>&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_prev_url.'" class="PagerLink">&lt;</a>&nbsp;&nbsp;';
	}

	$mid = ceil($GLOBALS['GENERAL_PAGER_COUNT'] / 2);

	if ($ttl_pg > $GLOBALS['GENERAL_PAGER_COUNT']) {
		if (($mid + $cur_pg) >= $ttl_pg) {
			$end = $ttl_pg;
			$mid += $mid + $cur_pg - $ttl_pg;
			$st = $cur_pg - $mid;
		} else if (($cur_pg - $mid) <= 0) {
			$st = 0;
			$mid += $mid - $cur_pg;
			$end = $mid + $cur_pg;
		} else {
			$st = $cur_pg - $mid;
			$end = $mid + $cur_pg;
		}

		if ($st < 0) {
			$start = 0;
		}
		if ($end > $ttl_pg) {
			$end = $ttl_pg;
		}
		if ($end - $start > $GLOBALS['GENERAL_PAGER_COUNT']) {
			$end = $start + $GLOBALS['GENERAL_PAGER_COUNT'];
		}
	} else {
		$end = $ttl_pg;
		$st = 0;
	}

	while ($st < $end) {
		if ($st != $cur_pg) {
			$page_start = $st * $count;
			if ($append) {
				$page_page_url = $arg . $upfx . $page_start . $suf;
			} else {
				$page_page_url = $arg;
				pager_replace($page_page_url, $page_start, $count);
			}
			$st++;
			$page_pager_data .= !$js_pager ? '<a href="'.$page_page_url.'" class="PagerLink">'.$st.'</a>&nbsp;&nbsp;' : '<a href="javascript://" onClick="'.$page_page_url.'" class="PagerLink">'.$st.'</a>&nbsp;&nbsp;';
		} else {
			$st++;
			$page_pager_data .= !$js_pager ? $st.'&nbsp;&nbsp;' : $st.'&nbsp;&nbsp;';
		}
	}

	$page_pager_data = substr($page_pager_data, 0 , strlen((!$js_pager ? '&nbsp;&nbsp;' : '&nbsp;&nbsp;')) * -1);

	if (($page_start = $start + $count) < $total) {
		$page_start_2 = ($st - 1) * $count;
		if ($append) {
			$page_next_url = $arg . $upfx . $page_start . $suf;
			$page_last_url = $arg . $upfx . $page_start_2 . $suf;
		} else {
			$page_next_url = $page_last_url = $arg;
			pager_replace($page_next_url, $upfx . $page_start, $count);
			pager_replace($page_last_url, $upfx . $page_start_2, $count);
		}
		$page_pager_data .= !$js_pager ? '&nbsp;&nbsp;<a href="'.$page_next_url.'" accesskey="n" class="PagerLink">&gt;</a>&nbsp;&nbsp;<a href="'.$page_last_url.'" class="PagerLink">&raquo;</a>' : '&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_next_url.'" class="PagerLink">&gt;</a>&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_last_url.'" class="PagerLink">&raquo;</a>';
	}

	return !$js_pager ? '<span class="SmallText fb">Pages ('.$ttl_pg.'): ['.$page_pager_data.']</span>' : '<span class="SmallText fb">Pages ('.$ttl_pg.'): ['.$page_pager_data.']</span>';
}

	if (!empty($_GET['goto'])) {
		$pl_view = empty($_GET['vote']) ? 0 : (int)$_GET['goto'];
		$mid = q_singleval("SELECT id FROM fud26_msg WHERE poll_id=".(int)$_GET['goto']);
		/* PATH_INFO is handled via /pv/ */
		header("Location: http://timeweather.net/forum/index.php?t=".d_thread_view."&goto=".$mid."&pl_view=".$pl_view."&"._rsidl.'#msg_'.$mid);
		return;
	}

	ses_update_status($usr->sid, '<a href="index.php?t=polllist">Reviewing Available Polls</a>');

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
	}

	if (!isset($_GET['oby'])) {
		$_GET['oby'] = 'DESC';
	}
	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}
	if (isset($_GET['uid']) && ($uid = (int)$_GET['uid'])) {
		$usr_lmt = ' WHERE p.owner='.$uid;
	} else {
		$uid = $usr_lmt = '';
	}

	if (!$is_a) {
		$usr_lmt = $usr_lmt . ($usr_lmt ? ' AND ' : ' WHERE ') . ' (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0)';
	}

	if ($_GET['oby'] == 'ASC') {
		$oby = 'ASC';
		$oby_rev_val = 'DESC';
	} else {
                $oby = 'DESC';
		$oby_rev_val = 'ASC';
	}

	$poll_entries = '';
	$c = uq('SELECT /*!40000 SQL_CALC_FOUND_ROWS */
			p.owner, p.name, p.creation_date, p.id, p.max_votes, p.total_votes,
			u.alias, u.alias AS login, (u.last_visit + '.($LOGEDIN_TIMEOUT * 60).') AS last_visit, u.users_opt,
			'.($is_a ? '1' : 'mm.id').' AS md,
			(CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco
			FROM fud26_poll p
			INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=p.forum_id
			INNER JOIN fud26_forum f ON p.forum_id=f.id
			INNER JOIN fud26_cat c ON c.id=f.cat_id
			LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=p.forum_id
			LEFT JOIN fud26_mod mm ON mm.forum_id=p.forum_id AND mm.user_id='._uid.'
			LEFT JOIN fud26_users u ON u.id=p.owner
			LEFT JOIN fud26_poll_opt_track pot ON pot.poll_id=p.id AND pot.user_id='._uid.
			$usr_lmt.' ORDER BY p.creation_date '.$oby.' LIMIT '.qry_limit($POLLS_PER_PAGE, $start));

	while ($obj = db_rowobj($c)) {
		$view_res_lnk = $obj->total_votes ? '<b>::</b> <a href="index.php?t=polllist&amp;goto='.$obj->id.'&amp;vote=1&amp;'._rsid.'#msg_'.$obj->id.'">View Results</a>' : '';
		if (!$obj->total_votes) {
			$obj->total_votes = '0';
		}
		if ($obj->owner && (!($obj->users_opt & 32768) || $is_a) && $FUD_OPT_2 & 32) {
			$online_indicator = $obj->last_visit > __request_timestamp__ ? '<img src="theme/default/images/online.png" title="'.$obj->login.' is currently online" alt="'.$obj->login.' is currently online" />&nbsp;' : '<img src="theme/default/images/offline.png" title="'.$obj->login.'  is currently offline" alt="'.$obj->login.'  is currently offline" />&nbsp;';
		} else {
			$online_indicator = '';
		}
		$poll_entries .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'">
	<td class="wa">'.$obj->name.'</td>
	<td class="nw">'.strftime("%a, %d %B %Y %H:%M", $obj->creation_date).'</td>
	<td class="nw">'.$online_indicator.'<a href="index.php?t=usrinfo&amp;id='.$obj->owner.'&amp;'._rsid.'">'.$obj->alias.'</a></td>
	<td class="nw ac">'.$obj->total_votes.'</td>
	<td class="nw ac"> <b>::</b> <a href="index.php?t=polllist&amp;goto='.$obj->id.'&amp;'._rsid.'#msg_'.$obj->id.'">Vote</a> '.$view_res_lnk.' <b>::</b></td>
</tr>';
	}

	if (($ttl = (int) q_singleval("SELECT /*!40000 FOUND_ROWS(), */ -1")) < 0) {
		$ttl = (int) q_singleval('SELECT count(*)
				FROM fud26_poll p
				INNER JOIN fud26_forum f ON p.forum_id=f.id
				INNER JOIN fud26_cat c ON c.id=f.cat_id
				LEFT JOIN fud26_mod mm ON mm.forum_id=p.forum_id AND mm.user_id='._uid.'
				INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=p.forum_id
				LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=p.forum_id'.$usr_lmt);
	}

	$pager = '';
	if ($ttl > $POLLS_PER_PAGE) {
		if ($FUD_OPT_2 & 32768) {
			$pager = tmpl_create_pager($start, $POLLS_PER_PAGE, $ttl, 'index.php/pl/'.$uid.'/', '/' . $oby . '/' . _rsid);
		} else {
			$pager = tmpl_create_pager($start, $POLLS_PER_PAGE, $ttl, 'index.php?t=polllist&amp;oby='.$oby.'&amp;uid='.$uid);
		}
	} else if (!$ttl) {
		$poll_entries = '<tr><td colspan="5" class="ac">There are no accessible polls.</td></tr>';
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
<br /><?php echo $admin_cp; ?><p>
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr>
	<th>Poll Name</th>
	<th class="nw"><a class="thLnk" href="index.php?t=polllist&amp;start=<?php echo $start; ?>&amp;oby=<?php echo $oby_rev_val; ?>&amp;<?php echo _rsid; ?>">Created On</a></th>
	<th class="nw">Created By</th>
	<th class="nw ac">Total Votes</th>
	<th class="nw ac">Action</th>
</tr>
<?php echo $poll_entries; ?>
</table>
<p>
<?php echo $pager; ?>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>