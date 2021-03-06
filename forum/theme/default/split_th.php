<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: split_th.php.t,v 1.43 2005/03/20 15:31:28 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function th_lock($id, $lck)
{
	q("UPDATE fud26_thread SET thread_opt=(thread_opt|1)".(!$lck ? '& ~ 1' : '')." WHERE id=".$id);
}

function th_inc_view_count($id)
{
	q('UPDATE fud26_thread SET views=views+1 WHERE id='.$id);
}

function th_inc_post_count($id, $r, $lpi=0, $lpd=0)
{
	if ($lpi && $lpd) {
		q('UPDATE fud26_thread SET replies=replies+'.$r.', last_post_id='.$lpi.', last_post_date='.$lpd.' WHERE id='.$id);
	} else {
		q('UPDATE fud26_thread SET replies=replies+'.$r.' WHERE id='.$id);
	}
}

function th_frm_last_post_id($id, $th)
{
	return (int) q_singleval('SELECT fud26_thread.last_post_id FROM fud26_thread INNER JOIN fud26_msg ON fud26_thread.root_msg_id=fud26_msg.id WHERE fud26_thread.forum_id='.$id.' AND fud26_thread.id!='.$th.' AND fud26_thread.moved_to=0 AND fud26_msg.apr=1 ORDER BY fud26_thread.last_post_date DESC LIMIT 1');
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
}function logaction($user_id, $res, $res_id=0, $action=null)
{
	q('INSERT INTO fud26_action_log (logtime, logaction, user_id, a_res, a_res_id)
		VALUES('.__request_timestamp__.', '.strnull($action).', '.$user_id.', '.strnull($res).', '.(int)$res_id.')');
}function apply_custom_replace($text)
{
	if (!defined('__fud_replace_init')) {
		make_replace_array();
	}
	if (empty($GLOBALS['__FUD_REPL__'])) {
		return $text;
	}

	return preg_replace($GLOBALS['__FUD_REPL__']['pattern'], $GLOBALS['__FUD_REPL__']['replace'], $text);
}

function make_replace_array()
{
	$GLOBALS['__FUD_REPL__']['pattern'] = $GLOBALS['__FUD_REPL__']['replace'] = array();
	$a =& $GLOBALS['__FUD_REPL__']['pattern'];
	$b =& $GLOBALS['__FUD_REPL__']['replace'];

	$c = uq('SELECT with_str, replace_str FROM fud26_replace WHERE replace_str IS NOT NULL AND with_str IS NOT NULL AND LENGTH(replace_str)>0');
	while ($r = db_rowarr($c)) {
		$a[] = $r[1];
		$b[] = $r[0];
	}

	define('__fud_replace_init', 1);
}

function make_reverse_replace_array()
{
	$GLOBALS['__FUD_REPLR__']['pattern'] = $GLOBALS['__FUD_REPLR__']['replace'] = array();
	$a =& $GLOBALS['__FUD_REPLR__']['pattern'];
	$b =& $GLOBALS['__FUD_REPLR__']['replace'];

	$c = uq('SELECT replace_opt, with_str, replace_str, from_post, to_msg FROM fud26_replace');
	while ($r = db_rowarr($c)) {
		if (!$r[0]) {
			$a[] = $r[3];
			$b[] = $r[4];
		} else if ($r[0] && strlen($r[1]) && strlen($r[2])) {
			$a[] = '/'.str_replace('/', '\\/', preg_quote(stripslashes($r[1]))).'/';
			preg_match('/\/(.+)\/(.*)/', $r[2], $regs);
			$b[] = str_replace('\\/', '/', $regs[1]);
		}
	}

	define('__fud_replacer_init', 1);
}

function apply_reverse_replace($text)
{
	if (!defined('__fud_replacer_init')) {
		make_reverse_replace_array();
	}
	if (empty($GLOBALS['__FUD_REPLR__'])) {
		return $text;
	}
	return preg_replace($GLOBALS['__FUD_REPLR__']['pattern'], $GLOBALS['__FUD_REPLR__']['replace'], $text);
}function th_add($root, $forum_id, $last_post_date, $thread_opt, $orderexpiry, $replies=0, $lpi=0)
{
	if (!$lpi) {
		$lpi = $root;
	}

	return db_qid("INSERT INTO
		fud26_thread
			(forum_id, root_msg_id, last_post_date, replies, views, rating, last_post_id, thread_opt, orderexpiry)
		VALUES
			(".$forum_id.", ".$root.", ".$last_post_date.", ".$replies.", 0, 0, ".$lpi.", ".$thread_opt.", ".$orderexpiry.")");
}

function th_move($id, $to_forum, $root_msg_id, $forum_id, $last_post_date, $last_post_id)
{
	if (!db_locked()) {
		db_lock('fud26_poll WRITE, fud26_thread_view WRITE, fud26_thread WRITE, fud26_forum WRITE, fud26_msg WRITE');
		$ll = 1;
	}
	$msg_count = q_singleval("SELECT count(*) FROM fud26_thread LEFT JOIN fud26_msg ON fud26_msg.thread_id=fud26_thread.id WHERE fud26_msg.apr=1 AND fud26_thread.id=".$id);

	q('UPDATE fud26_thread SET forum_id='.$to_forum.' WHERE id='.$id);
	q('UPDATE fud26_forum SET post_count=post_count-'.$msg_count.' WHERE id='.$forum_id);
	q('UPDATE fud26_forum SET thread_count=thread_count+1,post_count=post_count+'.$msg_count.' WHERE id='.$to_forum);
	q('DELETE FROM fud26_thread WHERE forum_id='.$to_forum.' AND root_msg_id='.$root_msg_id.' AND moved_to='.$forum_id);
	if (($aff_rows = db_affected())) {
		q('UPDATE fud26_forum SET thread_count=thread_count-'.$aff_rows.' WHERE id='.$to_forum);
	}
	q('UPDATE fud26_thread SET moved_to='.$to_forum.' WHERE id!='.$id.' AND root_msg_id='.$root_msg_id);

	q('INSERT INTO fud26_thread
		(forum_id, root_msg_id, last_post_date, last_post_id, moved_to)
	VALUES
		('.$forum_id.', '.$root_msg_id.', '.$last_post_date.', '.$last_post_id.', '.$to_forum.')');

	rebuild_forum_view($forum_id);
	rebuild_forum_view($to_forum);

	$p = array();
	$c = q('SELECT poll_id FROM fud26_msg WHERE thread_id='.$id.' AND apr=1 AND poll_id>0');
	while ($r = db_rowarr($c)) {
		$p[] = $r[0];
	}
	unset($c);
	if ($p) {
		q('UPDATE fud26_poll SET forum_id='.$to_forum.' WHERE id IN('.implode(',', $p).')');
	}

	if (isset($ll)) {
		db_unlock();
	}
}

function rebuild_forum_view($forum_id, $page=0)
{
	$tm = __request_timestamp__;

	if (!$page) {
		/* De-announce expired announcments and sticky messages */
		$r = q("SELECT fud26_thread.id FROM fud26_thread INNER JOIN fud26_msg ON fud26_thread.root_msg_id=fud26_msg.id WHERE fud26_thread.forum_id=".$forum_id." AND thread_opt>=2 AND (fud26_msg.post_stamp+fud26_thread.orderexpiry)<=".$tm);
		while ($tid = db_rowarr($r)) {
			q("UPDATE fud26_thread SET orderexpiry=0, thread_opt=thread_opt & ~ (2|4) WHERE id=".$tid[0]);
		}
		unset($r);

		/* Remove expired moved thread pointers */
		q('DELETE FROM fud26_thread WHERE forum_id='.$forum_id.' AND last_post_date<'.($tm-86400*$GLOBALS['MOVED_THR_PTR_EXPIRY']).' AND moved_to!=0');
		if (($aff_rows = db_affected())) {
			q('UPDATE fud26_forum SET thread_count=thread_count-'.$aff_rows.' WHERE id='.$forum_id);
		}
	}

	if (!db_locked()) {
		$ll = 1;
	        db_lock('fud26_thread_view WRITE, fud26_thread WRITE, fud26_msg WRITE');
	}

	if (__dbtype__ == 'mysql') {
		if ($page) {
			q('DELETE FROM fud26_thread_view WHERE forum_id='.$forum_id.' AND page<'.($page+1));
			q("INSERT INTO fud26_thread_view (thread_id,forum_id,page) SELECT fud26_thread.id, ".$forum_id.", 2147483645 FROM fud26_thread INNER JOIN fud26_msg ON fud26_thread.root_msg_id=fud26_msg.id WHERE forum_id=".$forum_id." AND fud26_msg.apr=1 ORDER BY (CASE WHEN thread_opt>=2 AND (fud26_msg.post_stamp+fud26_thread.orderexpiry>".$tm." OR fud26_thread.orderexpiry=0) THEN 4294967294 ELSE fud26_thread.last_post_date END) DESC, fud26_thread.last_post_id DESC LIMIT 0, ".($GLOBALS['THREADS_PER_PAGE']*$page));
			q('UPDATE fud26_thread_view SET page=CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].'), pos=pos-(CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].')-1)*'.$GLOBALS['THREADS_PER_PAGE'].' WHERE forum_id='.$forum_id.' AND page=2147483645');
		} else {
			q('DELETE FROM fud26_thread_view WHERE forum_id='.$forum_id);
			q("INSERT INTO fud26_thread_view (thread_id,forum_id,page) SELECT fud26_thread.id, ".$forum_id.", 2147483645 FROM fud26_thread INNER JOIN fud26_msg ON fud26_thread.root_msg_id=fud26_msg.id WHERE forum_id=".$forum_id." AND fud26_msg.apr=1 ORDER BY (CASE WHEN thread_opt>=2 AND (fud26_msg.post_stamp+fud26_thread.orderexpiry>".$tm." OR fud26_thread.orderexpiry=0) THEN 4294967294 ELSE fud26_thread.last_post_date END) DESC, fud26_thread.last_post_id DESC");
			q('UPDATE fud26_thread_view SET page=CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].'), pos=pos-(CEILING(pos/'.$GLOBALS['THREADS_PER_PAGE'].')-1)*'.$GLOBALS['THREADS_PER_PAGE'].' WHERE forum_id='.$forum_id);
		}
	} else if (__dbtype__ == 'pgsql') {
		$tmp_tbl_name = "fud26_ftvt_".get_random_value();
		q("CREATE TEMP TABLE ".$tmp_tbl_name." ( forum_id INT NOT NULL, page INT NOT NULL, thread_id INT NOT NULL, pos SERIAL)");

		if ($page) {
			q("DELETE FROM fud26_thread_view WHERE forum_id=".$forum_id." AND page<".($page+1));
			q("INSERT INTO ".$tmp_tbl_name." (thread_id,forum_id,page) SELECT fud26_thread.id, ".$forum_id.", 2147483647 FROM fud26_thread INNER JOIN fud26_msg ON fud26_thread.root_msg_id=fud26_msg.id WHERE forum_id=".$forum_id." AND fud26_msg.apr=1 ORDER BY (CASE WHEN thread_opt>=2 AND (fud26_msg.post_stamp+fud26_thread.orderexpiry>".$tm." OR fud26_thread.orderexpiry=0) THEN 2147483647 ELSE fud26_thread.last_post_date END) DESC, fud26_thread.last_post_id DESC LIMIT ".($GLOBALS['THREADS_PER_PAGE']*$page));
		} else {
			q("DELETE FROM fud26_thread_view WHERE forum_id=".$forum_id);
			q("INSERT INTO ".$tmp_tbl_name." (thread_id,forum_id,page) SELECT fud26_thread.id, ".$forum_id.", 2147483647 FROM fud26_thread INNER JOIN fud26_msg ON fud26_thread.root_msg_id=fud26_msg.id WHERE forum_id=".$forum_id." AND fud26_msg.apr=1 ORDER BY (CASE WHEN thread_opt>=2 AND (fud26_msg.post_stamp+fud26_thread.orderexpiry>".$tm." OR fud26_thread.orderexpiry=0) THEN 2147483647 ELSE fud26_thread.last_post_date END) DESC, fud26_thread.last_post_id DESC");
		}

		q("INSERT INTO fud26_thread_view (thread_id,forum_id,page,pos) SELECT thread_id,forum_id,CEIL(pos/".$GLOBALS['THREADS_PER_PAGE'].".0),(pos-(CEIL(pos/".$GLOBALS['THREADS_PER_PAGE'].".0)-1)*".$GLOBALS['THREADS_PER_PAGE'].") FROM ".$tmp_tbl_name);
		q("DROP TABLE ".$tmp_tbl_name);
	}

	if (isset($ll)) {
		db_unlock();
	}
}
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

	$th = isset($_GET['th']) ? (int)$_GET['th'] : (isset($_POST['th']) ? (int)$_POST['th'] : 0);
	if (!$th) {
		invl_inp_err();
	}

	/* permission check */
	if (!$is_a) {
		$perms = db_saq('SELECT mm.id, '.(_uid ? ' (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco ' : ' g1.group_cache_opt AS gco ').'
				FROM fud26_thread t
				LEFT JOIN fud26_mod mm ON mm.user_id='._uid.' AND mm.forum_id=t.forum_id
				'.(_uid ? 'INNER JOIN fud26_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=t.forum_id LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=t.forum_id' : 'INNER JOIN fud26_group_cache g1 ON g1.user_id=0 AND g1.resource_id=t.forum_id').'
				WHERE t.id='.$th);
		if (!$perms || !$perms[0] && !($perms[1] & 2048)) {
			std_error('access');
		}
	}

	$forum = isset($_POST['forum']) ? (int)$_POST['forum'] : 0;

	if ($forum && !empty($_POST['new_title']) && isset($_POST['sel_th']) && ($mc = count($_POST['sel_th']))) {
		/* we need to make sure that the user has access to destination forum */
		if (!$is_a && !q_singleval('SELECT f.id FROM fud26_forum f LEFT JOIN fud26_mod mm ON mm.user_id='._uid.' AND mm.forum_id=f.id '.(_uid ? 'INNER JOIN fud26_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=f.id LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id' : 'INNER JOIN fud26_group_cache g1 ON g1.user_id=0 AND g1.resource_id=f.id').' WHERE f.id='.$forum.' AND (mm.id IS NOT NULL OR '.(_uid ? ' ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END)' : ' (g1.group_cache_opt').' & 4) > 0)')) {
			std_error('access');
		}

		foreach ($_POST['sel_th'] as $k => $v) {
			if (!(int)$v) {
				unset($_POST['sel_th'][$k]);
			}
			$_POST['sel_th'][$k] = (int) $v;
		}
		/* sanity check */
		if (empty($_POST['sel_th'])) {
			if ($FUD_OPT_2 & 32768) {
				header('Location: http://timeweather.net/forum/index.php/t/'.$th.'/'._rsidl);
			} else {
				header('Location: http://timeweather.net/forum/index.php?t='.d_thread_view.'&th='.$th.'&'._rsidl);
			}
			exit;
		}

		if (isset($_POST['btn_selected'])) {
			sort($_POST['sel_th']);
			$mids = implode(',', $_POST['sel_th']);
			$start = $_POST['sel_th'][0];
			$end = $_POST['sel_th'][($mc - 1)];
		} else {
			$a = array();
			$c = uq('SELECT id FROM fud26_msg WHERE thread_id='.$th.' AND id NOT IN('.implode(',', $_POST['sel_th']).') AND apr=1 ORDER BY post_stamp ASC');
			while ($r = db_rowarr($c)) {
				$a[] = $r[0];
			}
			/* sanity check */
			if (!$a) {
				if ($FUD_OPT_2 & 32768) {
					header('Location: http://timeweather.net/forum/index.php/t/'.$th.'/'._rsidl);
				} else {
					header('Location: http://timeweather.net/forum/index.php?t='.d_thread_view.'&th='.$th.'&'._rsidl);
				}
				exit;
			}
			$mids = implode(',', $a);
			$mc = count($a);
			$start = $a[0];
			$end = $a[($mc - 1)];
		}

		/* fetch all relevant information */
		$data = db_sab('SELECT
				t.id, t.forum_id, t.replies, t.root_msg_id, t.last_post_id, t.last_post_date,
				m1.post_stamp AS new_th_lps, m1.id AS new_th_lpi,
				m2.post_stamp AS old_fm_lpd,
				f1.last_post_id AS src_lpi,
				f2.last_post_id AS dst_lpi
				FROM fud26_thread t
				INNER JOIN fud26_forum f1 ON t.forum_id=f1.id
				INNER JOIN fud26_forum f2 ON f2.id='.$forum.'
				LEFT JOIN fud26_msg m1 ON m1.id='.$end.'
				LEFT JOIN fud26_msg m2 ON m2.id=f2.last_post_id
		WHERE t.id='.$th);

		/* sanity check */
		if (!$data->replies) {
			if ($FUD_OPT_2 & 32768) {
				header('Location: http://timeweather.net/forum/index.php/t/'.$th.'/'._rsidl);
			} else {
				header('Location: http://timeweather.net/forum/index.php?t='.d_thread_view.'&th='.$th.'&'._rsidl);
			}
			exit;
		}

		apply_custom_replace($_POST['new_title']);

		if ($mc != ($data->replies + 1)) { /* check that we need to move the entire thread */
			db_lock('fud26_thread_view WRITE, fud26_thread WRITE, fud26_forum WRITE, fud26_msg WRITE, fud26_poll WRITE');

			$new_th = th_add($start, $forum, $data->new_th_lps, 0, 0, ($mc - 1), $data->new_th_lpi);

			/* Deal with the new thread */
			q('UPDATE fud26_msg SET thread_id='.$new_th.' WHERE id IN ('.$mids.')');
			q('UPDATE fud26_msg SET reply_to='.$start.' WHERE thread_id='.$new_th.' AND reply_to NOT IN ('.$mids.')');
			q("UPDATE fud26_msg SET reply_to=0, subject='".addslashes(htmlspecialchars($_POST['new_title']))."' WHERE id=".$start);

			/* Deal with the old thread */
			list($lpi, $lpd) = db_saq("SELECT id, post_stamp FROM fud26_msg WHERE thread_id=".$data->id." AND apr=1 ORDER BY post_stamp DESC LIMIT 1");$old_root_msg_id = q_singleval("SELECT id FROM fud26_msg WHERE thread_id=".$data->id." AND apr=1 ORDER BY post_stamp ASC LIMIT 1");
			q("UPDATE fud26_msg SET reply_to=".$old_root_msg_id." WHERE thread_id=".$data->id." AND reply_to IN(".$mids.")");
			q('UPDATE fud26_msg SET reply_to=0 WHERE id='.$old_root_msg_id);
			q('UPDATE fud26_thread SET root_msg_id='.$old_root_msg_id.', replies=replies-'.$mc.', last_post_date='.$lpd.', last_post_id='.$lpi.' WHERE id='.$data->id);

			if ($forum != $data->forum_id) {
				$p = array();
				$c = q('SELECT poll_id FROM fud26_msg WHERE thread_id='.$new_th.' AND apr=1 AND poll_id>0');
				while ($r = db_rowarr($c)) {
					$p[] = $r[0];
				}
				unset($c);
				if ($p) {
					q('UPDATE fud26_poll SET forum_id='.$data->forum_id.' WHERE id IN('.implode(',', $p).')');
				}

				/* deal with the source forum */
				if ($data->src_lpi != $data->last_post_id || $data->last_post_date <= $lpd) {
					q('UPDATE fud26_forum SET post_count=post_count-'.$mc.' WHERE id='.$data->forum_id);
				} else {
					q('UPDATE fud26_forum SET post_count=post_count-'.$mc.', last_post_id='.th_frm_last_post_id($data->forum_id, $data->id).' WHERE id='.$data->forum_id);
				}

				/* deal with destination forum */
				if ($data->old_fm_lpd > $data->new_th_lps) {
					q('UPDATE fud26_forum SET post_count=post_count+'.$mc.', thread_count=thread_count+1 WHERE id='.$forum);
				} else {
					q('UPDATE fud26_forum SET post_count=post_count+'.$mc.', thread_count=thread_count+1, last_post_id='.$data->new_th_lpi.' WHERE id='.$forum);
				}

				rebuild_forum_view($forum);
			} else {
				if ($data->src_lpi == $data->last_post_id && $data->last_post_date >= $lpd) {
					q('UPDATE fud26_forum SET thread_count=thread_count+1 WHERE id='.$data->forum_id);
				} else {
					q('UPDATE fud26_forum SET thread_count=thread_count+1, last_post_id='.$data->new_th_lpi.' WHERE id='.$data->forum_id);
				}
			}
			rebuild_forum_view($data->forum_id);
			db_unlock();
			logaction(_uid, 'THRSPLIT', $new_th);
			$th_id = $new_th;
		} else { /* moving entire thread */
			q("UPDATE fud26_msg SET subject='".addslashes(htmlspecialchars($_POST['new_title']))."' WHERE id=".$data->root_msg_id);
			if ($forum != $data->forum_id) {
				th_move($data->id, $forum, $data->root_msg_id, $thr->forum_id, $data->last_post_date, $data->last_post_id);

				if ($data->src_lpi == $data->last_post_id) {
					q('UPDATE fud26_forum SET last_post_id='.th_frm_last_post_id($data->forum_id, $data->id).' WHERE id='.$data->forum_id);
				}
				if ($data->old_fm_lpd < $data->last_post_date) {
					q('UPDATE fud26_forum SET last_post_id='.$data->last_post_id.' WHERE id='.$forum);
				}

				logaction(_uid, 'THRMOVE', $th);
			}
			$th_id = $data->id;
		}
		if ($FUD_OPT_2 & 32768) {
			header('Location: http://timeweather.net/forum/index.php/t/'.$th_id.'/'._rsidl);
		} else {
			header('Location: http://timeweather.net/forum/index.php?t='.d_thread_view.'&th='.$th_id.'&'._rsidl);
		}
		exit;
	}
	/* fetch a list of accesible forums */
	$c = uq('SELECT f.id, f.name
			FROM fud26_forum f
			INNER JOIN fud26_fc_view v ON v.f=f.id
			INNER JOIN fud26_cat c ON c.id=f.cat_id
			LEFT JOIN fud26_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
			INNER JOIN fud26_group_cache g1 ON g1.resource_id=f.id AND g1.user_id='.(_uid ? '2147483647' : '0').'
			'.(_uid ? ' LEFT JOIN fud26_group_cache g2 ON g2.resource_id=f.id AND g2.user_id='._uid : '').'
			'.($is_a ? '' : ' WHERE mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NULL THEN g1.group_cache_opt ELSE g2.group_cache_opt END) & 4) > 0').'
			ORDER BY v.id');
	$vl = $kl = '';
	while ($r = db_rowarr($c)) {
		$vl .= $r[0] . "\n";
		$kl .= $r[1] . "\n";
	}

	if (!$forum) {
		$forum = q_singleval('SELECT forum_id FROM fud26_thread WHERE id='.$th);
	}

	$forum_sel = tmpl_draw_select_opt(rtrim($vl), rtrim($kl), $forum);

	$c = uq("SELECT m.id, m.foff, m.length, m.file_id, m.subject, m.post_stamp, u.alias FROM fud26_msg m LEFT JOIN fud26_users u ON m.poster_id=u.id WHERE m.thread_id=".$th." AND m.apr=1 ORDER BY m.post_stamp ASC");

	$anon_alias = htmlspecialchars($ANON_NICK);

	$msg_entry = '';
	while ($r = db_rowobj($c)) {
		$msg_entry .= '<tr>
<td class="RowStyleC vt ac"><input type="checkbox" name="sel_th[]" value="'.$r->id.'"></td>
<td class="RowStyleA">
<table cellspacing=1 cellpadding=2 class="ContentTable">
<tr class="RowStyleB">
	<td class="SmallText">
	<b>Message By:</b> '.($r->alias ? $r->alias.'' : $anon_alias.'' )  .'<br />
	<b>Posted On:</b> '.strftime("%a, %d %B %Y %H:%M", $r->post_stamp).'<br />
	<b>Subject:</b> '.$r->subject.'
	</td>
</tr>
<tr class="RowStyleA"><td>'.read_msg_body($r->foff, $r->length, $r->file_id).'</td></tr>
</table>
</td>
</tr>';
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
<form name="split_th" action="index.php?t=split_th" method="post"><?php echo _hs; ?><input type="hidden" name="th" value="<?php echo $th; ?>">
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th class="wa" colspan=2>Thread Split Control Panel</th></tr>
<tr class="RowStyleA">
	<td class="al fb">New topic title:</td>
	<td ><input type="text" name="new_title" value="" size=50></td>
</tr>
<tr class="RowStyleA">
	<td class="al fb">Forum:</td>
	<td class="al"><select name="forum"><?php echo $forum_sel; ?></select></td>
</tr>
<tr class="RowStyleC">
	<td colspan=2 class="ac">
		<input type="submit" class="button" name="btn_selected" value="Split selected messages">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" class="button" name="btn_unselected" value="Split unselected messages">
	</td>
</tr>
</table>
<br />
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th class="nw">Select</th><th width="100%">Messages</th></tr>
<?php echo $msg_entry; ?>
</table>
<br />
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr class="RowStyleC">
	<td colspan=2 class="ac">
		<input type="submit" class="button" name="btn_selected" value="Split selected messages">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" class="button" name="btn_unselected" value="Split unselected messages">
	</td>
</tr>
</table>
</form>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>