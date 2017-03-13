<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: groupmgr.php.t,v 1.47 2005/03/18 15:25:54 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

function draw_tmpl_perm_table($perm, $perms, $names)
{
	$str = '';
	foreach ($perms as $k => $v) {
		$str .= ($perm & $v[0]) ? '<td title="'.$names[$k].'" class="permYES">Yes</td>' : '<td title="'.$names[$k].'" class="permNO">No</td>';
	}
	return $str;
}

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
}function grp_delete_member($id, $user_id)
{
	if (!$user_id || $user_id == '2147483647') {
		return;
	}

	q('DELETE FROM fud26_group_members WHERE group_id='.$id.' AND user_id='.$user_id);

	if (q_singleval("SELECT id FROM fud26_group_members WHERE user_id=".$user_id." LIMIT 1")) {
		/* we rebuild cache, since this user's permission for a particular resource are controled by
		 * more the one group. */
		grp_rebuild_cache(array($user_id));
	} else {
		q("DELETE FROM fud26_group_cache WHERE user_id=".$user_id);
	}
}

function grp_update_member($id, $user_id, $perm)
{
	q('UPDATE fud26_group_members SET group_members_opt='.$perm.' WHERE group_id='.$id.' AND user_id='.$user_id);
	grp_rebuild_cache(array($user_id));
}

function grp_rebuild_cache($user_id=null)
{
	$list = array();
	if ($user_id !== null) {
		$lmt = ' user_id IN('.implode(',', $user_id).') ';
	} else {
		$lmt = '';
	}

	/* generate an array of permissions, in the end we end up with 1ist of permissions */
	$r = uq("SELECT gm.user_id AS uid, gm.group_members_opt AS gco, gr.resource_id AS rid FROM fud26_group_members gm INNER JOIN fud26_group_resources gr ON gr.group_id=gm.group_id WHERE gm.group_members_opt>=65536 AND (gm.group_members_opt & 65536) > 0" . ($lmt ? ' AND '.$lmt : ''));
	while ($o = db_rowobj($r)) {
		foreach ($o as $k => $v) {
			$o->{$k} = (int) $v;
		}
		if (isset($list[$o->rid][$o->uid])) {
			if ($o->gco & 131072) {
				$list[$o->rid][$o->uid] |= $o->gco;
			} else {
				$list[$o->rid][$o->uid] &= $o->gco;
			}
		} else {
			$list[$o->rid][$o->uid] = $o->gco;
		}
	}

	$tmp = array();
	foreach ($list as $k => $v) {
		foreach ($v as $u => $p) {
			$tmp[] = $k.", ".$p.", ".$u;
		}
	}

	if (!$tmp) {
		q("DELETE FROM fud26_group_cache" . ($lmt ? ' WHERE '.$lmt : ''));
		return;
	}

	if (__dbtype__ == 'mysql') {
		q("REPLACE INTO fud26_group_cache (resource_id, group_cache_opt, user_id) VALUES (".implode('),(', $tmp).")");
		q("DELETE FROM fud26_group_cache WHERE ".($lmt ? $lmt . ' AND ' : '')." id < LAST_INSERT_ID()");
		return;
	}
	
	$tmp_t = "fud26_gc_".__request_timestamp__;
	q("CREATE TEMPORARY TABLE ".$tmp_t." (a INT, b INT, c INT)");
	ins_m($tmp_t, "a,b,c", $tmp, "integer, integer, integer");

	if (($ll = !db_locked())) {
		db_lock("fud26_group_cache WRITE");
	}

	q("DELETE FROM fud26_group_cache" . ($lmt ? ' WHERE '.$lmt : ''));
	q("INSERT INTO fud26_group_cache (resource_id, group_cache_opt, user_id) SELECT a,b,c FROM ".$tmp_t);

	if ($ll) {
		db_unlock();
	}

	q("DROP TABLE ".$tmp_t);
}

