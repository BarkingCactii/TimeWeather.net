<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: mnav.php.t,v 1.26 2005/03/18 01:58:51 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}$GLOBALS['__revfs'] = array('&quot;', '&lt;', '&gt;', '&amp;');
$GLOBALS['__revfd'] = array('"', '<', '>', '&');

function reverse_fmt($data)
{
	$s = $d = array();
	foreach ($GLOBALS['__revfs'] as $k => $v) {
		if (strpos($data, $v) !== false) {
			$s[] = $v;
			$d[] = $GLOBALS['__revfd'][$k];
		}
	}

	return $s ? str_replace($s, $d, $data) : $data;
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
}function tmpl_draw_select_opt($values, $names, $selected)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (count($vls) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values inside a select<br>\n");
	}

	$options = '';
	foreach ($vls as $k => $v) {
		$options .= '<option value="'.$v.'"'.($v == $selected ? ' selected' : '' )  .'>'.$nms[$k].'</option>';
	}

	return $options;
}function register_fp($id)
{
	if (!isset($GLOBALS['__MSG_FP__'][$id])) {
		$GLOBALS['__MSG_FP__'][$id] = fopen($GLOBALS['MSG_STORE_DIR'].'msg_'.$id, 'rb');
	}

	return $GLOBALS['__MSG_FP__'][$id];
}

function read_msg_body($off, $len, $file_id)
{
	if (!$len) {
		return;
	}

	$fp = register_fp($file_id);
	fseek($fp, $off);
	return fread($fp, $len);
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

	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}
	$forum_limiter = isset($_GET['forum_limiter']) ? $_GET['forum_limiter'] : '';
	$rng = isset($_GET['rng']) ? (float) $_GET['rng'] : 1;
	$rng2 = isset($_GET['rng2']) ? (float) $_GET['rng2'] : 0;
	$unit = isset($_GET['u']) ? (int) $_GET['u'] : 86400;
	$ppg = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;

	require $GLOBALS['FORUM_SETTINGS_PATH'].'cat_cache.inc';

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
}/* draw search engine selection boxes */
if ($is_a) {
	$c = uq('SELECT f.id, f.name, c.id FROM fud26_fc_view v INNER JOIN fud26_forum f ON f.id=v.f INNER JOIN fud26_cat c ON f.cat_id=c.id ORDER BY v.id');
} else {
	$c = uq('SELECT f.id, f.name, c.id
			FROM fud26_fc_view v
			INNER JOIN fud26_forum f ON f.id=v.f
			INNER JOIN fud26_cat c ON f.cat_id=c.id
			INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
			LEFT JOIN fud26_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
			LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
			WHERE mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 1) > 0
			ORDER BY v.id');
}
$oldc = $forum_limit_data = ''; $g = $f = array();
if ($forum_limiter) {
	if ($forum_limiter{0} != 'c') {
		$f[$forum_limiter] = 1;
	} else {
		$g[(int)ltrim($forum_limiter, 'c')] = 1;
	}
}

while ($r = db_rowarr($c)) {
	if ($oldc != $r[2]) {
		while (list($k, $i) = each($cat_cache)) {
			$forum_limit_data .= '<option value="c'.$k.'"'.(isset($g[$k]) ? ' selected' : '').'>- '.($tabw = ($i[0] ? str_repeat('&nbsp;&nbsp;&nbsp;', $i[0]) : '')).$i[1].'</option>';
			if ($k == $r[2]) {
				break;
			}
		}
		$oldc = $r[2];
	}
	$forum_limit_data .= '<option value="'.$r[0].'"'.(isset($f[$r[0]]) ? ' selected' : '').'>'.$tabw.'&nbsp;&nbsp;&nbsp;'.$r[1].'</option>';
}

/* user has no permissions to any forum, so as far as they are concerned the search is disabled */
if (!$forum_limit_data) {
	std_error('disabled');
}

