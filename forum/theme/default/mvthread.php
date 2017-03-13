<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: mvthread.php.t,v 1.32 2004/11/24 19:53:35 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

	define('plain_form', 1);

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function logaction($user_id, $res, $res_id=0, $action=null)
{
	q('INSERT INTO fud26_action_log (logtime, logaction, user_id, a_res, a_res_id)
		VALUES('.__request_timestamp__.', '.strnull($action).', '.$user_id.', '.strnull($res).', '.(int)$res_id.')');
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

	$th = isset($_POST['th']) ? (int)$_POST['th'] : (isset($_GET['th']) ? (int)$_GET['th'] : 0);
	$thx = isset($_POST['thx']) ? (int)$_POST['thx'] : (isset($_GET['thx']) ? (int)$_GET['thx'] : 0);
	$to = isset($_GET['to']) ? (int)$_GET['to'] : 0;

	/* thread x-change */
	if ($th && $thx) {
		if (!$GLOBALS['is_post'] && !sq_check(0, $usr->sq)) {
			return;
		}

		if (!$is_a && q_singleval('SELECT id FROM fud26_mod WHERE forum_id='.$thx.' AND user_id='._uid)) {
			std_error('access');
		}

		if (!empty($_POST['reason_msg'])) {
			fud_use('thrx_adm.inc', true);
			if (thx_add($_POST['reason_msg'], $th, $thx, _uid)) {
				logaction(_uid, 'THRXREQUEST', $th);
			}
			exit('<html><script>window.close();</script></html>');
		} else {
			$thr = db_sab('SELECT f.name AS frm_name, m.subject FROM fud26_forum f INNER JOIN fud26_thread t ON t.id='.$th.' INNER JOIN fud26_msg m ON t.root_msg_id=m.id WHERE f.id='.$thx);
			$table_data = '<tr><td class="small fb">'.$thr->frm_name.'</td></tr>
<tr><td class="small">Why do you wish the topic to be moved?<br /><textarea name="reason_msg" rows=7 cols=30></textarea><td></tr>
<tr><td class="ar"><input type="submit" class="button" name="submit" value="Submit Request"></td></tr>';
		}
	}

	/* moving a thread */
	if ($th && $to) {
		if (!$GLOBALS['is_post'] && !sq_check(0, $usr->sq)) {
			return;
		}

		$thr = db_sab('SELECT
				t.id, t.forum_id, t.last_post_id, t.root_msg_id, t.last_post_date, t.last_post_id,
				f1.last_post_id AS f1_lpi, f2.last_post_id AS f2_lpi,
				'.($is_a ? ' 1 AS mod1, 1 AS mod2' : ' mm1.id AS mod1, mm2.id AS mod2').',
				(CASE WHEN gs2.id IS NOT NULL THEN gs2.group_cache_opt ELSE gs1.group_cache_opt END) AS sgco,
				(CASE WHEN gd2.id IS NOT NULL THEN gd2.group_cache_opt ELSE gd1.group_cache_opt END) AS dgco
			FROM fud26_thread t
			INNER JOIN fud26_forum f1 ON t.forum_id=f1.id
			INNER JOIN fud26_forum f2 ON f2.id='.$to.'
			LEFT JOIN fud26_mod mm1 ON mm1.forum_id=f1.id AND mm1.user_id='._uid.'
			LEFT JOIN fud26_mod mm2 ON mm2.forum_id=f2.id AND mm2.user_id='._uid.'
			INNER JOIN fud26_group_cache gs1 ON gs1.user_id=2147483647 AND gs1.resource_id=f1.id
			LEFT JOIN fud26_group_cache gs2 ON gs2.user_id='._uid.' AND gs2.resource_id=f1.id
			INNER JOIN fud26_group_cache gd1 ON gd1.user_id=2147483647 AND gd1.resource_id=f2.id
			LEFT JOIN fud26_group_cache gd2 ON gd2.user_id='._uid.' AND gd2.resource_id=f2.id
			WHERE t.id='.$th);

		if (!$thr) {
			invl_inp_err();
		}

		if ((!$thr->mod1 && !($thr->sgco & 8192)) || (!$thr->mod2 && !($thr->dgco & 8192))) {
			std_error('access');
		}

		/* fetch data about source thread & forum */
		$src_frm_lpi = (int) $thr->f1_lpi;
		/* fetch data about dest forum */
		$dst_frm_lpi = (int) $thr->f2_lpi;

		th_move($thr->id, $to, $thr->root_msg_id, $thr->forum_id, $thr->last_post_date, $thr->last_post_id);

		if ($src_frm_lpi == $thr->last_post_id) {
			$mid = (int) q_singleval('SELECT MAX(last_post_id) FROM fud26_thread t INNER JOIN fud26_msg m ON t.root_msg_id=m.id WHERE t.forum_id='.$thr->forum_id.' AND t.moved_to=0 AND m.apr=1');
			q('UPDATE fud26_forum SET last_post_id='.$mid.' WHERE id='.$thr->forum_id);
		}

		if ($dst_frm_lpi < $thr->last_post_id) {
			q('UPDATE fud26_forum SET last_post_id='.$thr->last_post_id.' WHERE id='.$to);
		}

		logaction(_uid, 'THRMOVE', $th);

		if ($FUD_OPT_2 & 32768 && !empty($_SERVER['PATH_INFO'])) {
			exit("<html><script>window.opener.location='http://timeweather.net/forum/index.php/f/".$thr->forum_id."/"._rsid."'; window.close();</script></html>");
		} else {
			exit("<html><script>window.opener.location='http://timeweather.net/forum/index.php?t=".t_thread_view."&"._rsid."&frm_id=".$thr->forum_id."'; window.close();</script></html>");
		}
	}



	if (!$thx) {
		$thr = db_sab('SELECT f.name AS frm_name, m.subject, t.forum_id, t.id FROM fud26_thread t INNER JOIN fud26_forum f ON f.id=t.forum_id INNER JOIN fud26_msg m ON t.root_msg_id=m.id WHERE t.id='.$th);

		$c = uq('SELECT f.name, f.id, c.id, m.user_id, (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco
			FROM fud26_forum f
			INNER JOIN fud26_fc_view v ON v.f=f.id
			INNER JOIN fud26_cat c ON c.id=v.c
			LEFT JOIN fud26_mod m ON m.user_id='._uid.' AND m.forum_id=f.id
			INNER JOIN fud26_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=f.id
			LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
			WHERE c.id!=0 AND f.id!='.$thr->forum_id.($is_a ? '' : ' AND (CASE WHEN m.user_id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 1) > 0 THEN 1 ELSE 0 END)=1').'
			ORDER BY v.id');

		$table_data = $oldc = '';

		require $GLOBALS['FORUM_SETTINGS_PATH'].'cat_cache.inc';
		while ($r = db_rowarr($c)) {
			if ($oldc != $r[2]) {
				while (list($k, $i) = each($cat_cache)) {
					$table_data .= '<tr><td class="mvTc" style="padding-left: '.($tabw = ($i[0] * 10 + 2)).'px">'.$i[1].'</td></tr>';
					if ($k == $r[2]) {
						break;
					}
				}
				$oldc = $r[2];
			}

			if ($r[3] || $is_a || $r[4] & 8192) {
				$table_data .= '<tr><td style="padding-left: '.$tabw.'px"><a href="index.php?t=mvthread&amp;th='.$thr->id.'&amp;to='.$r[1].'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">'.$r[0].'</a></td></tr>';
			} else {
				$table_data .= '<tr><td style="padding-left: '.$tabw.'px">'.$r[0].' [<a href="index.php?t=mvthread&amp;th='.$thr->id.'&amp;'._rsid.'&amp;thx='.$r[1].'&amp;SQ='.$GLOBALS['sq'].'">request a move</a>]</td></tr>';
			}
		}
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<title><?php echo $GLOBALS['FORUM_TITLE'].$TITLE_EXTRA; ?></title>
<BASE HREF="http://timeweather.net/forum/">
<script language="JavaScript" src="lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="theme/default/forum.css" type="text/css">
</head>
<body>
<table class="wa" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<form action="index.php?t=mvthread" name="mvthread" method="post">
<table cellspacing=0 cellpadding=3 class="wa dashed">
<tr><td class="small">Move topic <b><?php echo $thr->subject; ?></b> to:</td></tr>
<?php echo $table_data; ?>
</table>
<?php echo _hs; ?><input type="hidden" name="th" value="<?php echo $th; ?>"><input type="hidden" name="thx" value="<?php echo $thx; ?>"></form>
</td></tr></table></body></html>