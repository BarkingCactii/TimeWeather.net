<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: mmod.php.t,v 1.38 2005/03/15 15:12:36 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}function msg_get($id)
{
	if (($r = db_sab('SELECT * FROM fud26_msg WHERE id='.$id))) {
		$r->body = read_msg_body($r->foff, $r->length, $r->file_id);
		return $r;
	}
	error_dialog('Invalid Message', 'The message you are trying to view does not exist.');
}

function poll_cache_rebuild($poll_id, &$data)
{
	if (!$poll_id) {
		$data = null;
		return;
	}

	if (!$data) { /* rebuild from cratch */
		$c = uq('SELECT id, name, count FROM fud26_poll_opt WHERE poll_id='.$poll_id);
		while ($r = db_rowarr($c)) {
			$data[$r[0]] = array($r[1], $r[2]);
		}
		if (!$data) {
			$data = null;
		}
	} else { /* register single vote */
		$data[$poll_id][1] += 1;
	}
}function frm_updt_counts($frm_id, $replies, $threads, $last_post_id)
{
	$threads	= !$threads ? '' : ', thread_count=thread_count+'.$threads;
	$last_post_id	= !$last_post_id ? '' : ', last_post_id='.$last_post_id;

	q('UPDATE fud26_forum SET post_count=post_count+'.$replies.$threads.$last_post_id.' WHERE id='.$frm_id);
}class fud_msg
{
	var $id, $thread_id, $poster_id, $reply_to, $ip_addr, $host_name, $post_stamp, $subject, $attach_cnt, $poll_id,
	    $update_stamp, $icon, $apr, $updated_by, $login, $length, $foff, $file_id, $msg_opt,
	    $file_id_preview, $length_preview, $offset_preview, $body, $mlist_msg_id;
}

$GLOBALS['CHARSET'] = 'ISO-8859-15';

class fud_msg_edit extends fud_msg
{
	function add_reply($reply_to, $th_id=null, $perm, $autoapprove=1)
	{
		if ($reply_to) {
			$this->reply_to = $reply_to;
			$fd = db_saq('SELECT t.forum_id, f.message_threshold, f.forum_opt FROM fud26_msg m INNER JOIN fud26_thread t ON m.thread_id=t.id INNER JOIN fud26_forum f ON f.id=t.forum_id WHERE m.id='.$reply_to);
		} else {
			$fd = db_saq('SELECT t.forum_id, f.message_threshold, f.forum_opt FROM fud26_thread t INNER JOIN fud26_forum f ON f.id=t.forum_id WHERE t.id='.$th_id);
		}

		return $this->add($fd[0], $fd[1], $fd[2], $perm, $autoapprove);
	}

	function add($forum_id, $message_threshold, $forum_opt, $perm, $autoapprove=1)
	{
		if (!$this->post_stamp) {
			$this->post_stamp = __request_timestamp__;
		}

		if (!isset($this->ip_addr)) {
			$this->ip_addr = get_ip();
		}
		$this->host_name = $GLOBALS['FUD_OPT_1'] & 268435456 ? "'".addslashes(get_host($this->ip_addr))."'" : 'NULL';
		$this->thread_id = isset($this->thread_id) ? $this->thread_id : 0;
		$this->reply_to = isset($this->reply_to) ? $this->reply_to : 0;

		$file_id = write_body($this->body, $length, $offset, $forum_id);

		/* determine if preview needs building */
		if ($message_threshold && $message_threshold < strlen($this->body)) {
			$thres_body = trim_html($this->body, $message_threshold);
			$file_id_preview = write_body($thres_body, $length_preview, $offset_preview, $forum_id);
		} else {
			$file_id_preview = $offset_preview = $length_preview = 0;
		}

		poll_cache_rebuild($this->poll_id, $poll_cache);
		$poll_cache = ($poll_cache ? serialize($poll_cache) : null);

		$this->id = db_qid("INSERT INTO fud26_msg (
			thread_id,
			poster_id,
			reply_to,
			ip_addr,
			host_name,
			post_stamp,
			subject,
			attach_cnt,
			poll_id,
			icon,
			msg_opt,
			file_id,
			foff,
			length,
			file_id_preview,
			offset_preview,
			length_preview,
			mlist_msg_id,
			poll_cache
		) VALUES(
			".$this->thread_id.",
			".$this->poster_id.",
			".(int)$this->reply_to.",
			'".$this->ip_addr."',
			".$this->host_name.",
			".$this->post_stamp.",
			".strnull(addslashes($this->subject)).",
			".(int)$this->attach_cnt.",
			".(int)$this->poll_id.",
			".strnull(addslashes($this->icon)).",
			".$this->msg_opt.",
			".$file_id.",
			".(int)$offset.",
			".(int)$length.",
			".$file_id_preview.",
			".$offset_preview.",
			".$length_preview.",
			".strnull($this->mlist_msg_id).",
			".strnull(addslashes($poll_cache))."
		)");

		$thread_opt = (int) ($perm & 4096 && isset($_POST['thr_locked']));

		if (!$this->thread_id) { /* new thread */
			if ($perm & 64 && isset($_POST['thr_ordertype'], $_POST['thr_orderexpiry'])) {
				if ((int)$_POST['thr_ordertype']) {
					$thread_opt |= (int) $_POST['thr_ordertype'];
					$thr_orderexpiry = (int) $_POST['thr_orderexpiry'];
				}
			}

			$this->thread_id = th_add($this->id, $forum_id, $this->post_stamp, $thread_opt, (isset($thr_orderexpiry) ? $thr_orderexpiry : 0));

			q('UPDATE fud26_msg SET thread_id='.$this->thread_id.' WHERE id='.$this->id);
		} else {
			th_lock($this->thread_id, $thread_opt & 1);
		}

		if ($autoapprove && $forum_opt & 2) {
			$this->approve($this->id);
		}

		return $this->id;
	}

