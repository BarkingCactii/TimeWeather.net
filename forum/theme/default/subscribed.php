<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: subscribed.php.t,v 1.25 2005/02/27 02:58:17 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function is_forum_notified($user_id, $forum_id)
{
	return q_singleval('SELECT id FROM fud26_forum_notify WHERE forum_id='.$forum_id.' AND user_id='.$user_id);
}

function forum_notify_add($user_id, $forum_id)
{
	db_li('INSERT INTO fud26_forum_notify (user_id, forum_id) VALUES ('.$user_id.', '.$forum_id.')', $ret);
}

function forum_notify_del($user_id, $forum_id)
{
	q('DELETE FROM fud26_forum_notify WHERE forum_id='.$forum_id.' AND user_id='.$user_id);
}function is_notified($user_id, $thread_id)
{
	return q_singleval('SELECT * FROM fud26_thread_notify WHERE thread_id='.$thread_id.' AND user_id='.$user_id);
}

function thread_notify_add($user_id, $thread_id)
{
	db_li('INSERT INTO fud26_thread_notify (user_id, thread_id) VALUES ('.$user_id.', '.$thread_id.')', $ret);
}

function thread_notify_del($user_id, $thread_id)
{
	q('DELETE FROM fud26_thread_notify WHERE thread_id='.$thread_id.' AND user_id='.$user_id);
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

	/* delete forum subscription */
	if (isset($_GET['frm_id']) && ($_GET['frm_id'] = (int)$_GET['frm_id']) && sq_check(0, $usr->sq)) {
		forum_notify_del(_uid, $_GET['frm_id']);
	}

	/* delete thread subscription */
	if (isset($_GET['th']) && ($_GET['th'] = (int)$_GET['th']) && sq_check(0, $usr->sq)) {
		thread_notify_del(_uid, $_GET['th']);
	}

	ses_update_status($usr->sid, 'Browsing own subscriptions');

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

	/* fetch a list of all the accessible forums */
	$lmt = '';
	if (!$is_a) {
		$c = uq('SELECT g1.resource_id
				FROM fud26_group_cache g1
				LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g1.resource_id=g2.resource_id
				LEFT JOIN fud26_mod m ON m.forum_id=g1.resource_id AND m.user_id='._uid.'
				WHERE g1.user_id=2147483647 AND (m.id IS NULL AND ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2)=0)');
		while ($r = db_rowarr($c)) {
			$lmt .= $r[0] . ',';
		}
		if ($lmt) {
			$lmt[strlen($lmt) - 1] = ' ';
			$lmt = ' AND forum_id NOT IN('.$lmt.') ';
		} else {
			$lmt = ' AND forum_id NOT IN(0) ';
		}
	}

	$c = uq('SELECT f.id, f.name FROM fud26_forum_notify fn LEFT JOIN fud26_forum f ON fn.forum_id=f.id WHERE fn.user_id='._uid.' '.$lmt.' ORDER BY f.last_post_id DESC');

	$subscribed_thread_data = $subscribed_forum_data = '';
	while (($r = db_rowarr($c))) {
		$subscribed_forum_data .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw"><a href="index.php?t=subscribed&amp;frm_id='.$r[0].'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">Unsubscribe</a><td class="wa"><a href="index.php?t='.t_thread_view.'&amp;frm_id='.$r[0].'&amp;'._rsid.'">'.$r[1].'</a></td></td></tr>';
	}

	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}

	$c = uq('SELECT /*!40000 SQL_CALC_FOUND_ROWS */ t.id, m.subject, f.name FROM fud26_thread_notify tn INNER JOIN fud26_thread t ON tn.thread_id=t.id INNER JOIN fud26_forum f ON f.id=t.forum_id INNER JOIN fud26_msg m ON t.root_msg_id=m.id WHERE tn.user_id='._uid.' '.$lmt.' ORDER BY t.last_post_id DESC LIMIT '.qry_limit($THREADS_PER_PAGE, $start));

	while (($r = db_rowarr($c))) {
		$subscribed_thread_data .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw"><a href="index.php?t=subscribed&amp;th='.$r[0].'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">Unsubscribe</a></td><td class="wa">'.$r[2].' &raquo; <a href="index.php?t='.d_thread_view.'&amp;th='.$r[0].'&amp;'._rsid.'">'.$r[1].'</a></td></tr>';
	}

	/* Since a person can have MANY subscribed threads, we need a pager & for the pager we need a entry count */
	if (($total = (int) q_singleval("SELECT /*!40000 FOUND_ROWS(), */ -1")) < 0) {
		$total = q_singleval('SELECT count(*) FROM fud26_thread_notify tn LEFT JOIN fud26_thread t ON tn.thread_id=t.id INNER JOIN fud26_msg m ON t.root_msg_id=m.id WHERE tn.user_id='._uid.' '.$lmt);
	}

	if ($FUD_OPT_2 & 32768) {
		$pager = tmpl_create_pager($start, $THREADS_PER_PAGE, $total, 'index.php/sl/start/', '/'._rsid.'#fff');
	} else {
		$pager = tmpl_create_pager($start, $THREADS_PER_PAGE, $total, 'index.php?t=subscribed&a=1&'._rsid, '#fff');
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
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=2>Subscribed Forums<a name="fff"></a></th></tr>
<?php echo ($subscribed_forum_data ? $subscribed_forum_data.'' : '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td colspan=2>No subscribed forums</td></tr>'); ?>
<tr><th colspan=2>Subscribed Topics</th></tr>
<?php echo ($subscribed_thread_data ? $subscribed_thread_data.'' : '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td colspan=2>No subscribed topics</td></tr>'); ?>
</table>
<?php echo $pager; ?>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>