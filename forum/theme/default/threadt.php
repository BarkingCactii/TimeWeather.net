<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: threadt.php.t,v 1.36 2005/02/23 20:37:47 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}/* check moved topic permissions */
function th_moved_perm_chk($frm_id)
{
	make_perms_query($fields, $join, $frm_id);
	$res = db_sab("SELECT m.forum_id, ".$fields."
		FROM fud26_forum f ".$join."
		LEFT JOIN fud26_mod m ON m.user_id="._uid." AND m.forum_id=".$frm_id."
		WHERE f.id=".$frm_id." LIMIT 1");
	if (!$res || (!($res->group_cache_opt & 2) && !$res->forum_id)) {
		return;
	}
	return 1;
}

/* make sure that we have what appears to be a valid forum id */
if (!isset($_GET['frm_id']) || (!($frm_id = (int)$_GET['frm_id']))) {
	invl_inp_err();
}

if (!isset($_GET['start']) || ($start = (int)$_GET['start']) < 1) {
	$start = 0;
}

/* This query creates frm object that contains info about the current
 * forum, category & user's subscription status & permissions to the
 * forum.
 */

make_perms_query($fields, $join, $frm_id);

$frm = db_sab('SELECT	f.id, f.name, f.thread_count, f.cat_id,'.
			(_uid ? ' fn.forum_id AS subscribed, m.forum_id AS md, ' : ' 0 AS subscribed, 0 AS md, ').
			'a.ann_id AS is_ann, ms.post_stamp, '.$fields.'
		FROM fud26_forum f
		INNER JOIN fud26_cat c ON c.id=f.cat_id '.
		(_uid ? ' LEFT JOIN fud26_forum_notify fn ON fn.user_id='._uid.' AND fn.forum_id='.$frm_id.' LEFT JOIN fud26_mod m ON m.user_id='._uid.' AND m.forum_id='.$frm_id : ' ')
		.$join.'
		LEFT JOIN fud26_ann_forums a ON a.forum_id='.$frm_id.'
		LEFT JOIN fud26_msg ms ON ms.id=f.last_post_id
		WHERE f.id='.$frm_id.' LIMIT 1');

if (!$frm) {
	invl_inp_err();
}

$MOD = ($is_a || $frm->md);

/* check that the user has permissions to access this forum */
if (!($frm->group_cache_opt & 2) && !$MOD) {
	if (!isset($_GET['logoff'])) {
		std_error('login');
	}
	if ($FUD_OPT_2 & 32768) {
		header('Location: http://timeweather.net/forum/index.php/i/' . _rsidl);
	} else {
		header('Location: http://timeweather.net/forum/index.php?' . _rsidl);
	}
	exit;
}

if ($_GET['t'] == 'threadt') {
	$ann_cols = '5';
	$cur_frm_page = $start + 1;
} else {
	$ann_cols = '6';
	$cur_frm_page = floor($start / $THREADS_PER_PAGE) + 1;
}

/* do various things for registered users */
if (_uid) {
	if (isset($_GET['sub']) && sq_check(0, $usr->sq)) {
		forum_notify_add(_uid, $frm->id);
		$frm->subscribed = 1;
	} else if (isset($_GET['unsub']) && sq_check(0, $usr->sq)) {
		forum_notify_del(_uid, $frm->id);
		$frm->subscribed = 0;
	}
} else {
	header("Last-Modified: " .  gmdate("D, d M Y H:i:s", (int)$frm->post_stamp) . " GMT");
}

$ppg = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;

/* handling of announcements */
$announcements = '';
if ($frm->is_ann) {
	$today = gmdate('Ymd', __request_timestamp__);
	$res = uq('SELECT a.subject, a.text FROM fud26_announce a INNER JOIN fud26_ann_forums af ON a.id=af.ann_id AND af.forum_id='.$frm->id.' WHERE a.date_started<='.$today.' AND a.date_ended>='.$today);
	while ($r = db_rowarr($res)) {
		$announcements .= '<tr><td class="AnnText" colspan="'.$ann_cols.'"><span class="AnnSubjText">'.$r[0].'</span><br />'.$r[1].'</td></tr>';
	}
}function &get_all_read_perms($uid, $mod)
{
	$limit = array(0);

	$r = uq('SELECT resource_id, group_cache_opt FROM fud26_group_cache WHERE user_id='._uid);
	while ($ent = db_rowarr($r)) {
		$limit[$ent[0]] = $ent[1] & 2;
	}

	if (_uid) {
		if ($mod) {
			$r = uq('SELECT forum_id FROM fud26_mod WHERE user_id='._uid);
			while ($ent = db_rowarr($r)) {
				$limit[$ent[0]] = 2;
			}
		}

		$r = uq("SELECT resource_id FROM fud26_group_cache WHERE resource_id NOT IN (".implode(',', array_keys($limit)).") AND user_id=2147483647 AND (group_cache_opt & 2) > 0");
		while ($ent = db_rowarr($r)) {
			if (!isset($limit[$ent[0]])) {
				$limit[$ent[0]] = 2;
			}
		}
	}

	return $limit;
}

function perms_from_obj($obj, $adm)
{
	$perms = 1|2|4|8|16|32|64|128|256|512|1024|2048|4096|8192|16384|32768|262144;

	if ($adm || $obj->md) {
		return $perms;
	}

	return ($perms & $obj->group_cache_opt);
}

function make_perms_query(&$fields, &$join, $fid='')
{
	if (!$fid) {
		$fid = 'f.id';
	}

	if (_uid) {
		$join = ' INNER JOIN fud26_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id='.$fid.' LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id='.$fid.' ';
		$fields = ' (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS group_cache_opt ';
	} else {
		$join = ' INNER JOIN fud26_group_cache g1 ON g1.user_id=0 AND g1.resource_id='.$fid.' ';
		$fields = ' g1.group_cache_opt ';
	}
}

	if (!($FUD_OPT_2 & 512)) {
		error_dialog('Tree view of the topic listing has been disabled.', 'The administrator has disabled the tree view of the topic listing. Please use the flat view instead.');
	}

	ses_update_status($usr->sid, 'Browsing forum (tree view) <a href="index.php?t=threadt&amp;frm_id='.$frm->id.'">'.$frm->name.'</a>', $frm->id);

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
	}function tmpl_create_forum_select($frm_id, $mod)
{
	if (!isset($_GET['t']) || ($_GET['t'] != 'thread' && $_GET['t'] != 'threadt')) {
		$dest = t_thread_view;
	} else {
		$dest = $_GET['t'];
	}

	if ($mod) { /* admin optimization */
		$c = uq('SELECT f.id, f.name, c.id FROM fud26_fc_view v INNER JOIN fud26_forum f ON f.id=v.f INNER JOIN fud26_cat c ON f.cat_id=c.id ORDER BY v.id');
	} else {
		$c = uq('SELECT f.id, f.name, c.id
			FROM fud26_fc_view v
			INNER JOIN fud26_forum f ON f.id=v.f
			INNER JOIN fud26_cat c ON f.cat_id=c.id
			INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id ' .
			(_uid ? ' LEFT JOIN fud26_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.' LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id WHERE mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 1) > 0 '  : ' WHERE (g1.group_cache_opt & 1) > 0 ').
			'ORDER BY v.id');
	}
	$f = array($frm_id => 1);

	$oldc = $selection_options = '';
	while ($r = db_rowarr($c)) {
		if ($oldc != $r[2]) {
			while (list($k, $i) = each($GLOBALS['cat_cache'])) {
				$selection_options .= '<option value="0">- '.($tabw = ($i[0] ? str_repeat('&nbsp;&nbsp;&nbsp;', $i[0]) : '')).$i[1].'</option>';
				if ($k == $r[2]) {
					break;
				}
			}
			$oldc = $r[2];
		}
		$selection_options .= '<option value="'.$r[0].'"'.(isset($f[$r[0]]) ? ' selected' : '').'>'.$tabw.'&nbsp;&nbsp;'.$r[1].'</option>';
	}
	
	return '<span class="SmallText fb">Goto Forum:</span>
<form action="index.php" name="frmquicksel" method="get" onSubmit="javascript: if (document.frmquicksel.frm_id.value < 1) document.frmquicksel.frm_id.value='.$frm_id.';">
<input type="hidden" name="t" value="'.$dest.'">'._hs.'<input type="hidden" name="forum_redr" value="1">
<select class="SmallText" name="frm_id" onChange="javascript: if ( this.value==0 ) return false; document.frmquicksel.submit();">
'.$selection_options.'
</select>&nbsp;&nbsp;<input type="submit" class="button" name="frm_goto" value="Go" ></form>';
}if (!isset($th)) {
	$th = 0;
}
if (!isset($frm->id)) {
	$frm->id = 0;
}require $GLOBALS['FORUM_SETTINGS_PATH'].'cat_cache.inc';

function draw_forum_path($cid, $fn='', $fid=0, $tn='')
{
	global $cat_par, $cat_cache;

	$data = '';
	do {
		$data = '&nbsp;&raquo; <a href="index.php?t=i&amp;cat='.$cid.'&amp;'._rsid.'">'.$cat_cache[$cid][1].'</a>' . $data;
	} while (($cid = $cat_par[$cid]) > 0);

	if ($fid) {
		$data .= '&nbsp;&raquo; <a href="index.php?t='.t_thread_view.'&amp;frm_id='.$fid.'&amp;'._rsid.'">'.$fn.'</a>';
	} else if ($fn) {
		$data .= '&nbsp;&raquo; <b>'.$fn.'</b>';
	}

	return '<a href="index.php?t=i&amp;'._rsid.'">Home</a>'.$data.($tn ? '&nbsp;&raquo; <b>'.$tn.'</b>' : '');
}

	$TITLE_EXTRA = ': '.$frm->name;

	$r = q("SELECT
			t.moved_to, t.thread_opt, t.root_msg_id, r.last_view,
			m.subject, m.reply_to, m.poll_id, m.attach_cnt, m.icon, m.poster_id, m.post_stamp, m.thread_id, m.id,
			u.alias
		FROM fud26_thread_view tv
		INNER JOIN fud26_thread t ON tv.thread_id=t.id
		INNER JOIN fud26_msg m ON t.id=m.thread_id AND m.apr=1
		LEFT JOIN fud26_users u ON m.poster_id=u.id
		LEFT JOIN fud26_read r ON t.id=r.thread_id AND r.user_id="._uid."
		WHERE tv.forum_id=".$frm->id." AND tv.page=".$cur_frm_page." ORDER by pos ASC, m.id");

	if (!db_count($r)) {
		$thread_list_table_data = '<span class="GenText">There are no messages in this forum.</span>';
	} else {
		$thread_list_table_data = '';
		$p = $cur_th_id = 0;
		error_reporting(0);
		while ($obj = db_rowobj($r)) {
			unset($stack, $tree, $arr, $cur);
			db_seek($r, $p);

			$cur_th_id = $obj->thread_id;
			while ($obj = db_rowobj($r)) {
				if ($obj->thread_id != $cur_th_id) {
					db_seek($r, $p);
					break;
				}

				$arr[$obj->id] = $obj;
				$arr[$obj->reply_to]->kiddie_count++;
				$arr[$obj->reply_to]->kiddies[] = &$arr[$obj->id];

				if (!$obj->reply_to) {
					$tree->kiddie_count++;
					$tree->kiddies[] = &$arr[$obj->id];
				}
				$p++;
			}

			if (is_array($tree->kiddies)) {
				reset($tree->kiddies);
				$stack[0] = &$tree;
				$stack_cnt = isset($tree->kiddie_count) ? $tree->kiddie_count : 0;
				$j = $lev = 0;

				$thread_list_table_data .= '<tr><td><table border=0 cellspacing=0 cellpadding=0 class="tt">';

				while ($stack_cnt > 0) {
					$cur = &$stack[$stack_cnt-1];

					if (isset($cur->subject) && empty($cur->sub_shown)) {
						if ($TREE_THREADS_MAX_DEPTH > $lev) {
							if (isset($cur->subject[$TREE_THREADS_MAX_SUBJ_LEN])) {
								$cur->subject = substr($cur->subject, 0, $TREE_THREADS_MAX_SUBJ_LEN).'...';
							}
							if (_uid) {
								if ($usr->last_read < $cur->post_stamp && $cur->post_stamp>$cur->last_view) {
									$thread_read_status = $cur->thread_opt & 1 ? '<img src="theme/default/images/unreadlocked.png" title="Locked topic with unread messages" alt="" />'	: '<img src="theme/default/images/unread.png" title="This topic contains messages you have not yet read" alt="" />';
								} else {
									$thread_read_status = $cur->thread_opt & 1 ? '<img src="theme/default/images/readlocked.png" title="This topic has been locked" alt="" />' : '<img src="theme/default/images/read.png" title="This topic has no unread messages" alt="" />';
								}
							} else {
								$thread_read_status = $cur->thread_opt & 1 ? '<img src="theme/default/images/readlocked.png" title="This topic has been locked" alt="" />' : '<img src="theme/default/images/read.png" title="The read &amp; unread messages are only tracked for registered users" alt="" />';
							}

							$thread_list_table_data .= '<tr>
<td class="RowStyleB">'.$thread_read_status.'</td>
<td class="RowStyleB">'.($cur->icon ? '<img src="images/message_icons/'.$cur->icon.'" alt="'.$cur->icon.'" />' : '&nbsp;' ) .'</td>
<td class="tt" style="padding-left: '.(20 * ($lev - 1)).'px">'.($cur->poll_id ? 'Poll:&nbsp;' : '' ) .($cur->attach_cnt ? '<img src="theme/default/images/attachment.gif" alt="" />' : '' ) .'<a href="index.php?t='.d_thread_view.'&amp;goto='.$cur->id.'&amp;'._rsid.'#msg_'.$cur->id.'" class="big">'.$cur->subject.'</a>
'.(($lev == 1 && $cur->thread_opt > 1) ? ($cur->thread_opt & 4 ? '<span class="StClr"> (sticky)</span>' : '<span class="AnClr"> (announcement)</span>' ) : '' ) .'
<div class="TopBy">By: '.($cur->poster_id ? '<a href="index.php?t=usrinfo&amp;id='.$cur->poster_id.'&amp;'._rsid.'">'.$cur->alias.'</a>' : $GLOBALS['ANON_NICK'].'' ) .' on '.strftime("%a, %d %B %Y %H:%M", $cur->post_stamp).'</div></td>
</tr>';
						} else if ($TREE_THREADS_MAX_DEPTH == $lev) {
							$thread_list_table_data .= '<tr>
<td class="RowStyleB" colspan=2>&nbsp;</td>
<td class="tt" style="padding-left: '.($width += 20).'px"><a href="index.php?t='.d_thread_view.'&amp;goto='.$cur->id.'&amp;'._rsid.'#msg_'.$cur->id.'" class="big">&lt;more&gt;</a></td>
</tr>';
						}

						$cur->sub_shown = 1;
					}

					if (!isset($cur->kiddie_count)) {
						$cur->kiddie_count = 0;
					}

					if ($cur->kiddie_count && isset($cur->kiddie_pos)) {
						++$cur->kiddie_pos;
					} else {
						$cur->kiddie_pos = 0;
					}

					if ($cur->kiddie_pos < $cur->kiddie_count) {
						++$lev;
						$stack[$stack_cnt++] = &$cur->kiddies[$cur->kiddie_pos];
					} else { // unwind the stack if needed
						unset($stack[--$stack_cnt]);
						--$lev;
					}
				}
				unset($cur);

				$thread_list_table_data .= '</table></td></tr>';
			}
		}
	}

	if ($FUD_OPT_2 & 32768) {
		$page_pager = tmpl_create_pager($start, 1, ceil($frm->thread_count / $THREADS_PER_PAGE), 'index.php/sf/threadt/'.$frm->id.'/1/', '/' . _rsid);
	} else {
		$page_pager = tmpl_create_pager($start, 1, ceil($frm->thread_count / $THREADS_PER_PAGE), 'index.php?t=threadt&amp;frm_id='.$frm->id.'&amp;'._rsid);
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
<?php echo $admin_cp; ?>
<table class="wa" border=0 cellspacing=0 cellpadding=0><tr>
<td class="al wa"><?php echo draw_forum_path($frm->cat_id, $frm->name); ?><br /><span class="GenText fb">Show:</span> <a href="index.php?t=selmsg&amp;date=today&amp;<?php echo _rsid; ?>&amp;frm_id=<?php echo $frm->id; ?>&amp;th=<?php echo $th; ?>" title="Show all messages that were posted today">Today's Messages</a>&nbsp;<?php echo (_uid ? '<b>::</b> <a href="index.php?t=selmsg&amp;unread=1&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all unread messages">Unread Messages</a>&nbsp;' : '' ) .(!$th ? '<b>::</b> <a href="index.php?t=selmsg&amp;reply_count=0&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all messages, which have no replies">Unanswered Messages</a>' : ''); ?> <b>::</b> <a href="index.php?t=polllist&amp;<?php echo _rsid; ?>">Show Polls</a> <b>::</b> <a href="index.php?t=mnav&amp;<?php echo _rsid; ?>">Message Navigator</a><br /><img src="blank.gif" alt="" height=2 /><br /><?php echo (_uid ? ($frm->subscribed ? '<a href="index.php?t='.$_GET['t'].'&amp;unsub=1&amp;frm_id='.$frm->id.'&amp;start='.$start.'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'" title="Stop receiving notifications about new topics in the forum">Unsubscribe</a>' : '<a href="index.php?t='.$_GET['t'].'&amp;sub=1&amp;frm_id='.$frm->id.'&amp;start='.$start.'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'" title="Receive notifications when someone creates a new topic in this forum">Subscribe</a>' )  : '' ) .((_uid && ($MOD || $frm->group_cache_opt & 2048)) ? '&nbsp;<a href="index.php?t=merge_th&amp;frm='.$frm->id.'&amp;'._rsid.'">Merge Topics</a>' : ''); ?></td>
<td class="GenText nw vb ar"><a href="index.php?t=thread&amp;frm_id=<?php echo $frm->id; ?>&amp;<?php echo _rsid; ?>"><img alt="Return to the default flat view" title="Return to the default flat view" src="theme/default/images/flat_view.gif" /></a>&nbsp;<a href="index.php?t=post&amp;frm_id=<?php echo $frm->id; ?>&amp;<?php echo _rsid; ?>"><img src="theme/default/images/new_thread.gif" alt="Create a new topic" /></a></td>
</tr></table>
<table cellspacing="0" cellpadding="2" class="ContentTable">
<?php echo $announcements; ?>
<?php echo $thread_list_table_data; ?>
</table>
<table border=0 cellspacing=0 cellpadding=0 class="wa">
<tr>
<td class="vt"><?php echo $page_pager; ?>&nbsp;</td>
<td class="GenText nw vb ar"><a href="index.php?t=thread&amp;frm_id=<?php echo $frm->id; ?>&amp;<?php echo _rsid; ?>"><img alt="Return to the default flat view" title="Return to the default flat view" src="theme/default/images/flat_view.gif" /></a>&nbsp;<a href="index.php?t=post&amp;frm_id=<?php echo $frm->id; ?>&amp;<?php echo _rsid; ?>"><img src="theme/default/images/new_thread.gif" alt="Create a new topic" /></a></td>
</tr>
</table>
<?php echo tmpl_create_forum_select((isset($frm->forum_id) ? $frm->forum_id : $frm->id), $usr->users_opt & 1048576); ?>
<?php echo (_uid ? '<div class="ar SmallText">[<a href="index.php?t=markread&amp;'._rsid.'&id='.$frm->id.'&amp;SQ='.$GLOBALS['sq'].'" title="All unread messages in this forum will be marked read">mark all unread forum messages read</a>]'.(($FUD_OPT_2 & 270532608) == 270532608 ? '&nbsp;[ <a href="'.$GLOBALS['WWW_ROOT'].'pdf.php?frm='.$frm->id.'&amp;page='.$cur_frm_page.'">Generate printable PDF</a> ]' : '' ) .($FUD_OPT_2 & 1048576 ? '&nbsp;[ <a href="index.php?t=help_index&amp;section=boardusage#syndicate">Syndicate this forum (XML)</a> ]' : '' )  .'</div>' : '<div class="ar SmallText">'.(($FUD_OPT_2 & 270532608) == 270532608 ? '&nbsp;[ <a href="'.$GLOBALS['WWW_ROOT'].'pdf.php?frm='.$frm->id.'&amp;page='.$cur_frm_page.'">Generate printable PDF</a> ]' : '' ) .($FUD_OPT_2 & 1048576 ? '&nbsp;[ <a href="index.php?t=help_index&amp;section=boardusage#syndicate">Syndicate this forum (XML)</a> ]' : '' )  .'</div>'); ?>
<fieldset>
        <legend>Legend</legend>
<img src="theme/default/images/unread.png" alt="New Messages" />&nbsp;New Messages&nbsp;&nbsp;
<img src="theme/default/images/read.png" alt="No New messages" />&nbsp;No New messages&nbsp;&nbsp;
<img src="theme/default/images/unreadlocked.png" alt="Locked (w/ unread messages)" />&nbsp;Locked (w/ unread messages)&nbsp;&nbsp;
<img src="theme/default/images/readlocked.png" alt="Locked" />&nbsp;Locked&nbsp;&nbsp;
<img src="theme/default/images/moved.png" alt="Moved to another forum" />&nbsp;Moved to another forum
</fieldset>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>
<?php
	if (_uid) {
		user_register_forum_view($frm->id);
	}
?>