<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: uc.php.t,v 1.7 2004/11/24 19:53:37 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}include $GLOBALS['FORUM_SETTINGS_PATH'] . 'ip_filter_cache';
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'login_filter_cache';
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'email_filter_cache';

function is_ip_blocked($ip)
{
	if (empty($GLOBALS['__FUD_IP_FILTER__'])) {
		return;
	}
	$block =& $GLOBALS['__FUD_IP_FILTER__'];
	list($a,$b,$c,$d) = explode('.', $ip);

	if (!isset($block[$a])) {
		return;
	}
	if (isset($block[$a][$b][$c][$d])) {
		return 1;
	}

	if (isset($block[$a][256])) {
		$t = $block[$a][256];
	} else if (isset($block[$a][$b])) {
		$t = $block[$a][$b];
	} else {
		return;
	}

	if (isset($t[$c])) {
		$t = $t[$c];
	} else if (isset($t[256])) {
		$t = $t[256];
	} else {
		return;
	}

	return (isset($t[$d]) || isset($t[256])) ? 1 : null;
}

function is_login_blocked($l)
{
	foreach ($GLOBALS['__FUD_LGN_FILTER__'] as $v) {
		if (preg_match($v, $l)) {
			return 1;
		}
	}
	return;
}

function is_email_blocked($addr)
{
	if (empty($GLOBALS['__FUD_EMAIL_FILTER__'])) {
		return;
	}
	$addr = strtolower($addr);
	foreach ($GLOBALS['__FUD_EMAIL_FILTER__'] as $k => $v) {
		if (($v && (strpos($addr, $k) !== false)) || (!$v && preg_match($k, $addr))) {
			return 1;
		}
	}
	return;
}