	function sync($id, $frm_id, $message_threshold, $perm)
	{
		$file_id = write_body($this->body, $length, $offset, $frm_id);

		/* determine if preview needs building */
		if ($message_threshold && $message_threshold < strlen($this->body)) {
			$thres_body = trim_html($this->body, $message_threshold);
			$file_id_preview = write_body($thres_body, $length_preview, $offset_preview, $forum_id);
		} else {
			$file_id_preview = $offset_preview = $length_preview = 0;
		}

		poll_cache_rebuild($this->poll_id, $poll_cache);
		$poll_cache = ($poll_cache ? serialize($poll_cache) : null);

		q("UPDATE fud26_msg SET
			file_id=".$file_id.",
			foff=".(int)$offset.",
			length=".(int)$length.",
			mlist_msg_id=".strnull(addslashes($this->mlist_msg_id)).",
			file_id_preview=".$file_id_preview.",
			offset_preview=".$offset_preview.",
			length_preview=".$length_preview.",
			updated_by=".$id.",
			msg_opt=".$this->msg_opt.",
			attach_cnt=".(int)$this->attach_cnt.",
			poll_id=".(int)$this->poll_id.",
			update_stamp=".__request_timestamp__.",
			icon=".strnull(addslashes($this->icon))." ,
			poll_cache=".strnull(addslashes($poll_cache)).",
			subject=".strnull(addslashes($this->subject))."
		WHERE id=".$this->id);

		/* determine wether or not we should deal with locked & sticky stuff
		 * current approach may seem a little redundant, but for (most) users who
		 * do not have access to locking & sticky this eliminated a query.
		 */
		$th_data = db_saq('SELECT orderexpiry, thread_opt, root_msg_id FROM fud26_thread WHERE id='.$this->thread_id);
		$locked = (int) isset($_POST['thr_locked']);
		if (isset($_POST['thr_ordertype'], $_POST['thr_orderexpiry']) || (($th_data[1] ^ $locked) & 1)) {
			$thread_opt = (int) $th_data[1];
			$orderexpiry = isset($_POST['thr_orderexpiry']) ? (int) $_POST['thr_orderexpiry'] : 0;

			/* confirm that user has ability to change lock status of the thread */
			if ($perm & 4096) {
				if ($locked && !($thread_opt & $locked)) {
					$thread_opt |= 1;
				} else if (!$locked && $thread_opt & 1) {
					$thread_opt &= ~1;
				}
			}

			/* confirm that user has ability to change sticky status of the thread */
			if ($th_data[2] == $this->id && isset($_POST['thr_ordertype'], $_POST['thr_orderexpiry']) && $perm & 64) {
				if (!$_POST['thr_ordertype'] && $thread_opt>1) {
					$orderexpiry = 0;
					$thread_opt &= ~6;
				} else if ($thread_opt < 2 && (int) $_POST['thr_ordertype']) {
					$thread_opt |= $_POST['thr_ordertype'];
				} else if (!($thread_opt & (int) $_POST['thr_ordertype'])) {
					$thread_opt = $_POST['thr_ordertype'] | ($thread_opt & 1);
				}
			}

			/* Determine if any work needs to be done */
			if ($thread_opt != $th_data[1] || $orderexpiry != $th_data[0]) {
				q("UPDATE fud26_thread SET thread_opt=".$thread_opt.", orderexpiry=".$orderexpiry." WHERE id=".$this->thread_id);
				/* Avoid rebuilding the forum view whenever possible, since it's a rather slow process
				 * Only rebuild if expiry time has changed or message gained/lost sticky status
				 */
				$diff = $thread_opt ^ $th_data[1];
				if (($diff > 1 && !($diff & 6)) || $orderexpiry != $th_data[0]) {
					rebuild_forum_view($frm_id);
				}
			}
		}

		if ($GLOBALS['FUD_OPT_1'] & 16777216) {
			delete_msg_index($this->id);
			index_text((preg_match('!^Re: !i', $this->subject) ? '': $this->subject), $this->body, $this->id);
		}
	}

	function delete($rebuild_view=1, $mid=0, $th_rm=0)
	{
		if (!$mid) {
			$mid = $this->id;
		}

		if (!db_locked()) {
			db_lock('fud26_thr_exchange WRITE, fud26_thread_view WRITE, fud26_level WRITE, fud26_forum WRITE, fud26_forum_read WRITE, fud26_thread WRITE, fud26_msg WRITE, fud26_attach WRITE, fud26_poll WRITE, fud26_poll_opt WRITE, fud26_poll_opt_track WRITE, fud26_users WRITE, fud26_thread_notify WRITE, fud26_msg_report WRITE, fud26_thread_rate_track WRITE');
			$ll = 1;
		}

		if (!($del = db_sab('SELECT
				fud26_msg.id, fud26_msg.attach_cnt, fud26_msg.poll_id, fud26_msg.thread_id, fud26_msg.reply_to, fud26_msg.apr, fud26_msg.poster_id,
				fud26_thread.replies, fud26_thread.root_msg_id AS root_msg_id, fud26_thread.last_post_id AS thread_lip, fud26_thread.forum_id,
				fud26_forum.last_post_id AS forum_lip FROM fud26_msg LEFT JOIN fud26_thread ON fud26_msg.thread_id=fud26_thread.id LEFT JOIN fud26_forum ON fud26_thread.forum_id=fud26_forum.id WHERE fud26_msg.id='.$mid))) {
			if (isset($ll)) {
				db_unlock();
			}
			return;
		}

		/* attachments */
		if ($del->attach_cnt) {
			$res = q('SELECT location FROM fud26_attach WHERE message_id='.$mid." AND attach_opt=0");
			while ($loc = db_rowarr($res)) {
				@unlink($loc[0]);
			}
			unset($res);
			q('DELETE FROM fud26_attach WHERE message_id='.$mid." AND attach_opt=0");
		}

		q('DELETE FROM fud26_msg_report WHERE msg_id='.$mid);

		if ($del->poll_id) {
			poll_delete($del->poll_id);
		}

		/* check if thread */
		if ($del->root_msg_id == $del->id) {
			$th_rm = 1;
			/* delete all messages in the thread if there is more then 1 message */
			if ($del->replies) {
				$rmsg = q('SELECT id FROM fud26_msg WHERE thread_id='.$del->thread_id.' AND id != '.$del->id);
				while ($dim = db_rowarr($rmsg)) {
					fud_msg_edit::delete(false, $dim[0], 1);
				}
				unset($rmsg);
			}

			q('DELETE FROM fud26_thread_notify WHERE thread_id='.$del->thread_id);
			q('DELETE FROM fud26_thread WHERE id='.$del->thread_id);
			q('DELETE FROM fud26_thread_rate_track WHERE thread_id='.$del->thread_id);
			q('DELETE FROM fud26_thr_exchange WHERE th='.$del->thread_id);

			if ($del->apr) {
				/* we need to determine the last post id for the forum, it can be null */
				$lpi = (int) q_singleval('SELECT fud26_thread.last_post_id FROM fud26_thread INNER JOIN fud26_msg ON fud26_thread.last_post_id=fud26_msg.id AND fud26_msg.apr=1 WHERE forum_id='.$del->forum_id.' AND moved_to=0 ORDER BY fud26_msg.post_stamp DESC LIMIT 1');
				q('UPDATE fud26_forum SET last_post_id='.$lpi.', thread_count=thread_count-1, post_count=post_count-'.$del->replies.'-1 WHERE id='.$del->forum_id);
			}
		} else if (!$th_rm  && $del->apr) {
			q('UPDATE fud26_msg SET reply_to='.$del->reply_to.' WHERE thread_id='.$del->thread_id.' AND reply_to='.$mid);

			/* check if the message is the last in thread */
			if ($del->thread_lip == $del->id) {
				list($lpi, $lpd) = db_saq('SELECT id, post_stamp FROM fud26_msg WHERE thread_id='.$del->thread_id.' AND apr=1 AND id!='.$del->id.' ORDER BY post_stamp DESC LIMIT 1');
				q('UPDATE fud26_thread SET last_post_id='.$lpi.', last_post_date='.$lpd.', replies=replies-1 WHERE id='.$del->thread_id);
			} else {
				q('UPDATE fud26_thread SET replies=replies-1 WHERE id='.$del->thread_id);
			}

			/* check if the message is the last in the forum */
			if ($del->forum_lip == $del->id) {
				$lp = db_saq('SELECT fud26_thread.last_post_id, fud26_thread.last_post_date FROM fud26_thread_view INNER JOIN fud26_thread ON fud26_thread_view.forum_id=fud26_thread.forum_id AND fud26_thread_view.thread_id=fud26_thread.id WHERE fud26_thread_view.forum_id='.$del->forum_id.' AND fud26_thread_view.page=1 AND fud26_thread.moved_to=0 ORDER BY fud26_thread.last_post_date DESC LIMIT 1');
				if (!isset($lpd) || $lp[1] > $lpd) {
					$lpi = $lp[0];
				}
				q('UPDATE fud26_forum SET post_count=post_count-1, last_post_id='.$lpi.' WHERE id='.$del->forum_id);
			} else {
				q('UPDATE fud26_forum SET post_count=post_count-1 WHERE id='.$del->forum_id);
			}
		}

		q('DELETE FROM fud26_msg WHERE id='.$mid);

		if ($del->apr) {
			if ($del->poster_id) {
				user_set_post_count($del->poster_id);
			}

			if ($rebuild_view) {
				rebuild_forum_view($del->forum_id);

				/* needed for moved thread pointers */
				$r = q('SELECT forum_id, id FROM fud26_thread WHERE root_msg_id='.$del->root_msg_id);
				while (($res = db_rowarr($r))) {
					if ($th_rm) {
						q('DELETE FROM fud26_thread WHERE id='.$res[1]);
					}
					rebuild_forum_view($res[0]);
				}
				unset($r);
			}
		}

		if (isset($ll)) {
			db_unlock();
		}
	}

	function approve($id)
	{
		/* fetch info about the message, poll (if one exists), thread & forum */
		$mtf = db_sab('SELECT
					m.id, m.poster_id, m.apr, m.subject, m.foff, m.length, m.file_id, m.thread_id, m.poll_id, m.attach_cnt,
					m.post_stamp, m.reply_to, m.mlist_msg_id, m.msg_opt,
					t.forum_id, t.last_post_id, t.root_msg_id, t.last_post_date,
					m2.post_stamp AS frm_last_post_date,
					f.name AS frm_name,
					u.alias, u.email, u.sig,
					n.id AS nntp_id, ml.id AS mlist_id
				FROM fud26_msg m
				INNER JOIN fud26_thread t ON m.thread_id=t.id
				INNER JOIN fud26_forum f ON t.forum_id=f.id
				LEFT JOIN fud26_msg m2 ON f.last_post_id=m2.id
				LEFT JOIN fud26_users u ON m.poster_id=u.id
				LEFT JOIN fud26_mlist ml ON ml.forum_id=f.id AND (ml.mlist_opt & 2) > 0
				LEFT JOIN fud26_nntp n ON n.forum_id=f.id AND (n.nntp_opt & 2) > 0
				WHERE m.id='.$id.' AND m.apr=0');

		/* nothing to do or bad message id */
		if (!$mtf) {
			return;
		}

		if ($mtf->alias) {
			$mtf->alias = reverse_fmt($mtf->alias);
		} else {
			$mtf->alias = $GLOBALS['ANON_NICK'];
		}

		q("UPDATE fud26_msg SET apr=1 WHERE id=".$mtf->id);

		if ($mtf->poster_id) {
			user_set_post_count($mtf->poster_id);
		}

		$last_post_id = $mtf->post_stamp > $mtf->frm_last_post_date ? $mtf->id : 0;

		if ($mtf->root_msg_id == $mtf->id) {	/* new thread */
			rebuild_forum_view($mtf->forum_id);
			$threads = 1;
		} else {				/* reply to thread */
			if ($mtf->post_stamp > $mtf->last_post_date) {
				th_inc_post_count($mtf->thread_id, 1, $mtf->id, $mtf->post_stamp);
			} else {
				th_inc_post_count($mtf->thread_id, 1);
			}
			rebuild_forum_view($mtf->forum_id, q_singleval('SELECT page FROM fud26_thread_view WHERE forum_id='.$mtf->forum_id.' AND thread_id='.$mtf->thread_id));
			$threads = 0;
		}

		/* update forum thread & post count as well as last_post_id field */
		frm_updt_counts($mtf->forum_id, 1, $threads, $last_post_id);

		if ($mtf->poll_id) {
			poll_activate($mtf->poll_id, $mtf->forum_id);
		}

		$mtf->body = read_msg_body($mtf->foff, $mtf->length, $mtf->file_id);

		if ($GLOBALS['FUD_OPT_1'] & 16777216) {
			index_text((preg_match('!Re: !i', $mtf->subject) ? '': $mtf->subject), $mtf->body, $mtf->id);
		}

		/* handle notifications */
		if ($mtf->root_msg_id == $mtf->id) {
			if (empty($mtf->frm_last_post_date)) {
				$mtf->frm_last_post_date = 0;
			}

			/* send new thread notifications to forum subscribers */
			$c = uq('SELECT u.email
					FROM fud26_forum_notify fn
					INNER JOIN fud26_users u ON fn.user_id=u.id AND (u.users_opt & 134217728) = 0
					INNER JOIN fud26_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id='.$mtf->forum_id.'
					LEFT JOIN fud26_forum_read r ON r.forum_id=fn.forum_id AND r.user_id=fn.user_id
					LEFT JOIN fud26_group_cache g2 ON g2.user_id=fn.user_id AND g2.resource_id='.$mtf->forum_id.'
					LEFT JOIN fud26_mod mm ON mm.forum_id='.$mtf->forum_id.' AND mm.user_id=u.id
				WHERE
					fn.forum_id='.$mtf->forum_id.' AND fn.user_id!='.(int)$mtf->poster_id.'
					'.($GLOBALS['FUD_OPT_3'] & 64 ? 'AND (CASE WHEN (r.last_view IS NULL AND (u.last_read=0 OR u.last_read >= '.$mtf->frm_last_post_date.')) OR r.last_view > '.$mtf->frm_last_post_date.' THEN 1 ELSE 0 END)=1' : '').'
					AND (((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0 OR (u.users_opt & 1048576) > 0 OR mm.id IS NOT NULL)');
			$notify_type = 'frm';
		} else {
			/* send new reply notifications to thread subscribers */
			$c = uq('SELECT u.email, r.msg_id, u.id
					FROM fud26_thread_notify tn
					INNER JOIN fud26_users u ON tn.user_id=u.id AND (u.users_opt & 134217728) = 0
					INNER JOIN fud26_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id='.$mtf->forum_id.'
					LEFT JOIN fud26_read r ON r.thread_id=tn.thread_id AND r.user_id=tn.user_id
					LEFT JOIN fud26_group_cache g2 ON g2.user_id=tn.user_id AND g2.resource_id='.$mtf->forum_id.'
					LEFT JOIN fud26_mod mm ON mm.forum_id='.$mtf->forum_id.' AND mm.user_id=u.id
				WHERE
					tn.thread_id='.$mtf->thread_id.' AND tn.user_id!='.(int)$mtf->poster_id.'
					'.($GLOBALS['FUD_OPT_3'] & 64 ? 'AND (r.msg_id='.$mtf->last_post_id.' OR (r.msg_id IS NULL AND '.$mtf->post_stamp.' > u.last_read))' : '').'
					AND (((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0 OR (u.users_opt & 1048576) > 0 OR mm.id IS NOT NULL)');
			$notify_type = 'thr';
		}
		$tl = $to = array();
		while ($r = db_rowarr($c)) {
			$to[] = $r[0];

			if (isset($r[2]) && !$r[1]) {
				$tl[] = $r[2];
			}
		}
		unset($c);
		if ($tl) {
			/* this allows us to mark the message we are sending notification about as read, so that we do not re-notify the user
			 * until this message is read.
			 */
			db_li('INSERT INTO fud26_read (thread_id, msg_id, last_view, user_id) SELECT '.$mtf->thread_id.', 0, 0, id FROM fud26_users WHERE id IN('.implode(',', $tl).')', $dummy);
		}
		if ($to) {
			send_notifications($to, $mtf->id, $mtf->subject, $mtf->alias, $notify_type, ($notify_type == 'thr' ? $mtf->thread_id : $mtf->forum_id), $mtf->frm_name, $mtf->forum_id);
		}

		// Handle Mailing List and/or Newsgroup syncronization.
		if (($mtf->nntp_id || $mtf->mlist_id) && !$mtf->mlist_msg_id) {
			fud_use('email_msg_format.inc', true);

			$from = $mtf->poster_id ? reverse_fmt($mtf->alias).' <'.$mtf->email.'>' : $GLOBALS['ANON_NICK'].' <'.$GLOBALS['NOTIFY_FROM'].'>';
			$body = $mtf->body . (($mtf->msg_opt & 1 && $mtf->sig) ? "\n--\n" . $mtf->sig : '');
			plain_text($body);
			$mtf->subject = reverse_fmt($mtf->subject);

			if ($mtf->reply_to) {
				$replyto_id = q_singleval('SELECT mlist_msg_id FROM fud26_msg WHERE id='.$mtf->reply_to);
			} else {
				$replyto_id = 0;
			}

			if ($mtf->attach_cnt) {
				$r = uq("SELECT a.id, a.original_name,
						CASE WHEN m.mime_hdr IS NULL THEN 'application/octet-stream' ELSE m.mime_hdr END
						FROM fud26_attach a
						LEFT JOIN fud26_mime m ON a.mime_type=m.id
						WHERE a.message_id=".$mtf->id." AND a.attach_opt=0");
				while ($ent = db_rowarr($r)) {
					$attach[$ent[1]] = file_get_contents($GLOBALS['FILE_STORE'].$ent[0].'.atch');
					if ($mtf->mlist_id) {
						$attach_mime[$ent[1]] = $ent[2];
					}
				}
			} else {
				$attach_mime = $attach = null;
			}

			if ($mtf->nntp_id) {
				fud_use('nntp.inc', true);

				$nntp_adm = db_sab('SELECT * FROM fud26_nntp WHERE id='.$mtf->nntp_id);
				$nntp = new fud_nntp;

				$nntp->server = $nntp_adm->server;
				$nntp->newsgroup = $nntp_adm->newsgroup;
				$nntp->port = $nntp_adm->port;
				$nntp->timeout = $nntp_adm->timeout;
				$nntp->nntp_opt = $nntp_adm->nntp_opt;
				$nntp->login = $nntp_adm->login;
				$nntp->pass = $nntp_adm->pass;

				define('sql_p', 'fud26_');

				$lock = $nntp->get_lock();
				$nntp->post_message($mtf->subject, $body, $from, $mtf->id, $replyto_id, $attach);
				$nntp->close_connection();
				$nntp->release_lock($lock);
			} else {
				fud_use('mlist_post.inc', true);
				
				$r = db_saq('SELECT name, additional_headers FROM fud26_mlist WHERE id='.$mtf->mlist_id);
				mail_list_post($r[0], $from, $mtf->subject, $body, $mtf->id, $replyto_id, $attach, $attach_mime, $r[1]);
			}
		}
	}
}

function write_body($data, &$len, &$offset, $fid=0)
{
	$MAX_FILE_SIZE = 2147483647;

	$len = strlen($data);
	$i = 1;

	if ($fid) {
		db_lock('fud26_fl_'.$fid.' WRITE');
	}

	while ($i < 100) {
		$fp = fopen($GLOBALS['MSG_STORE_DIR'].'msg_'.$i, 'ab');
		fseek($fp, 0, SEEK_END);
		if (!($off = ftell($fp))) {
			$off = __ffilesize($fp);
		}
		if (!$off || ($off + $len) < $MAX_FILE_SIZE) {
			break;
		}
		fclose($fp);
		$i++;
	}

	if (fwrite($fp, $data) !== $len) {
		if ($fid) {
			db_unlock();
		}
		exit("FATAL ERROR: system has ran out of disk space<br>\n");
	}
	fclose($fp);

	if ($fid) {
		db_unlock();
	}

	if (!$off) {
		@chmod('msg_'.$i, ($GLOBALS['FUD_OPT_2'] & 8388608 ? 0600 : 0666));
	}
	$offset = $off;

	return $i;
}

function trim_html($str, $maxlen)
{
	$n = strlen($str);
	$ln = 0;
	$tree = array();
	for ($i = 0; $i < $n; $i++) {
		if ($str[$i] != '<') {
			$ln++;
			if ($ln > $maxlen) {
				break;
			}
			continue;
		}

		if (($p = strpos($str, '>', $i)) === false) {
			break;
		}

		for ($k = $i; $k < $p; $k++) {
			switch ($str[$k]) {
				case ' ':
				case "\r":
				case "\n":
				case "\t":
				case ">":
					break 2;
			}
		}

		if ($str[$i+1] == '/') {
			$tagname = strtolower(substr($str, $i+2, $k-$i-2));
			if (@end($tagindex[$tagname])) {
				$k = key($tagindex[$tagname]);
				unset($tagindex[$tagname][$k], $tree[$k]);
			}
		} else {
			$tagname = strtolower(substr($str, $i+1, $k-$i-1));
			switch ($tagname) {
				case 'br':
				case 'img':
				case 'meta':
					break;
				default:
					$tree[] = $tagname;
					end($tree);
					$tagindex[$tagname][key($tree)] = 1;
			}
		}
		$i = $p;
	}

	$data = substr($str, 0, $i);
	if ($tree) {
		foreach (array_reverse($tree) as $v) {
			$data .= '</'.$v.'>';
		}
	}

	return $data;
}

function make_email_message(&$body, &$obj, $iemail_unsub)
{
	$TITLE_EXTRA = $iemail_poll = $iemail_attach = '';
	if ($obj->poll_cache) {
		$pl = unserialize($obj->poll_cache);
		if (!empty($pl)) {
			foreach ($pl as $k => $v) {
				$length = ($v[1] && $obj->total_votes) ? round($v[1] / $obj->total_votes * 100) : 0;
				$iemail_poll .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td>'.$v[0].'</td><td><img src="theme/default/images/poll_pix.gif" alt="" height="10" width="'.$length.'" /> '.$v[1].' / '.$length.'%</td></tr>';
			}
			$iemail_poll = '<table cellspacing=1 cellpadding=2 class="PollTable">
<tr><th nowrap colspan=3>'.$obj->poll_name.'<img src="blank.gif" alt="" height=1 width=10 /><span class="small">[ '.$obj->total_votes.' vote(s) ]</span></th></tr>
'.$iemail_poll.'
</table><p>';
		}
	}
	if ($obj->attach_cnt && $obj->attach_cache) {
		$atch = unserialize($obj->attach_cache);
		if (!empty($atch)) {
			foreach ($atch as $v) {
				$sz = $v[2] / 1024;
				$sz = $sz < 1000 ? number_format($sz, 2).'KB' : number_format($sz/1024, 2).'MB';
				$iemail_attach .= '<tr>
<td class="vm"><a href="index.php?t=getfile&amp;id='.$v[0].'"><img alt="" src="'.$GLOBALS['WWW_ROOT'].'images/mime/'.$v[4].'" /></a></td>
<td><span class="GenText fb">Attachment:</span> <a href="index.php?t=getfile&amp;id='.$v[0].'">'.$v[1].'</a><br />
<span class="SmallText">(Size: '.$sz.', Downloaded '.$v[3].' time(s))</span></td></tr>';
			}
			$iemail_attach = '<p>
<table border=0 cellspacing=0 cellpadding=2>
'.$iemail_attach.'
</table>';
		}
	}

	if ($GLOBALS['FUD_OPT_2'] & 32768) {
		$pfx = str_repeat('/', substr_count(_rsid, '/'));
	}

	return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<title>'.$GLOBALS['FORUM_TITLE'].$TITLE_EXTRA.'</title>
<BASE HREF="http://timeweather.net/forum/">
<script language="JavaScript" src="lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="theme/default/forum.css" type="text/css">
</head>
<body>
<table class="wa" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr class="RowStyleB">
	<td width="33%"><b>Subject:</b> '.$obj->subject.'</td>
	<td width="33%"><b>Author:</b> '.$obj->alias.'</td>
	<td width="33%"><b>Date:</b> '.strftime("%a, %d %B %Y %H:%M", $obj->post_stamp).'</td>
</tr>
<tr class="RowStyleA">
	<td colspan="3">
	'.$iemail_poll.'
	'.$body.'
	'.$iemail_attach.'
	</td>
</tr>
<tr class="RowStyleB">
	<td colspan="3">
	[ <a href="index.php?t=post&reply_to='.$obj->id.'">Reply</a> ][ <a href="index.php?t=post&reply_to='.$obj->id.'&quote=true">Quote</a> ][ <a href="index.php?t=rview&goto='.$obj->id.'#msg_'.$obj->id.'">View Topic/Message</a> ]'.$iemail_unsub.'
	</td>
</tr>
</table>
</td></tr></table></body></html>';
}

function send_notifications($to, $msg_id, $thr_subject, $poster_login, $id_type, $id, $frm_name, $frm_id)
{
	if (!$to) {
		return;
	}

	$goto_url['email'] = 'http://timeweather.net/forum/index.php?t=rview&goto='.$msg_id.'#msg_'.$msg_id;
	$CHARSET = $GLOBALS['CHARSET'];
	if ($GLOBALS['FUD_OPT_2'] & 64) {
		$obj = db_sab("SELECT p.total_votes, p.name AS poll_name, m.reply_to, m.subject, m.id, m.post_stamp, m.poster_id, m.foff, m.length, m.file_id, u.alias, m.attach_cnt, m.attach_cache, m.poll_cache FROM fud26_msg m LEFT JOIN fud26_users u ON m.poster_id=u.id LEFT JOIN fud26_poll p ON m.poll_id=p.id WHERE m.id=".$msg_id." AND m.apr=1");

		if (!$obj->alias) { /* anon user */
			$obj->alias = htmlspecialchars($GLOBALS['ANON_NICK']);
		}

		$headers  = "MIME-Version: 1.0\r\n";
		if ($obj->reply_to) {
			$headers .= "In-Reply-To: ".$obj->reply_to."\r\n";
		}
		$headers .= "List-Id: ".$frm_id.".".$_SERVER['SERVER_NAME']."\r\n";
		$split = get_random_value(128)                                                                            ;
		$headers .= "Content-Type: multipart/alternative;\n  boundary=\"------------" . $split . "\"\r\n";
		$boundry = "\r\n--------------" . $split . "\r\n";

		$pfx = '';
		if ($GLOBALS['FUD_OPT_2'] & 32768 && !empty($_SERVER['PATH_INFO'])) {
			if ($GLOBALS['FUD_OPT_1'] & 128) {
				$pfx .= '0/';
			}
			if ($GLOBALS['FUD_OPT_2'] & 8192) {
				$pfx .= '0/';
			}
		}

		$plain_text = read_msg_body($obj->foff, $obj->length, $obj->file_id);
		$iemail_unsub = html_entity_decode($id_type == 'thr' ? '[ <a href="index.php?t=rview&th='.$id.'">Unsubscribe from this topic</a> ]' : '[ <a href="index.php?t=rview&frm_id='.$id.'">Unsubscribe from this forum</a> ]');

		$body_email = 	$boundry . "Content-Type: text/plain; charset=" . $CHARSET . "; format=flowed\r\nContent-Transfer-Encoding: 7bit\r\n\r\n" . html_entity_decode(strip_tags($plain_text)) . "\r\n\r\n" . html_entity_decode('To participate in the discussion, go here:') . ' ' . 'http://timeweather.net/forum/index.php?t=rview&th=' . $id . "\r\n" .
				$boundry . "Content-Type: text/html; charset=" . $CHARSET . "\r\nContent-Transfer-Encoding: 7bit\r\n\r\n" . make_email_message($plain_text, $obj, $iemail_unsub) . "\r\n" . substr($boundry, 0, -2) . "--\r\n";
	} else {
		$headers = "Content-Type: text/plain; charset={$CHARSET}\r\n";
	}

	$thr_subject = reverse_fmt($thr_subject);
	$poster_login = reverse_fmt($poster_login);

	if ($id_type == 'thr') {
		$subj = html_entity_decode('New reply to '.$thr_subject.' by '.$poster_login);

		if (!isset($body_email)) {
			$unsub_url['email'] = 'http://timeweather.net/forum/index.php?t=rview&th='.$id.'&notify=1&opt=off';
			$body_email = html_entity_decode('To view unread replies go to '.$goto_url['email'].'\n\nIf you do not wish to receive further notifications about replies in this topic, please go here: '.$unsub_url['email']);
		}
	} else if ($id_type == 'frm') {
		$frm_name = reverse_fmt($frm_name);

		$subj = html_entity_decode('New topic in forum '.$frm_name.', called '.$thr_subject.', by '.$poster_login);

		if (!isset($body_email)) {
			$unsub_url['email'] = 'http://timeweather.net/forum/index.php?t=rview&unsub=1&frm_id='.$id;
			$body_email = html_entity_decode('To view the topic go to:\n'.$goto_url['email'].'\n\nTo stop receiving notifications about new topics in this forum, please go here: '.$unsub_url['email']);
		}
	}

	send_email($GLOBALS['NOTIFY_FROM'], $to, $subj, $body_email, $headers);
}function check_return($returnto)
{
	if ($GLOBALS['FUD_OPT_2'] & 32768 && !empty($_SERVER['PATH_INFO'])) {
		if (!$returnto || !strncmp($returnto, '/er/', 4)) {
			header('Location: http://timeweather.net/forum/index.php/i/'._rsidl);
		} else if ($returnto[0] == '/') { /* unusual situation, path_info & normal themes are active */
			header('Location: http://timeweather.net/forum/index.php'.$returnto);
		} else {
			header('Location: http://timeweather.net/forum/index.php?'.$returnto);
		}
	} else if (!$returnto || !strncmp($returnto, 't=error', 7)) {
		header('Location: http://timeweather.net/forum/index.php?t=index&'._rsidl);
	} else if (strpos($returnto, 'S=') === false && $GLOBALS['FUD_OPT_1'] & 128) {
		header('Location: http://timeweather.net/forum/index.php?'.$returnto.'&S='.s);
	} else {
		header('Location: http://timeweather.net/forum/index.php?'.$returnto);
	}
	exit;
}function logaction($user_id, $res, $res_id=0, $action=null)
{
	q('INSERT INTO fud26_action_log (logtime, logaction, user_id, a_res, a_res_id)
		VALUES('.__request_timestamp__.', '.strnull($action).', '.$user_id.', '.strnull($res).', '.(int)$res_id.')');
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
}function poll_delete($id)
{
	if (!$id) {
		return;
	}

	q('UPDATE fud26_msg SET poll_id=0 WHERE poll_id='.$id);
	q('DELETE FROM fud26_poll_opt WHERE poll_id='.$id);
	q('DELETE FROM fud26_poll_opt_track WHERE poll_id='.$id);
	q('DELETE FROM fud26_poll WHERE id='.$id);
}

function poll_fetch_opts($id)
{
	$a = array();
	$c = uq('SELECT id,name FROM fud26_poll_opt WHERE poll_id='.$id);
	while ($r = db_rowarr($c)) {
		$a[$r[0]] = $r[1];
	}

	return $a;
}

function poll_del_opt($id, $poll_id)
{
	q('DELETE FROM fud26_poll_opt WHERE poll_id='.$poll_id.' AND id='.$id);
	q('DELETE FROM fud26_poll_opt_track WHERE poll_id='.$poll_id.' AND poll_opt='.$id);
	if ($GLOBALS['FUD_OPT_3'] & 1024 || __dbtype__ != 'mysql') {
		q('UPDATE fud26_poll SET total_votes=(SELECT SUM(count) FROM fud26_poll_opt WHERE id='.$id.') WHERE id='.$poll_id);
	} else {
		q('UPDATE fud26_poll SET total_votes='.(int) q_singleval('SELECT SUM(count) FROM fud26_poll_opt WHERE id='.$id).' WHERE id='.$poll_id);
	}
}

function poll_activate($poll_id, $frm_id)
{
	q('UPDATE fud26_poll SET forum_id='.$frm_id.' WHERE id='.$poll_id);
}

function poll_sync($poll_id, $name, $max_votes, $expiry)
{
	q("UPDATE fud26_poll SET name='".addslashes(htmlspecialchars($name))."', expiry_date=".intzero($expiry).", max_votes=".intzero($max_votes)." WHERE id=".$poll_id);
}

function poll_add($name, $max_votes, $expiry, $uid=_uid)
{
	return db_qid("INSERT INTO fud26_poll (name, owner, creation_date, expiry_date, max_votes) VALUES ('".addslashes(htmlspecialchars($name))."', ".$uid.", ".__request_timestamp__.", ".intzero($expiry).", ".intzero($max_votes).")");
}

function poll_opt_sync($id, $name)
{
	q("UPDATE fud26_poll_opt SET name='".addslashes($name)."' WHERE id=".$id);
}

function poll_opt_add($name, $poll_id)
{
	return db_qid("INSERT INTO fud26_poll_opt (poll_id,name) VALUES(".$poll_id.", '".addslashes($name)."')");
}

function poll_validate($poll_id, $msg_id)
{
	if (($mid = (int) q_singleval('SELECT id FROM fud26_msg WHERE poll_id='.$poll_id)) && $mid != $msg_id) {
		return 0;
	}
	return $poll_id;
}function safe_attachment_copy($source, $id, $ext)
{
	$loc = $GLOBALS['FILE_STORE'] . $id . '.atch';
	if (!$ext && !move_uploaded_file($source, $loc)) {
		error_dialog('unable to move uploaded file', 'From: '.$source.' To: '.$loc, 'ATCH');
	} else if ($ext && !copy($source, $loc)) {
		error_dialog('unable to handle file attachment', 'From: '.$source.' To: '.$loc, 'ATCH');
	}
	@unlink($source);

	@chmod($loc, ($GLOBALS['FUD_OPT_2'] & 8388608 ? 0600 : 0666));

	return $loc;
}

function attach_add($at, $owner, $attach_opt=0, $ext=0)
{
	$id = db_qid("INSERT INTO fud26_attach (location,message_id,original_name,owner,attach_opt,mime_type,fsize) SELECT '', 0, '".addslashes($at['name'])."', ".$owner.", ".$attach_opt.", id, ".$at['size']." FROM fud26_mime WHERE fl_ext IN('', '".addslashes(substr(strrchr($at['name'], '.'), 1))."') ORDER BY fl_ext DESC LIMIT 1");

	safe_attachment_copy($at['tmp_name'], $id, $ext);

	return $id;
}

function attach_finalize($attach_list, $mid, $attach_opt=0)
{
	$id_list = '';
	$attach_count = 0;

	$tbl = !$attach_opt ? 'msg' : 'pmsg';

	foreach ($attach_list as $key => $val) {
		if (empty($val)) {
			@unlink($GLOBALS['FILE_STORE'].(int)$key.'.atch');
		} else {
			$attach_count++;
			$id_list .= (int)$key.',';
		}
	}

	if ($id_list) {
		$id_list = substr($id_list, 0, -1);
		$cc = __FUD_SQL_CONCAT__.'('.__FUD_SQL_CONCAT__."('".$GLOBALS['FILE_STORE']."', id), '.atch')";
		q("UPDATE fud26_attach SET location=".$cc.", message_id=".$mid." WHERE id IN(".$id_list.") AND attach_opt=".$attach_opt);
		$id_list = ' AND id NOT IN('.$id_list.')';
	} else {
		$id_list = '';
	}

	/* delete any unneeded (removed, temporary) attachments */
	q("DELETE FROM fud26_attach WHERE message_id=".$mid." ".$id_list);

	if (!$attach_opt && ($atl = attach_rebuild_cache($mid))) {
		q('UPDATE fud26_msg SET attach_cnt='.$attach_count.', attach_cache=\''.addslashes(serialize($atl)).'\' WHERE id='.$mid);
	}

	if (!empty($GLOBALS['usr']->sid)) {
		ses_putvar((int)$GLOBALS['usr']->sid, null);
	}
}

function attach_rebuild_cache($id)
{
	$ret = array();
	$c = uq('SELECT a.id, a.original_name, a.fsize, a.dlcount, CASE WHEN m.icon IS NULL THEN \'unknown.gif\' ELSE m.icon END FROM fud26_attach a LEFT JOIN fud26_mime m ON a.mime_type=m.id WHERE message_id='.$id.' AND attach_opt=0');
	while ($r = db_rowarr($c)) {
		$ret[] = $r;
	}
	return $ret;
}

function attach_inc_dl_count($id, $mid)
{
	q('UPDATE fud26_attach SET dlcount=dlcount+1 WHERE id='.$id);
	if (($a = attach_rebuild_cache($mid))) {
		q('UPDATE fud26_msg SET attach_cache=\''.addslashes(serialize($a)).'\' WHERE id='.$mid);
	}
}function validate_email($email)
{
        return !preg_match('!^([-_A-Za-z0-9\.]+)\@([-_A-Za-z0-9\.]+)\.([A-Za-z0-9]{2,4})$!', $email);
}

function encode_subject($text)
{
	if (preg_match('![\x7f-\xff]!', $text)) {
		$text = '=?' . 'ISO-8859-15' . '?B?' . base64_encode($text) . '?=';
	}

	return $text;
}

function send_email($from, $to, $subj, $body, $header='')
{
	if (empty($to)) {
		return;
	}

	if ($GLOBALS['FUD_OPT_1'] & 512) {
		if (!class_exists('fud_smtp')) {
			fud_use('smtp.inc');
		}
		$smtp = new fud_smtp;
		$smtp->msg = str_replace(array('\n', "\n."), array("\n", "\n.."), $body);
		$smtp->subject = encode_subject($subj);
		$smtp->to = $to;
		$smtp->from = $from;
		$smtp->headers = $header;
		$smtp->send_smtp_email();
		return;
	}

	if ($header) {
		$header = "\n" . str_replace("\r", "", $header);
	}
	$header = "From: ".$from."\nErrors-To: ".$from."\nReturn-Path: ".$from."\nX-Mailer: FUDforum v".$GLOBALS['FORUM_VERSION'].$header;

	$body = str_replace(array('\n',"\r"), array("\n",""), $body);
	$subj = encode_subject($subj);
	if (version_compare("4.3.3RC2", phpversion(), ">")) {
		$body = str_replace("\n.", "\n..", $body);
	}

	/* special handling for multibyte languages */
	if (!empty($GLOBALS['usr']->lang) && ($GLOBALS['usr']->lang == 'chinese' || $GLOBALS['usr']->lang == 'japanese') && extension_loaded('mbstring')) {
		if ($GLOBALS['usr']->lang == 'japanese') {
			mb_language('ja');
		} else {
			mb_language('uni');
		}
		mb_internal_encoding('UTF-8');
		$mail_func = 'mb_send_mail';
	} else {
		$mail_func = 'mail';
	}

	foreach ((array)$to as $email) {
		$mail_func($email, $subj, $body, $header);
	}
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
}function get_host($ip)
{
	if (!$ip || $ip == '0.0.0.0') {
		return;
	}

	$name = gethostbyaddr($ip);

	if ($name == $ip) {
		$name = substr($name, 0, strrpos($name, '.')) . '*';
	} else if (substr_count($name, '.') > 2) {
		$name = '*' . substr($name, strpos($name, '.')+1);
	}

	return $name;
}function delete_msg_index($msg_id)
{
	q('DELETE FROM fud26_index WHERE msg_id='.$msg_id);
	q('DELETE FROM fud26_title_index WHERE msg_id='.$msg_id);
}

function mb_word_split($str)
{
	$m = array();
	$lang = $GLOBALS['usr']->lang == 'chinese' ? 'EUC-CN' : 'BIG-5';

	if (extension_loaded('iconv')) {
		preg_match_all('!(\w)!u', @iconv($lang, 'UTF-8', $str), $m);
	} else if (extension_loaded('mbstring')) {
		preg_match_all('!(\w)!u', @mb_convert_encoding($str, 'UTF-8', $lang), $m);
	} else { /* poor man's alternative to proper multi-byte support */
		preg_match_all("!([\\1-\\255]{1,2})!", $str, $m);
	}

	if (!$m) {
		return array();
	}

	$m2 = array();
	foreach (array_unique($m[0]) as $v) {
		if (isset($v[1])) {
			$m2[] = "'".addslashes($v)."'";
		}
	}

	return $m2;
}

function text_to_worda($text)
{
	$a = array();

	/* if no good locale, default to splitting by spaces */
	if (!$GLOBALS['good_locale']) {
		$GLOBALS['usr']->lang = 'latvian';
	}

	$text = reverse_fmt($text);
	while (1) {
		switch ($GLOBALS['usr']->lang) {
			case 'chinese_big5':
			case 'chinese':
				return array_unique(mb_word_split($text));
		
			case 'japanese':
				preg_match_all('!(\w)!u', $text, $tmp);
				break;

			case 'latvian':
			case 'russian-1251':
				$t1 = array_unique(preg_split('![\x00-\x40]+!', $text, -1, PREG_SPLIT_NO_EMPTY));
				break;

			default:
				$t1 = array_unique(str_word_count(strip_tags(strtolower($text)), 1));
				if (!$t1) { /* fall through to split by special chars */
					$GLOBALS['usr']->lang = 'latvian';
					continue;		
				} 
				break;
		}

		/* this is mostly a hack for php verison < 4.3 because isset(string[bad offset]) returns a warning */
		error_reporting(0);
	
		foreach ($t1 as $v) {
			if (isset($v[51]) || !isset($v[2])) continue;
			$a[] = "'".addslashes($v)."'";
		}

		error_reporting(2047); /* restore error reporting */

		break;
	}

	return $a;
}

function index_text($subj, $body, $msg_id)
{
	/* Remove Stuff In Quotes */
	while (preg_match('!<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>(.*?)</b></td></tr><tr><td class="quote"><br />(.*?)<br /></td></tr></table>!is', $body)) {
		$body = preg_replace('!<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>(.*?)</b></td></tr><tr><td class="quote"><br />(.*?)<br /></td></tr></table>!is', '', $body);
	}

	$w1 = text_to_worda($subj);
	$w2 = $w1 ? array_merge($w1, text_to_worda($body)) : text_to_worda($body);

	if (!$w2) {
		return;
	}

	$w2 = array_unique($w2);
	if (__dbtype__ == 'mysql') {
		ins_m('fud26_search', 'word', $w2);
	} else {
		if (!defined('search_prep')) {
			define('search_prep', 'PREPARE fud26_srch_ins (text) AS INSERT INTO fud26_search (word) VALUES($1)');
			define('search_prep2', 'PREPARE fud26_srch_sel (text) AS SELECT id FROM fud26_search WHERE word= $1');
			pg_query(fud_sql_lnk, search_prep);
			pg_query(fud_sql_lnk, search_prep2);
		}
		foreach ($w2 as $w) {			
			if (pg_num_rows(pg_query(fud_sql_lnk, "EXECUTE fud26_srch_sel (".$w.")")) < 1) {
				pg_query(fud_sql_lnk, "EXECUTE fud26_srch_ins (".$w.")");
			}
		}
		/* if persistent connections are used de-allocte the prepared statement to prevent query failures */
		if ($GLOBALS['FUD_OPT_1'] & 256) {
			pg_query(fud_sql_lnk, 'DEALLOCATE fud26_srch_sel');
			pg_query(fud_sql_lnk, 'DEALLOCATE fud26_srch_ins');
		}
	}

	if ($w1) {
		db_li('INSERT INTO fud26_title_index (word_id, msg_id) SELECT id, '.$msg_id.' FROM fud26_search WHERE word IN('.implode(',', $w1).')', $ef);
	}
	db_li('INSERT INTO fud26_index (word_id, msg_id) SELECT id, '.$msg_id.' FROM fud26_search WHERE word IN('.implode(',', $w2).')', $ef);
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
}class fud_smtp
{
	var $fs, $last_ret, $msg, $subject, $to, $from, $headers;

	function get_return_code($cmp_code='250')
	{
		if (!($this->last_ret = fgets($this->fs, 1024))) {
			return;
		}
		if (substr($this->last_ret, 0, 3) == $cmp_code) {
			return 1;
		}

		return;
	}

	function wts($string)
	{
		fwrite($this->fs, $string . "\r\n");
	}

	function open_smtp_connex()
	{
		if( !($this->fs = fsockopen($GLOBALS['FUD_SMTP_SERVER'], 25, $errno, $errstr, $GLOBALS['FUD_SMTP_TIMEOUT'])) ) {
			exit("ERROR: stmp server at ".$GLOBALS['FUD_SMTP_SERVER']." is not available<br>\nAdditional Problem Info: $errno -> $errstr <br>\n");
		}
		if (!$this->get_return_code(220)) {
			return;
		}
		$this->wts("HELO ".$GLOBALS['FUD_SMTP_SERVER']);
		if (!$this->get_return_code()) {
			return;
		}

		/* Do SMTP Auth if needed */
		if ($GLOBALS['FUD_SMTP_LOGIN']) {
			$this->wts('AUTH LOGIN');
			if (!$this->get_return_code(334)) {
				return;
			}
			$this->wts(base64_encode($GLOBALS['FUD_SMTP_LOGIN']));
			if (!$this->get_return_code(334)) {
				return;
			}
			$this->wts(base64_encode($GLOBALS['FUD_SMTP_PASS']));
			if (!$this->get_return_code(235)) {
				return;
			}
		}

		return 1;
	}

	function send_from_hdr()
	{
		$this->wts('MAIL FROM: <'.$GLOBALS['NOTIFY_FROM'].'>');
		return $this->get_return_code();
	}

	function send_to_hdr()
	{
		$this->to = (array) $this->to;

		foreach ($this->to as $to_addr) {
			$this->wts('RCPT TO: <'.$to_addr.'>');
			if (!$this->get_return_code()) {
				return;
			}
		}
		return 1;
	}

	function send_data()
	{
		$this->wts('DATA');
		if (!$this->get_return_code(354)) {
			return;
		}

		/* This is done to ensure what we comply with RFC requiring each line to end with \r\n */
		$this->msg = preg_replace("!(\r)?\n!si", "\r\n", $this->msg);

		if( empty($this->from) ) $this->from = $GLOBALS['NOTIFY_FROM'];

		$this->wts('Subject: '.$this->subject);
		$this->wts('Date: '.date("r"));
		$this->wts('To: '.(count($this->to) == 1 ? $this->to[0] : $GLOBALS['NOTIFY_FROM']));
		$this->wts('From: '.$this->from);
		$this->wts('X-Mailer: FUDforum v'.$GLOBALS['FORUM_VERSION']);
		$this->wts($this->headers."\r\n");
		$this->wts($this->msg);
		$this->wts('.');

		return $this->get_return_code();
	}

	function close_connex()
	{
		$this->wts('quit');
		fclose($this->fs);
	}

	function send_smtp_email()
	{
		if (!$this->open_smtp_connex()) {
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_from_hdr()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_to_hdr()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}
		if (!$this->send_data()) {
			$this->close_connex();
			exit("Invalid STMP return code: ".$this->last_ret."<br>\n");
		}

		$this->close_connex();
	}
}$GLOBALS['seps'] = array(' '=>' ', "\n"=>"\n", "\r"=>"\r", "'"=>"'", '"'=>'"', '['=>'[', ']'=>']', '('=>'(', ';'=>';', ')'=>')', "\t"=>"\t", '='=>'=', '>'=>'>', '<'=>'<');

function fud_substr_replace($str, $newstr, $pos, $len)
{
        return substr($str, 0, $pos).$newstr.substr($str, $pos+$len);
}

function tags_to_html($str, $allow_img=1, $no_char=0)
{
	if (!$no_char) {
		$str = htmlspecialchars($str);
	}

	$str = nl2br($str);

	$ostr = '';
	$pos = $old_pos = 0;

	while (($pos = strpos($str, '[', $pos)) !== false) {
		if (isset($GLOBALS['seps'][$str[$pos + 1]])) {
			++$pos;
			continue;
		}

		if (($epos = strpos($str, ']', $pos)) === false) {
			break;
		}
		if (!($epos-$pos-1)) {
			$pos = $epos + 1;
			continue;
		}
		$tag = substr($str, $pos+1, $epos-$pos-1);
		if (($pparms = strpos($tag, '=')) !== false) {
			$parms = substr($tag, $pparms+1);
			if (!$pparms) { /*[= exception */
				$pos = $epos+1;
				continue;
			}
			$tag = substr($tag, 0, $pparms);
		} else {
			$parms = '';
		}

		$tag = strtolower($tag);

		switch ($tag) {
			case 'quote title':
				$tag = 'quote';
				break;
			case 'list type':
				$tag = 'list';
				break;
		}

		if ($tag[0] == '/') {
			if (isset($end_tag[$pos])) {
				if( ($pos-$old_pos) ) $ostr .= substr($str, $old_pos, $pos-$old_pos);
				$ostr .= $end_tag[$pos];
				$pos = $old_pos = $epos+1;
			} else {
				$pos = $epos+1;
			}

			continue;
		}

		$cpos = $epos;
		$ctag = '[/'.$tag.']';
		$ctag_l = strlen($ctag);
		$otag = '['.$tag;
		$otag_l = strlen($otag);
		$rf = 1;
		$nt_tag = 0;
		while (($cpos = strpos($str, '[', $cpos)) !== false) {
			if (isset($end_tag[$cpos]) || isset($GLOBALS['seps'][$str[$cpos + 1]])) {
				++$cpos;
				continue;
			}

			if (($cepos = strpos($str, ']', $cpos)) === false) {
				if (!$nt_tag) {
					break 2;
				} else {
					break;
				}
			}

			if (strcasecmp(substr($str, $cpos, $ctag_l), $ctag) == 0) {
				--$rf;
			} else if (strcasecmp(substr($str, $cpos, $otag_l), $otag) == 0) {
				++$rf;
			} else {
				$nt_tag++;
				++$cpos;
				continue;
			}

			if (!$rf) {
				break;
			}
			$cpos = $cepos;
		}

		if (!$cpos || ($rf && $str[$cpos] == '<')) { /* left over [ handler */
			++$pos;
			continue;
		}

		if ($cpos !== false) {
			if (($pos-$old_pos)) {
				$ostr .= substr($str, $old_pos, $pos-$old_pos);
			}
			switch ($tag) {
				case 'notag':
					$ostr .= '<span name="notag">'.substr($str, $epos+1, $cpos-1-$epos).'</span>';
					$epos = $cepos;
					break;
				case 'url':
					if (!$parms) {
						$url = substr($str, $epos+1, ($cpos-$epos)-1);
					} else {
						$url = $parms;
					}

					if (!strncasecmp($url, 'www.', 4)) {
						$url = 'http&#58;&#47;&#47;'. $url;
					} else if (strpos(strtolower($url), 'javascript:') !== false) {
						$ostr .= substr($str, $pos, $cepos - $pos + 1);
						$epos = $cepos;
						$str[$cpos] = '<';
						break;
					} else {
						$url = str_replace('://', '&#58;&#47;&#47;', $url);
					}

					$end_tag[$cpos] = '</a>';
					$ostr .= '<a href="'.$url.'" target="_blank">';
					break;
				case 'i':
				case 'u':
				case 'b':
				case 's':
				case 'sub':
				case 'sup':
				case 'del':
					$end_tag[$cpos] = '</'.$tag.'>';
					$ostr .= '<'.$tag.'>';
					break;
				case 'email':
					if (!$parms) {
						$parms = str_replace('@', '&#64;', substr($str, $epos+1, ($cpos-$epos)-1));
						$ostr .= '<a href="mailto:'.$parms.'" target="_blank">'.$parms.'</a>';
						$epos = $cepos;
						$str[$cpos] = '<';
					} else {
						$end_tag[$cpos] = '</a>';
						$ostr .= '<a href="mailto:'.str_replace('@', '&#64;', $parms).'" target="_blank">';
					}
					break;
				case 'color':
				case 'size':
				case 'font':
					if ($tag == 'font') {
						$tag = 'face';
					}
					$end_tag[$cpos] = '</font>';
					$ostr .= '<font '.$tag.'="'.$parms.'">';
					break;
				case 'code':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);

					$ostr .= '<div class="pre"><pre>'.reverse_nl2br($param).'</pre></div>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'pre':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);

					$ostr .= '<pre>'.reverse_nl2br($param).'</pre>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'php':
					$param = trim(reverse_fmt(reverse_nl2br(substr($str, $epos+1, ($cpos-$epos)-1))));

					if (strncmp($param, '<?php', 5)) {
						if (strncmp($param, '<?', 2)) {
							$param = "<?php\n" . $param;
						} else {
							$param = "<?php\n" . substr($param, 3);
						}
					}
					if (substr($param, -2) != '?>') {
						$param .= "\n?>";
					}

					$ostr .= '<span name="php">'.trim(@highlight_string($param, true)).'</span>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'img':
				case 'imgl':
				case 'imgr':
					if (!$allow_img) {
						$ostr .= substr($str, $pos, ($cepos-$pos)+1);
					} else {
						$class = ($tag == 'img') ? '' : 'class="'.$tag{3}.'" ';

						if (!$parms) {
							$parms = substr($str, $epos+1, ($cpos-$epos)-1);
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img '.$class.'src="'.$parms.'" border=0 alt="'.$parms.'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						} else {
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img '.$class.'src="'.$parms.'" border=0 alt="'.substr($str, $epos+1, ($cpos-$epos)-1).'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						}
					}
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'quote':
					if (!$parms) {
						$parms = 'Quote:';
					}
					$ostr .= '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>'.$parms.'</b></td></tr><tr><td class="quote"><br />';
					$end_tag[$cpos] = '<br /></td></tr></table>';
					break;
				case 'align':
					$end_tag[$cpos] = '</div>';
					$ostr .= '<div align="'.$parms.'">';
					break;
				case 'list':
					$tmp = substr($str, $epos, ($cpos-$epos));
					$tmp_l = strlen($tmp);
					$tmp2 = str_replace(array('[*]', '<br />'), array('<li>', ''), $tmp);
					$tmp2_l = strlen($tmp2);
					$str = str_replace($tmp, $tmp2, $str);

					$diff = $tmp2_l - $tmp_l;
					$cpos += $diff;

					if (isset($end_tag)) {
						foreach($end_tag as $key => $val) {
							if ($key < $epos) {
								continue;
							}

							$end_tag[$key+$diff] = $val;
						}
					}

					switch (strtolower($parms)) {
						case '1':
						case 'a':
							$end_tag[$cpos] = '</ol>';
							$ostr .= '<ol type="'.$parms.'">';
							break;
						case 'square':
						case 'circle':
						case 'disc':
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul type="'.$parms.'">';
							break;
						default:
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul>';
					}
					break;
				case 'spoiler':
					$rnd = rand();
					$end_tag[$cpos] = '</div></div>';
					$ostr .= '<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis(\''.$rnd.'\', 1);">'
						.($parms ? $parms : 'Toggle Spoiler').'</a><div align="left" id="'.$rnd.'" style="display: none;">';
					break;
				case 'acronym':
					$end_tag[$cpos] = '</acronym>';
					$ostr .= '<acronym title="'.($parms ? $parms : ' ').'">';
					break;
			}

			$str[$pos] = '<';
			$pos = $old_pos = $epos+1;
		} else {
			$pos = $epos+1;
		}
	}
	$ostr .= substr($str, $old_pos, strlen($str)-$old_pos);

	/* url paser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '://', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}
		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i > $ppos) {
			if ($ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if (!$pos || $ostr[$i] == '<') {
			$pos += 3;
			continue;
		}

		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the span tag
		if (($ts = strpos($ostr, '<span>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</span>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		$us = $pos;
		$l = strlen($ostr);
		while (1) {
			--$us;
			if ($ppos > $us || $us >= $l || isset($GLOBALS['seps'][$ostr[$us]])) {
				break;
			}
		}

		unset($GLOBALS['seps']['=']);
		$ue = $pos;
		while (1) {
			++$ue;
			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}

			if ($ostr[$ue] == '&') {
				if ($ostr[$ue+4] == ';') {
					$ue += 4;
					continue;
				}
				if ($ostr[$ue+3] == ';' || $ostr[$ue+5] == ';') {
					break;
				}
			}

			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}
		}
		$GLOBALS['seps']['='] = '=';

		$url = substr($ostr, $us+1, $ue-$us-1);
		if (!strncasecmp($url, 'javascript', strlen('javascript')) || ($ue - $us - 1) < 9) {
			$pos = $ue;
			continue;
		}
		$html_url = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
		$html_url_l = strlen($html_url);
		$ostr = fud_substr_replace($ostr, $html_url, $us+1, $ue-$us-1);
		$ppos = $pos;
		$pos = $us+$html_url_l;
	}

	/* email parser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '@', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}

		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i>$ppos) {
			if ( $ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if ($i < 0 || $ostr[$i]=='<') {
			++$pos;
			continue;
		}


		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<div class="pre"><pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre></div>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		for ($es = ($pos - 1); $es > ($ppos - 1); $es--) {
			if (
				( ord($ostr[$es]) >= ord('A') && ord($ostr[$es]) <= ord('z') ) ||
				( ord($ostr[$es]) >= ord(0) && ord($ostr[$es]) <= ord(9) ) ||
				( $ostr[$es] == '.' || $ostr[$es] == '-' || $ostr[$es] == '\'')
			) { continue; }
			++$es;
			break;
		}
		if ($es == $pos) {
			$ppos = $pos += 1;
			continue;
		}
		if ($es < 0) {
			$es = 0;
		}

		for ($ee = ($pos + 1); @isset($ostr[$ee]); $ee++) {
			if (
				( ord($ostr[$ee]) >= ord('A') && ord($ostr[$ee]) <= ord('z') ) ||
				( ord($ostr[$ee]) >= ord(0) && ord($ostr[$ee]) <= ord(9) ) ||
				( $ostr[$ee] == '.' || $ostr[$ee] == '-' )
			) { continue; }
			break;
		}
		if ($ee == ($pos+1)) {
			$ppos = $pos += 1;
			continue;
		}

		$email = str_replace('@', '&#64;', substr($ostr, $es, $ee-$es));
		$email_url = '<a href="mailto:'.$email.'" target="_blank">'.$email.'</a>';
		$email_url_l = strlen($email_url);
		$ostr = fud_substr_replace($ostr, $email_url, $es, $ee-$es);
		$ppos =	$es+$email_url_l;
		$pos = $ppos;
	}

	return $ostr;
}

function html_to_tags($fudml)
{
	while (preg_match('!<span name="php">(.*?)</span>!is', $fudml, $res)) {
		$tmp = trim(html_entity_decode(strip_tags(str_replace('<br />', "\n", $res[1]))));
		$m = md5($tmp);
		$php[$m] = $tmp;
		$fudml = str_replace($res[0], "[php]\n".$m."\n[/php]", $fudml);
	}

	if (strpos($fudml, '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>')  !== false) {
		$fudml = str_replace(array('<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>','</b></td></tr><tr><td class="quote"><br />','<br /></td></tr></table>'), array('[quote title=', ']', '[/quote]'), $fudml);
		// old bad code
		$fudml = str_replace(array('<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>','</b></td></tr><tr><td class="quote"><br>','<br></td></tr></table>'), array('[quote title=', ']', '[/quote]'), $fudml);
	}

	/* old format */
	if (preg_match('!<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis\(\'.*?\', 1\);">.*?</a><div align="left" id="(.*?)" style="visibility: hidden;">!is', $fudml)) {
		$fudml = preg_replace('!\<div class\="dashed" style\="padding: 3px;" align\="center" width\="100%"\>\<a href\="javascript://" OnClick\="javascript: layerVis\(\'.*?\', 1\);">(.*?)\</a\>\<div align\="left" id\=".*?" style\="visibility: hidden;"\>!is', '[spoiler=\1]', $fudml);
		$fudml = str_replace('</div></div>', '[/spoiler]', $fudml);
	}

	/* new format */	
	if (preg_match('!<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis\(\'.*?\', 1\);">.*?</a><div align="left" id="(.*?)" style="display: none;">!is', $fudml)) {
		$fudml = preg_replace('!\<div class\="dashed" style\="padding: 3px;" align\="center" width\="100%"\>\<a href\="javascript://" OnClick\="javascript: layerVis\(\'.*?\', 1\);">(.*?)\</a\>\<div align\="left" id\=".*?" style\="display: none;"\>!is', '[spoiler=\1]', $fudml);
		$fudml = str_replace('</div></div>', '[/spoiler]', $fudml);
	}

	$fudml = str_replace('<font face=', '<font font=', $fudml);
	foreach (array('color', 'font', 'size') as $v) {
		while (preg_match('!<font '.$v.'=".+?">.*?</font>!is', $fudml, $m)) {
			$fudml = preg_replace('!<font '.$v.'="(.+?)">(.*?)</font>!is', '['.$v.'=\1]\2[/'.$v.']', $fudml);
		}
	}

	while (preg_match('!<acronym title=".+?">.*?</acronym>!is', $fudml)) {
		$fudml = preg_replace('!<acronym title="(.+?)">(.*?)</acronym>!is', '[acronym=\1]\2[/acronym]', $fudml);
	}
	while (preg_match('!<(o|u)l type=".+?">.*?</\\1l>!is', $fudml)) {
		$fudml = preg_replace('!<(o|u)l type="(.+?)">(.*?)</\\1l>!is', '[list type=\2]\3[/list]', $fudml);
	}

	$fudml = str_replace(
	array(
		'<b>', '</b>', '<i>', '</i>', '<u>', '</u>', '<s>', '</s>', '<sub>', '</sub>', '<sup>', '</sup>', '<del>', '</del>',
		'<div class="pre"><pre>', '</pre></div>', '<div align="center">', '<div align="left">', '<div align="right">', '</div>',
		'<ul>', '</ul>', '<span name="notag">', '</span>', '<li>', '&#64;', '&#58;&#47;&#47;', '<br />', '<pre>', '</pre>'
	),
	array(
		'[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[s]', '[/s]', '[sub]', '[/sub]', '[sup]', '[/sup]', '[del]', '[/del]', 
		'[code]', '[/code]', '[align=center]', '[align=left]', '[align=right]', '[/align]', '[list]', '[/list]',
		'[notag]', '[/notag]', '[*]', '@', '://', '', '[pre]', '[/pre]'
	),
	$fudml);

	while (preg_match('!<img src="(.*?)" border=0 alt="\\1">!is', $fudml)) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="\\1">!is', '[img]\1[/img]', $fudml);
	}
	while (preg_match('!<img class="(r|l)" src="(.*?)" border=0 alt="\\2">!is', $fudml)) {
		$fudml = preg_replace('!<img class="(r|l)" src="(.*?)" border=0 alt="\\2">!is', '[img\1]\2[/img\1]', $fudml);
	}
	while (preg_match('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', '[email]\1[/email]', $fudml);
	}
	while (preg_match('!<a href="(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">\\1</a>!is', '[url]\1[/url]', $fudml);
	}

	if (strpos($fudml, '<img src="') !== false) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="(.*?)">!is', '[img=\1]\2[/img]', $fudml);
	}
	if (strpos($fudml, '<img class="') !== false) {
		$fudml = preg_replace('!<img class="(r|l)" src="(.*?)" border=0 alt="(.*?)">!is', '[img\1=\2]\3[/img\1]', $fudml);
	}
	if (strpos($fudml, '<a href="mailto:') !== false) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">(.+?)</a>!is', '[email=\1]\2[/email]', $fudml);
	}
	if (strpos($fudml, '<a href="') !== false) {
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">(.+?)</a>!is', '[url=\1]\2[/url]', $fudml);
	}

	if (isset($php)) {
		$fudml = str_replace(array_keys($php), array_values($php), $fudml);
	}

	/* unhtmlspecialchars */
	return reverse_fmt($fudml);
}


function filter_ext($file_name)
{
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'file_filter_regexp';
	if (empty($GLOBALS['__FUD_EXT_FILER__'])) {
		return;
	}
	if (($p = strrpos($file_name, '.')) === false) {
		return 1;
	}
	return !in_array(strtolower(substr($file_name, ($p + 1))), $GLOBALS['__FUD_EXT_FILER__']);
}

function safe_tmp_copy($source, $del_source=0, $prefx='')
{
	if (!$prefx) {
		 $prefx = getmypid();
	}

	$umask = umask(($GLOBALS['FUD_OPT_2'] & 8388608 ? 0177 : 0111));
	if (!move_uploaded_file($source, ($name = tempnam($GLOBALS['TMP'], $prefx.'_')))) {
		return;
	}
	umask($umask);
	if ($del_source) {
		@unlink($source);
	}
	umask($umask);

	return basename($name);
}

function reverse_nl2br(&$data)
{
	if (strpos($data, '<br />') !== false) {
		return str_replace('<br />', '', $data);
	}
	return $data;
}
if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
	}

	if (isset($_GET['del'])) {
		$del = (int) $_GET['del'];
	} else if (isset($_POST['del'])) {
		$del = (int) $_POST['del'];
	} else {
		$del = 0;
	}
	if (isset($_GET['th'])) {
		$th = (int) $_GET['th'];
	} else if (isset($_POST['th'])) {
		$th = (int) $_POST['th'];
	} else {
		$th = 0;
	}

	if (isset($_POST['NO'])) {
		check_return($usr->returnto);
	}

	if ($del) {
		if (!($data = db_saq('SELECT t.forum_id, m.thread_id, m.id, m.subject, t.root_msg_id, m.reply_to, t.replies, mm.id,
			(CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco,
			m.foff, m.length, m.file_id, m.poster_id, u.alias, u.email, m.subject
			FROM fud26_msg m
			INNER JOIN fud26_thread t ON t.id=m.thread_id
			LEFT JOIN fud26_mod mm ON mm.forum_id=t.forum_id AND mm.user_id='._uid.'
			INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647': '0').' AND g1.resource_id=t.forum_id
			LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=t.forum_id
			LEFT JOIN fud26_users u ON u.id=m.poster_id
			WHERE m.id='.$del))) {
			check_return($usr->returnto);
		}

		if ($del && !($data[8] & 32) && !$is_a && !$data[7]) {
			check_return($usr->returnto);
		}

		if (empty($_POST['confirm'])) {
			if ($data[2] != $data[4]) {
				$delete_msg = 'You are about to <span class="ErrorText">DELETE</span> the message titled: <b>'.$data[3].'</b><p>';
			} else {
				$delete_msg = 'You are about to <span class="ErrorText">DELETE</span> the <span class="ErrorText">ENTIRE TOPIC</span> titled: <b>'.$data[3].'</b><p>';
			}
			?> <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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
<div class="ctb">
<table cellspacing="1" cellpadding="2" class="DialogTable">
<tr><th>Confirm Topic/Message Deletion</th></tr>
<tr class="RowStyleA ac"><td class="GenText">
<form method="post" action="index.php?t=mmod">
<input type="hidden" name="del" value="<?php echo $del; ?>">
<input type="hidden" name="confirm" value="1">
<?php echo _hs; ?>
<?php echo $delete_msg; ?>
<div class="mnavWarnTbl">
<b>Optional</b><br />Specify a reason for the deletion of the message, which will be sent to the author via a private message.<br />
<textarea rows="10" cols="40" name="del_reason"></textarea><br />
<input type="checkbox" name="del_inc_body"> Include the body of the removed message that will be sent to the author.
</div>
<span class="fb">Do you wish to proceed?</span><br />
<input type="submit" class="button" name="YES" value="Yes">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" class="button" name="NO" value="No">
</form>
</td></tr>
</table></div>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html> <?php
			exit;
		}

		if (isset($_POST['YES'])) {
			$if_not_pm = (!empty($_POST['del_reason']) && $data[12]);

			if ($if_not_pm && !empty($_POST['del_inc_body'])) {
				$body = read_msg_body($data[9], $data[10], $data[11]);
			} else {
				$body = '';
			}
			if ($if_not_pm) {
				fud_use('ssu.inc');
				fud_use('private.inc');
				if ($body) {
					/* PM disabled, notification will be sent via e-mail. */
					if ($FUD_OPT_1 & 1024) {
						/* will be done by send_status_update() */
						$body = str_replace('<br />', '', $body);
					} else {
						$body = strip_tags($body);
					}
					$body = ($GLOBALS['FUD_OPT_1'] & 1024 ? htmlspecialchars($_POST['del_reason']).'' : $_POST['del_reason'].'' )  .'
--------------------------------------
Original message
--------------------------------------
Subject: '.$data[15].'
--------------------------------------
'.$body;
				} else {
					$body = $FUD_OPT_1 & 1024 ? htmlspecialchars($_POST['del_reason']) : $_POST['del_reason'];
				}
				send_status_update($data[12], $data[13], $data[14], 'Your message was removed by administration staff.', $body);
			}

			if ($data[2] == $data[4]) {
				logaction(_uid, 'DELTHR', 0, '"'.addslashes($data[3]).'" w/'.$data[6].' replies');

				fud_msg_edit::delete(true, $data[2], 1);

				if (strpos($usr->returnto, 'selmsg') === false) {
					if ($FUD_OPT_2 & 32768) {
						header('Location: http://timeweather.net/forum/index.php/f/'.$data[0].'/'._rsidl);
					} else {
						header('Location: http://timeweather.net/forum/index.php?t='.t_thread_view.'&'._rsidl.'&frm_id='.$data[0]);
					}
					exit;
				} else {
					check_return($usr->returnto);
				}
			} else {
				logaction(_uid, 'DELMSG', 0, addslashes($data[3]));
				fud_msg_edit::delete(true, $data[2], 0);
			}
		}

		if (strpos($usr->returnto, 'selmsg') !== false) {
			check_return($usr->returnto);
		}

		if (d_thread_view == 'tree') {
			if (!$data[5]) {
				if ($FUD_OPT_2 & 32768) {
					header('Location: http://timeweather.net/forum/index.php/mv/tree/'.$data[1].'/'._rsidl);
				} else {
					header('Location: http://timeweather.net/forum/index.php?t=tree&'._rsidl.'&th='.$data[1]);
				}
			} else {
				if ($FUD_OPT_2 & 32768) {
					header('Location: http://timeweather.net/forum/index.php/mv/tree/'.$data[1].'/'.$data[5].'/'._rsidl);
				} else {
					header('Location: http://timeweather.net/forum/index.php?t=tree&'._rsidl.'&th='.$data[1].'&mid='.$data[5]);
				}
			}
		} else {
			$prev_id = q_singleval("SELECT id FROM fud26_msg WHERE thread_id=".$data[1]." AND id < ".$data[2]." ORDER BY id DESC LIMIT 1");
		
			if ($FUD_OPT_2 & 32768) {
				header('Location: http://timeweather.net/forum/index.php/mv/msg/'.$data[1].'/'.$prev_id.'/'._rsidl);
			} else {
				header('Location: http://timeweather.net/forum/index.php?t=msg&th='.$data[1].'&'._rsidl.'&goto='.$prev_id);
			}
		}
		exit;
	} else if ($th && (!isset($_GET['th']) || sq_check(0, $usr->sq))) {
		if (!($data = db_saq('SELECT mm.id, (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco
			FROM fud26_thread t
			LEFT JOIN fud26_mod mm ON mm.forum_id=t.forum_id AND mm.user_id='._uid.'
			INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647': '0').' AND g1.resource_id=t.forum_id
			LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=t.forum_id
			WHERE t.id='.$th))) {
			check_return($usr->returnto);
		}
		if (!$data[0] && !($data[1] & 4096) && !$is_a) {
			check_return($usr->returnto);
		}

		if (isset($_GET['lock'])) {
			logaction(_uid, 'THRLOCK', $th);
			th_lock($th, 1);
		} else {
			logaction(_uid, 'THRUNLOCK', $th);
			th_lock($th, 0);
		}
	}
	check_return($usr->returnto);
?>