function trim_body($body)
{
	/* remove stuff in quotes */
	while (($p = strpos($body, '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>')) !== false) {
		if (($pos = strpos($body, '<br></td></tr></table>', $p)) === false) {
			$pos = strpos($body, '<br /></td></tr></table>', $p);
			if ($pos === false) {
				break;
			}
			$e = $pos + strlen('<br /></td></tr></table>');
		} else {
			$e = $pos + strlen('<br></td></tr></table>');
		}
		$body = substr($body, 0, $p) . substr($body, $e);
	}

	$body = strip_tags($body);
	if (strlen($body) > $GLOBALS['MNAV_MAX_LEN']) {
		$body = substr($body, 0, $GLOBALS['MNAV_MAX_LEN']) . '...';
	}
	return $body;
}

	$TITLE_EXTRA = ': Message Navigator';

	ses_update_status($usr->sid, 'Browsing Messages using <a href="index.php?t=mnav">Message Navigator</a>');

	if ($forum_limiter) {
		if ($forum_limiter[0] != 'c') {
			$qry_lmt = ' AND f.id=' . (int)$forum_limiter . ' ';
		} else {
			$qry_lmt = ' AND c.id=' . (int)substr($forum_limiter, 1) . ' ';
		}
	} else {
		$qry_lmt = '';
	}

	$mnav_time_unit = tmpl_draw_select_opt("60\n3600\n86400\n604800\n2635200", "Minute(s)\nHour(s)\nDay(s)\nWeek(s)\nMonth(s)", $unit);

	$mnav_pager = '';
	if (!$rng) {
		$rng = ''; $unit = 86400;
		$mnav_data = '<br />
<div class="ctb">
<table cellspacing="1" cellpadding="2" class="mnavWarnTbl">
<tr>
	<td class="GenTextRed">You must enter a valid date range. This value can contain a decimal point, (0.12) but it must be greater than zero.</td>
</tr>
</table>
</div>';
	} else if ($unit <= 0) {
		$rng = ''; $unit = 86400;
		$mnav_data = '<br />
<div class="ctb">
<table cellspacing="1" cellpadding="2" class="mnavWarnTbl">
<tr>
	<td class="GenTextRed">You must specify a valid time unit.</td>
</tr>
</table>
</div>';
	} else if (($mage = round($rng * $unit)) > ($MNAV_MAX_DATE * 86400) && $MNAV_MAX_DATE > 0) {
		$mnav_data = '<br />
<div class="ctb">
<table cellspacing="1" cellpadding="2" class="mnavWarnTbl">
<tr>
	<td class="GenTextRed">The date range you specified is larger then the one allowed by the administrator. Try a smaller range of dates.</td>
</tr>
</table>
</div>';
	} else if (isset($_GET['u'])) {
		$tm = __request_timestamp__ - $mage;

		if ($rng2 > 0) {
			$date_limit = ' AND m.post_stamp < '.(__request_timestamp__ - ($rng2 * $unit));
		} else {
			$date_limit = '';
		}

		$c = uq('SELECT /*!40000 SQL_CALC_FOUND_ROWS */ u.alias, f.name AS forum_name, f.id AS forum_id,
				m.poster_id, m.id, m.thread_id, m.subject, m.poster_id, m.foff, m.length, m.post_stamp, m.file_id, m.icon
				FROM fud26_msg m
				INNER JOIN fud26_thread t ON m.thread_id=t.id
				INNER JOIN fud26_forum f ON t.forum_id=f.id
				INNER JOIN fud26_cat c ON f.cat_id=c.id
				INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
				LEFT JOIN fud26_users u ON m.poster_id=u.id
				LEFT JOIN fud26_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
				LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
			WHERE
				m.post_stamp > '.$tm.' '.$date_limit.' AND m.apr=1 '.$qry_lmt.'
				'.($is_a ? '' : ' AND (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0)').'
				ORDER BY m.thread_id, t.forum_id, m.post_stamp DESC LIMIT '.qry_limit($ppg, $start));

		$oldf = $oldt = 0;
		$mnav_data = '<div class="ctb">
<table cellspacing="0" cellpadding="0" class="ContentTable">';
		while ($r = db_rowobj($c)) {
			if ($oldf != $r->forum_id) {
				$mnav_data .= '<tr><th colspan="3"> Forum: <a class="thLnk" href="index.php?t='.t_thread_view.'&amp;frm_id='.$r->forum_id.'&amp;'._rsid.'"><span class="lg">'.htmlspecialchars($r->forum_name).'</span></a></th></tr>';
				$oldf = $r->forum_id;
			}
			if ($oldt != $r->thread_id) {
				$mnav_data .= '<tr><th class="RowStyleC">&nbsp;&nbsp;&nbsp;</th><th colspan="2"> Topic: <a class="thLnk" href="index.php?t='.d_thread_view.'&amp;goto='.$r->id.'&amp;'._rsid.'#msg_'.$r->id.'">'.$r->subject.'</a></th></tr>';
				$oldt = $r->thread_id;
			}
			$mnav_data .= '<tr><td class="RowStyleC">&nbsp;&nbsp;&nbsp;</td><td class="RowStyleC">&nbsp;&nbsp;&nbsp;</td><td>
<table cellspacing=0 cellpadding=2 border=0 class="mnavMsg">
<tr class="mnavH SmallText">
	<td class="nw al"><a href="index.php?t='.d_thread_view.'&amp;goto='.$r->id.'&amp;'._rsid.'#msg_'.$r->id.'">'.$r->subject.'</a></td>
	<td class="wa ac">Posted By: '.(!empty($r->poster_id) ? '<a href="index.php?t=usrinfo&amp;id='.$r->poster_id.'&amp;'._rsid.'">'.$r->alias.'</a>' : $GLOBALS['ANON_NICK'].'' ) .'</td>
	<td class="nw ar">'.strftime("%a, %d %B %Y %H:%M", $r->post_stamp).'</td>
</tr>
<tr class="mnavM SmallText">
	<td colspan="3">'.trim_body(read_msg_body($r->foff, $r->length, $r->file_id)).' <a href="index.php?t='.d_thread_view.'&amp;goto='.$r->id.'&amp;'._rsid.'#msg_'.$r->id.'">More &raquo;&raquo;</a></td>
</tr>
</table>
</td></tr>';
		}

		if (($total = (int) q_singleval("SELECT /*!40000 FOUND_ROWS(), */ -1")) < 0) {
			$total = q_singleval('SELECT count(*) FROM fud26_msg m
					INNER JOIN fud26_thread t ON m.thread_id=t.id
					INNER JOIN fud26_forum f ON t.forum_id=f.id
					INNER JOIN fud26_cat c ON f.cat_id=c.id
					INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
					LEFT JOIN fud26_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
					LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
				WHERE
					m.post_stamp > '.$tm.' '.$date_limit.' AND m.apr=1 '.$qry_lmt.'
					'.($is_a ? '' : ' AND (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0)'));
		}

		if (!$total) {
			$mnav_data = '<div align="center" class="GenText manvNoRes">There are no messages matching the query.</div>';
		} else {
			$mnav_data .= '</table>
</div>';

			/* handle pager if needed */
			if ($total > $ppg) {
				if ($FUD_OPT_2 & 32768) {
					$mnav_pager = tmpl_create_pager($start, $ppg, $total, 'index.php/ma/'.$rng.'/'.$rng2.'/'.$unit.'/', '/'._rsid);
				} else {
					$mnav_pager = tmpl_create_pager($start, $ppg, $total, 'index.php?t=mnav&amp;rng='.$rng.'&amp;u='.$unit.'&amp;'._rsid.'&amp;forum_limiter='.$forum_limiter.'&mbsp;rng2='.$rng2);
				}
			}
		}
	} else {
		$mnav_data = '';
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
<span class="GenText fb">Show:</span> <a href="index.php?t=selmsg&amp;date=today&amp;<?php echo _rsid; ?>&amp;frm_id=<?php echo $frm->id; ?>&amp;th=<?php echo $th; ?>" title="Show all messages that were posted today">Today's Messages</a>&nbsp;<?php echo (_uid ? '<b>::</b> <a href="index.php?t=selmsg&amp;unread=1&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all unread messages">Unread Messages</a>&nbsp;' : '' ) .(!$th ? '<b>::</b> <a href="index.php?t=selmsg&amp;reply_count=0&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all messages, which have no replies">Unanswered Messages</a>' : ''); ?> <b>::</b> <a href="index.php?t=polllist&amp;<?php echo _rsid; ?>">Show Polls</a> <b>::</b> <a href="index.php?t=mnav&amp;<?php echo _rsid; ?>">Message Navigator</a><br /><img src="blank.gif" alt="" height=2 /><?php echo $admin_cp; ?>

<form name="mnav" method="get" action="index.php"><?php echo _hs; ?><input type="hidden" name="t" value="mnav">
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=4 class="wa">Message Navigator</th></tr>
<tr class="<?php echo alt_var('color_alt','RowStyleA','RowStyleB'); ?>">
	<td class="GenText nw" width="30%">Date range:</td>
	<td class="GenText SmallText">newer than<br /><input tabindex="1" type="text" name="rng" value="<?php echo $rng; ?>" maxlength="10" size="11"></td>
	<td class="GenText SmallText">older than<br /><input tabindex="2" type="text" name="rng2" value="<?php echo $rng2; ?>" maxlength="10" size="11"></td>
	<td class="al vb" width="60%"><select name="u" tabindex="3"><?php echo $mnav_time_unit; ?></select></td></tr>
<tr class="<?php echo alt_var('color_alt','RowStyleA','RowStyleB'); ?>">
	<td class="GenText nw">Only search In:</td>
	<td colspan=3 class="vt">
		<select name="forum_limiter" tabindex="4"><option value="">Search all forums</option>
		<?php echo $forum_limit_data; ?>
		</select>
	</td>
</tr>
<tr class="RowStyleC"><td class="GenText ar" colspan="4"><input type="submit" tabindex="5" class="button" name="btn_submit" value="Begin Search"></td></tr>
</table></form>
<script>
<!--
document.mnav.rng.focus();
//-->
</script>
<br />
<?php echo $mnav_data; ?>
<div align="left"><?php echo $mnav_pager; ?></div>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>