function group_perm_array()
{
	return array(
		'p_VISIBLE' => array(1, 'Visible'),
		'p_READ' => array(2, 'Read'),
		'p_POST' => array(4, 'Create new topics'),
		'p_REPLY' => array(8, 'Reply to messages'),
		'p_EDIT' => array(16, 'Edit messages'),
		'p_DEL' => array(32, 'Delete messages'),
		'p_STICKY' => array(64, 'Make topics sticky'),
		'p_POLL' => array(128, 'Create polls'),
		'p_FILE' => array(256, 'Attach files'),
		'p_VOTE' => array(512, 'Vote on polls'),
		'p_RATE' => array(1024, 'Rate topics'),
		'p_SPLIT' => array(2048, 'Split/Merge topics'),
		'p_LOCK' => array(4096, 'Lock/Unlock topics'),
		'p_MOVE' => array(8192, 'Move topics'),
		'p_SML' => array(16384, 'Use smilies/emoticons'),
		'p_IMG' => array(32768, 'Use [img] tags'),
		'p_SEARCH' => array(262144, 'Can Search')
	);
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
}

	if (!_uid) {
		std_error('login');
	}
	$group_id = isset($_POST['group_id']) ? (int)$_POST['group_id'] : (isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0);

	if ($group_id && !$is_a && !q_singleval('SELECT id FROM fud26_group_members WHERE group_id='.$group_id.' AND user_id='._uid.' AND group_members_opt>=131072 AND (group_members_opt & 131072) > 0')) {
		std_error('access');
	}

	$hdr = group_perm_array();
	/* fetch all the groups user has access to */
	if ($is_a) {
		$r = uq('SELECT id, name, forum_id FROM fud26_groups WHERE id>2 ORDER BY name');
	} else {
		$r = uq('SELECT g.id, g.name, g.forum_id FROM fud26_group_members gm INNER JOIN fud26_groups g ON gm.group_id=g.id WHERE gm.user_id='._uid.' AND group_members_opt>=131072 AND (group_members_opt & 131072) > 0 ORDER BY g.name');
	}

	/* make a group selection form */
	$n = 0;
	$vl = $kl = '';
	while ($e = db_rowarr($r)) {
		$vl .= $e[0] . "\n";
	        $kl .= ($e[2] ? '* ' : '') . htmlspecialchars($e[1]) . "\n";
		$n++;
	}

	if (!$n) {
		std_error('access');
	} else if ($n == 1) {
		$group_id = rtrim($vl);
		$group_selection = '';
	} else {
		if (!$group_id) {
			$group_id = (int)$vl;
		}
		$group_selection = '<br /><br />
<form method="post" action="index.php?t=groupmgr">
<div class="ctb"><table cellspacing="1" cellpadding="2" class="MiniTable">
<tr><th colspan=3>Group Editor Selection</th></tr>
<tr class="RowStyleC">
	<td class="nw fb">Group:</td>
	<td><select name="group_id">'.tmpl_draw_select_opt(rtrim($vl), rtrim($kl), $group_id).'</select></td>
	<td class="ar"><input type="submit" class="button" name="btn_groupswitch" value="Edit Group"></td>
</tr>
</table></div>'._hs.'</form>';
	}