function is_allowed_user(&$usr)
{
	if ($GLOBALS['FUD_OPT_1'] & 1048576 && $usr->users_opt & 262144) {
		error_dialog('ERROR: Your account is not yet confirmed', 'We have not received a confirmation from your parent and/or legal guardian, which would allow you to post messages. If you lost your COPPA form, <a href="index.php?t=coppa_fax&amp;'._rsid.'">click here</a> to see it again.');
	}

	if ($GLOBALS['FUD_OPT_2'] & 1 && !($usr->users_opt & 131072)) {
		std_error('emailconf');
	}

	if ($GLOBALS['FUD_OPT_2'] & 1024 && $usr->users_opt & 2097152) {
		error_dialog('Unverified Account', 'The administrator had chosen to review all accounts manually prior to activation. Until your account has been validated by the administrator you will not be able to utilize the full capabilities of your account.');
	}

	if ($usr->users_opt & 65536 || is_email_blocked($usr->email) || is_login_blocked($usr->login) || is_ip_blocked(get_ip())) {
		ses_delete($usr->sid);
		$usr = ses_anon_make();
		setcookie($GLOBALS['COOKIE_NAME'].'1', 'd34db33fd34db33fd34db33fd34db33f', __request_timestamp__+63072000, $GLOBALS['COOKIE_PATH'], $GLOBALS['COOKIE_DOMAIN']);
		error_dialog('ERROR: you are not allowed to post', 'Your account has been blocked from posting');
	}
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
}

	if (__fud_real_user__) {
		is_allowed_user($usr);
	} else {
		std_error('login');
	}

	ses_update_status($usr->sid, 'Viewing personal control panel.');

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

	if (!empty($_GET['ufid']) && sq_check(0, $usr->sq)) {
		forum_notify_del(_uid, (int)$_GET['ufid']);
	}
	if (!empty($_GET['utid']) && sq_check(0, $usr->sq)) {
		thread_notify_del(_uid, (int)$_GET['utid']);
	}
	if (!empty($_GET['ubid']) && sq_check(0, $usr->sq)) {
		buddy_delete(_uid, (int)$_GET['ubid']);
	}

	$uc_buddy_ents = '';
	$c = uq("SELECT u.id, u.alias, u.last_visit, (users_opt & 32768) FROM fud26_buddy b INNER JOIN fud26_users u ON b.bud_id=u.id WHERE b.user_id="._uid." ORDER BY u.last_visit DESC");
	while ($r = db_rowarr($c)) {
		$uc_pm = ($FUD_OPT_1 & 1024) ? '<a href="index.php?t=ppost&toi='.$r[0].'&amp;'._rsid.'">PM</a>&nbsp;||&nbsp;' : '';
		$obj->login = $r[1];
		$uc_online = (!$r[3] && ($r[2] + $LOGEDIN_TIMEOUT * 60) > __request_timestamp__) ? '<img src="theme/default/images/online.png" alt="'.$obj->login.' is currently online" title="'.$obj->login.' is currently online" />' : '<img src="theme/default/images/offline.png" alt="'.$obj->login.'  is currently offline" title="'.$obj->login.'  is currently offline" />';
		$uc_buddy_ents .= '<tr class="RowStyleA">
	<td class="vm">'.$uc_online.'</td>
	<td class="nw vm wa"><a href="index.php?t=usrinfo&amp;id='.$r[0].'&amp;'._rsid.'">'.$r[1].'</a></td>
	<td class="nw vm RowStyleB SmallText">'.$uc_pm.'<a href="index.php?t=uc&amp;ubid='.$r[0].'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">X</a></td>
</tr>';
	}
	unset($c);

	$uc_new_pms = '';
	$c = uq("SELECT m.ouser_id, u.alias, m.post_stamp, m.subject, m.id FROM fud26_pmsg m INNER JOIN fud26_users u ON u.id=m.ouser_id WHERE m.duser_id="._uid." AND fldr=1 AND read_stamp=0 ORDER BY post_stamp DESC LIMIT ".($usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE));
	while ($r = db_rowarr($c)) {
		$uc_new_pms .= '<tr class="RowStyleB">
	<td><a href="index.php?t=pmsg_view&amp;id='.$r[4].'&amp;'._rsid.'">'.$r[3].'</a></td>
	<td class="nw"><a href="index.php?t=usrinfo&amp;id='.$r[0].'&amp;'._rsid.'">'.$r[1].'</a></td>
	<td class="DateText nw">'.strftime("%b %d %Y %H:%M", $r[2]).'</td>
</tr>';
	}
	unset($c);
	if ($uc_new_pms) {
		$uc_new_pms = '<tr>
	<th class="wa">Subject</th>
	<th class="nw">Author</th>
	<th class="nw">Time</th>
</tr>
'.$uc_new_pms;
	}

	$uc_sub_forum = '';
	$c = uq("SELECT
		f.name, f.id, f.descr, f.thread_count, f.post_count,
		u.alias,
		m.subject, m.id AS mid, m.post_stamp, m.poster_id,
		c.name AS cat_name
		FROM fud26_forum_notify fn
		INNER JOIN fud26_forum f ON f.id=fn.forum_id
		INNER JOIN fud26_cat c ON c.id=f.cat_id
		INNER JOIN fud26_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=f.id
		LEFT JOIN fud26_group_cache g2 ON g2.user_id="._uid." AND g2.resource_id=f.id
		LEFT JOIN fud26_msg m ON f.last_post_id=m.id
		LEFT JOIN fud26_users u ON u.id=m.poster_id
		LEFT JOIN fud26_forum_read fr ON fr.forum_id=f.id AND fr.user_id="._uid."
		LEFT JOIN fud26_mod mo ON mo.user_id="._uid." AND mo.forum_id=f.id
		WHERE fn.user_id="._uid."
		AND ".$usr->last_read." < m.post_stamp AND (fr.last_view IS NULL OR m.post_stamp > fr.last_view)
		".($is_a ? '' : " AND (mo.id IS NOT NULL OR
		(CASE WHEN g2.group_cache_opt IS NULL THEN g1.group_cache_opt ELSE g2.group_cache_opt END & 1) > 0)")."
		ORDER BY m.post_stamp DESC");
	while ($r = db_rowobj($c)) {
		$uc_sub_forum .= '<tr>
	<td class="RowStyleA SmallText wa"><a href="index.php?t='.t_thread_view.'&amp;frm_id='.$r->id.'&amp;'._rsid.'" class="big">'.htmlspecialchars($r->cat_name).' &raquo; '.$r->name.'</a>'.($r->descr ? '<br />'.$r->descr.'' : '' ) .'<br /><a href="index.php?t=post&amp;frm_id='.$r->id.'&amp;'._rsid.'">New Topic</a> || <a href="index.php?t=uc&amp;ufid='.$r->id.'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">Unsubscribe</a></td>
	<td class="RowStyleB ac">'.$r->post_count.'</td>
	<td class="RowStyleB ac">'.$r->thread_count.'</td>
	<td class="RowStyleA SmallText ar nw">'.($r->mid ? '<a href="index.php?t='.d_thread_view.'&amp;goto='.$r->mid.'&amp;'._rsid.'#msg_'.$r->mid.'">'.$r->subject.'</a><br />
<span class="DateText">'.strftime("%a, %d %B %Y", $r->post_stamp).'</span><br />By: '.($r->alias ? '<a href="index.php?t=usrinfo&amp;id='.$r->poster_id.'&amp;'._rsid.'">'.$r->alias.'</a>' : $GLOBALS['ANON_NICK'].'' ) : '' ) .'</td>
</tr>';
	}
	if ($uc_sub_forum) {
		$uc_sub_forum = '<tr>
        <th class="wa">Category &raquo; Forum</th>
	<th nowrap>Messages</th>
        <th nowrap>Topics</th>
        <th nowrap>Last message</th>
</tr>
'.$uc_sub_forum;
	}
	unset($c);

	$uc_sub_topic = '';
	$ppg = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;
	$c = uq("SELECT
			m2.subject, m.post_stamp, m.poster_id,
			u.alias,
			t.replies, t.views, t.thread_opt, t.id, t.last_post_id
		FROM fud26_thread_notify tn
		INNER JOIN fud26_thread t ON tn.thread_id=t.id
		INNER JOIN fud26_msg m ON t.last_post_id=m.id
		INNER JOIN fud26_msg m2 ON t.root_msg_id=m2.id
		INNER JOIN fud26_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=t.forum_id
		LEFT JOIN fud26_group_cache g2 ON g2.user_id="._uid." AND g2.resource_id=t.forum_id
		LEFT JOIN fud26_users u ON u.id=m.poster_id
		LEFT JOIN fud26_read r ON t.id=r.thread_id AND r.user_id="._uid."
		LEFT JOIN fud26_mod mo ON mo.user_id="._uid." AND mo.forum_id=t.forum_id
		WHERE tn.user_id="._uid." AND m.post_stamp > ".$usr->last_read." AND m.post_stamp > r.last_view ".
		($is_a ? '' : " AND (mo.id IS NOT NULL OR (CASE WHEN g2.group_cache_opt IS NULL THEN g1.group_cache_opt ELSE g2.group_cache_opt END & 1) > 0)").
		"ORDER BY m.post_stamp DESC LIMIT ".($usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE));
	while ($r = db_rowobj($c)) {
		$msg_count = $r->replies + 1;
		if ($msg_count > $ppg && $usr->users_opt & 256) {
			if ($THREAD_MSG_PAGER < ($pgcount = ceil($msg_count / $ppg))) {
				$i = $pgcount - $THREAD_MSG_PAGER;
				$mini_pager_data = '&nbsp;...';
			} else {
				$mini_pager_data = '';
				$i = 0;
			}
			while ($i < $pgcount) {
				$mini_pager_data .= '&nbsp;<a href="index.php?t='.d_thread_view.'&amp;th='.$r->id.'&amp;start='.($i * $ppg).'&amp;'._rsid.'">'.++$i.'</a>';
			}
			$mini_thread_pager = $mini_pager_data ? '<span class="SmallText">(<img src="theme/default/images/pager.gif" alt="" />'.$mini_pager_data.')</span>' : '';
		} else {
			$mini_thread_pager = '';
		}

		$uc_sub_topic .= '<tr>
	<td class="RowStyleA"><a href="index.php?t='.d_thread_view.'&amp;th='.$r->id.'&amp;unread=1&amp;'._rsid.'"><img src="theme/default/images/newposts.gif" title="Click here to go the first unread message in this topic" alt="" /></a>&nbsp;<a class="big" href="index.php?t='.d_thread_view.'&amp;th='.$r->id.'&amp;'._rsid.'">'.$r->subject.'</a> '.$mini_thread_pager.'</a><br /><div class="ar"><a href="index.php?t=post&amp;th_id='.$r->id.'&amp;'._rsid.'">Reply</a> || <a href="index.php?t=uc&amp;utid='.$r->id.'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">Unsubscribe</a></div></td>
	<td class="RowStyleB ac">'.$r->replies.'</td>
	<td class="RowStyleB ac">'.$r->views.'</td>
	<td class="RowStyleC ar nw"><span class="DateText">'.strftime("%a, %d %B %Y", $r->post_stamp).'</span><br />By: '.($r->alias ? '<a href="index.php?t=usrinfo&amp;id='.$r->poster_id.'&amp;'._rsid.'">'.$r->alias.'</a>' : $GLOBALS['ANON_NICK'].'' ) .' <a href="index.php?t='.d_thread_view.'&amp;goto='.$r->last_post_id.'&amp;'._rsid.'#msg_'.$r->last_post_id.'"><img src="theme/default/images/goto.gif" title="Go to the last message in this topic" alt="" /></a></td>
</tr>';
	}
	if ($uc_sub_topic) {
		$uc_sub_topic = '<tr>
        <th class="wa">Topic</th>
	<th nowrap>Replies</th>
        <th nowrap>Views</th>
        <th nowrap>Last message</th>
</tr>
'.$uc_sub_topic;
	}
	unset($c);

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

<table cellspacing="3" cellpadding="3" border="0" class="wa">
<tr>
	<td class="vt"><table border="0" cellspacing="1" cellpadding="2" class="ucPW">
<tr><th colspan="3">Buddy List</th></tr>
<?php echo $uc_buddy_ents; ?>
</table></td>

	<td class="wa vt">
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan="3">New Private Messages</th></tr>
<?php echo $uc_new_pms; ?>
</table>
<p>
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan="4">Subscribed Forums With New Messages</th></tr>
<?php echo $uc_sub_forum; ?>
</table>
<p>
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan="4">Subscribed Topics With New Messages</th></tr>
<?php echo $uc_sub_topic; ?></td>
</table>
</tr>
</table>

<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>