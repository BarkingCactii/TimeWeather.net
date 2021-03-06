<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: thread_view_common.inc.t,v 1.46 2004/12/21 16:15:31 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

/* check moved topic permissions */
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
}
?>