if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
	}if (_uid) {
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
}

	if (isset($_POST['btn_cancel'])) {
		unset($_POST);
	}
	if (!($grp = db_sab('SELECT * FROM fud26_groups WHERE id='.$group_id))) {
		invl_inp_err();
	}

	/* fetch controlled resources */
	if (!$grp->forum_id) {
		$group_resources = '<b>This group controls permissions of the following forums:</b><br>';
		$c = uq('SELECT f.name FROM fud26_group_resources gr INNER JOIN fud26_forum f ON gr.resource_id=f.id WHERE gr.group_id='.$group_id);
		while ($r = db_rowarr($c)) {
			$group_resources .= '&nbsp;&nbsp;&nbsp;'.$r[0].'<br>';
		}
	} else {
		$fname = q_singleval('SELECT name FROM fud26_forum WHERE id='.$grp->forum_id);
		$group_resources = '<b>Primary group for forum:</b> '.$fname;
	}

	if ($is_a) {
		$maxperms = 2147483647;
	} else {
		$maxperms = (int) $grp->groups_opt;
	}

	$login_error = '';
	$perm = 0;

	if (isset($_POST['btn_submit'])) {
		foreach ($hdr as $k => $v) {
			if (isset($_POST[$k]) && $_POST[$k] & $v[0]) {
				$perm |= $v[0];
			}
		}

		/* auto approve members */
		$perm |= 65536;

		if (empty($_POST['edit'])) {
			$gr_member = $_POST['gr_member'];

			if (!($usr_id = q_singleval("SELECT id FROM fud26_users WHERE alias='".addslashes(char_fix(htmlspecialchars($gr_member)))."'"))) {
				$login_error = '<span class="ErrorText">There is no user with a login of "'.char_fix(htmlspecialchars($gr_member)).'"</span><br />';
			} else if (q_singleval('SELECT id FROM fud26_group_members WHERE group_id='.$group_id.' AND user_id='.$usr_id)) {
				$login_error = '<span class="ErrorText">User "'.char_fix(htmlspecialchars($gr_member)).'" already exists in this group.</span><br />';
			} else {
				q('INSERT INTO fud26_group_members (group_members_opt, user_id, group_id) VALUES ('.$perm.', '.$usr_id.', '.$group_id.')');
				grp_rebuild_cache(array($usr_id));
			}
		} else if (($usr_id = q_singleval('SELECT user_id FROM fud26_group_members WHERE group_id='.$group_id.' AND id='.(int)$_POST['edit'])) !== null) {
			if (q_singleval("SELECT user_id FROM fud26_group_members WHERE group_id=".$group_id." AND user_id=".$usr_id." AND group_members_opt>=131072 AND (group_members_opt & 131072) > 0")) {
				$perm |= 131072;
			}
			q('UPDATE fud26_group_members SET group_members_opt='.$perm.' WHERE id='.(int)$_POST['edit']);
			grp_rebuild_cache(array($usr_id));
		}
		if (!$login_error) {
			unset($_POST);
			$gr_member = '';
		}
	}

	if (isset($_GET['del']) && ($del = (int)$_GET['del']) && $group_id && sq_check(0, $usr->sq)) {
		$is_gl = q_singleval("SELECT user_id FROM fud26_group_members WHERE group_id=".$group_id." AND user_id=".$del." AND group_members_opt>=131072 AND (group_members_opt & 131072) > 0");
		grp_delete_member($group_id, $del);

		/* if the user was a group moderator, rebuild moderation cache */
		if ($is_gl) {
			fud_use('groups_adm.inc', true);
			rebuild_group_ldr_cache($del);
		}
	}

	$edit = 0;
	if (isset($_GET['edit']) && ($edit = (int)$_GET['edit'])) {
		if (!($mbr = db_sab('SELECT gm.*, u.alias FROM fud26_group_members gm LEFT JOIN fud26_users u ON u.id=gm.user_id WHERE gm.group_id='.$group_id.' AND gm.id='.$edit))) {
			invl_inp_err();
		}
		if ($mbr->user_id == 0) {
			$gr_member = '<span class="anon">Anonymous</span>';
		} else if ($mbr->user_id == '2147483647') {
			$gr_member = '<span class="reg">All Registered Users</span>';
		} else {
			$gr_member = $mbr->alias;
		}
		$perm = $mbr->group_members_opt;
	} else if ($group_id > 2 && !isset($_POST['btn_submit']) && ($luser_id = q_singleval('SELECT MAX(id) FROM fud26_group_members WHERE group_id='.$group_id))) {
		/* help trick, we fetch the last user added to the group */
		if (!($mbr = db_sab('SELECT 1 AS user_id, group_members_opt FROM fud26_group_members WHERE id='.$luser_id))) {
			invl_inp_err();
		}
		$perm = $mbr->group_members_opt;
	} else {
		$mbr = 0;
	}

	/* anon users cannot vote or rate */
	if ($mbr && !$mbr->user_id) {
		$maxperms = $maxperms &~ (512|1024);
	}

	/* no members inside the group */
	if (!$perm && !$mbr) {
		$perm = $maxperms;
	}

	/* translated permission names */
	$ts_list = array(
'p_VISIBLE'=>'Visible',
'p_READ'=>'Read',
'p_POST'=>'Post',
'p_REPLY'=>'Reply',
'p_EDIT'=>'Edit',
'p_DEL'=>'Delete',
'p_STICKY'=>'Sticky messages',
'p_POLL'=>'Create polls',
'p_FILE'=>'Attach files',
'p_VOTE'=>'Vote',
'p_RATE'=>'Rate topics',
'p_SPLIT'=>'Split topics',
'p_LOCK'=>'Lock topics',
'p_MOVE'=>'Move topics',
'p_SML'=>'Use smilies',
'p_IMG'=>'Use image tags',
'p_SEARCH'=>'Can Search');

	$perm_sel_hdr = $perm_select = $tmp = '';
	$i = 0;
	foreach ($hdr as $k => $v) {
		$selyes = '';
		if ($maxperms & $v[0]) {
			if ($perm & $v[0]) {
				$selyes = ' selected';
			}
			$perm_select .= '<td class="ac">
<select name="'.$k.'" class="SmallText">
	<option value="0">No</option>
	<option value="'.$v[0].'"'.$selyes.'>Yes</option>
</select>
</td>';
		} else {
			/* only show the permissions the user can modify */
			continue;
		}
		$tmp .= '<th class="ac">'.$ts_list[$k].'</th>';

		if (++$i == '6') {
			$perm_sel_hdr .= '<tr>'.$tmp.'</tr>
<tr class="RowStyleB">'.$perm_select.'</tr>';
			$perm_select = $tmp = '';
			$i = 0;
		}
	}

	if ($tmp) {
		while (++$i < '6' + 1) {
			$tmp .= '<th> </th>';
			$perm_select .= '<td> </td>';
		}
		$perm_sel_hdr .= '<tr>'.$tmp.'</tr>
<tr class="RowStyleB">'.$perm_select.'</tr>';
	}

	/* draw list of group members */
	$group_members_list = '';
	$r = uq('SELECT gm.id AS mmid, gm.*, g.*, u.alias FROM fud26_group_members gm INNER JOIN fud26_groups g ON gm.group_id=g.id LEFT JOIN fud26_users u ON gm.user_id=u.id WHERE gm.group_id='.$group_id.' ORDER BY gm.id');
	while ($obj = db_rowobj($r)) {
		$perm_table = draw_tmpl_perm_table($obj->group_members_opt, $hdr, $ts_list);

		if ($obj->user_id == '0') {
			$member_name = '<span class="anon">Anonymous</span>';
			$group_members_list .= '<tr class="'.alt_var('mem_list_alt','RowStyleA','RowStyleB').'">
<td class="nw">'.$member_name.'</td>
'.$perm_table.'
<td class="nw">[<a href="index.php?t=groupmgr&amp;'._rsid.'&amp;edit='.$obj->mmid.'&amp;group_id='.$obj->group_id.'">Edit</a>]</td></tr>';
		} else if ($obj->user_id == '2147483647')  {
			$member_name = '<span class="reg">All Registered Users</span>';
			$group_members_list .= '<tr class="'.alt_var('mem_list_alt','RowStyleA','RowStyleB').'">
<td class="nw">'.$member_name.'</td>
'.$perm_table.'
<td class="nw">[<a href="index.php?t=groupmgr&amp;'._rsid.'&amp;edit='.$obj->mmid.'&amp;group_id='.$obj->group_id.'">Edit</a>]</td></tr>';
		} else {
			$member_name = $obj->alias;
			if ($obj->user_id == _uid && !$is_a) {
				$group_members_list .= '<tr class="'.alt_var('mem_list_alt','RowStyleA','RowStyleB').'">
<td class="nw">'.$member_name.'</td>
'.$perm_table.'
<td class="nw">[<a href="index.php?t=groupmgr&amp;'._rsid.'&amp;edit='.$obj->mmid.'&amp;group_id='.$obj->group_id.'">Edit</a>]</td></tr>';
			} else {
				$group_members_list .= '<tr class="'.alt_var('mem_list_alt','RowStyleA','RowStyleB').'">
<td class="nw">'.$member_name.'</td>
'.$perm_table.'
<td class="nw">[<a href="index.php?t=groupmgr&amp;'._rsid.'&amp;edit='.$obj->mmid.'&amp;group_id='.$obj->group_id.'">Edit</a>] [<a href="index.php?t=groupmgr&amp;'._rsid.'&amp;del='.$obj->user_id.'&amp;group_id='.$obj->group_id.'&amp;SQ='.$GLOBALS['sq'].'">Delete</a>]</td></tr>';
			}
		}
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
<br /><?php echo $admin_cp; ?>
<?php echo $group_selection; ?>
<br />
<div class="ac">Currently Editing: <b><?php echo $grp->name; ?></b><br><?php echo $group_resources; ?></div>
<br />
<form method="post" action="index.php?t=groupmgr" name="groupmgr">
<table cellspacing="1" cellpadding="2" class="ContentTable">
<?php echo ($edit ? '<tr class="RowStyleA"><td class="nw fb">Member</td><td class="wa al">'.$gr_member.'</td></tr>' : '<tr class="RowStyleA"><td class="nw db">Member</td><td class="wa al">'.$login_error.'<input tabindex="1" type="text" name="gr_member" value="'.(isset($_POST['gr_member']) ? char_fix(htmlspecialchars($_POST['gr_member'])).'' : '' )  .'">'.($FUD_OPT_1 & (8388608|4194304) ? '&nbsp;&nbsp;&nbsp;[<a href="javascript://" onClick="javascript: window_open(\'index.php?t=pmuserloc&amp;'._rsid.'&amp;js_redr=groupmgr.gr_member&amp;overwrite=1\', \'user_list\', 325,250);">Find User</a>]' : '' )  .'</td></tr>'); ?>
<tr class="RowStyleB">
	<td colspan=2>
		<table cellspacing=1 cellpadding=3 width="100%" class="ContentTable">
			<?php echo $perm_sel_hdr; ?>
		</table>
	</td>
</tr>
<tr>
	<td colspan=2 class="RowStyleC ar">
		<?php echo ($edit ? '<input type="submit" tabindex="3" class="button" name="btn_cancel" value="Cancel"> <input type="submit" tabindex="2" class="button" name="btn_submit" value="Update Member">' : '<input type="submit" tabindex="2" class="button" name="btn_submit" value="Add Member">'); ?>
	</td>
</tr>
</table><input type="hidden" name="group_id" value="<?php echo $group_id; ?>"><input type="hidden" name="edit" value="<?php echo $edit; ?>"><?php echo _hs; ?></form>
<script>
<!--
if (document.groupmgr.gr_member) {
	document.groupmgr.gr_member.focus();
}
//-->
</script>
<br /><br />
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th>Member</th><th colspan="<?php echo count($hdr); ?>">Permissions <span class="small">(move mouse over the permission to see its type)</span></th><th class="ac">Action</th></tr>
<?php echo $group_members_list; ?>
</table>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>