<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: minimsg.inc.t,v 1.29 2005/03/03 17:20:30 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

$start = isset($_GET['start']) ? (int)$_GET['start'] : (isset($_POST['minimsg_pager_switch']) ? (int)$_POST['minimsg_pager_switch'] : 0);
if ($start < 0) {
	$start = 0;
}
if ($th_id && !$GLOBALS['MINIMSG_OPT_DISABLED']) {
	$count = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;
	$total = $thr->replies + 1;

	if ($reply_to && !isset($_POST['minimsg_pager_switch']) && $total > $count) {
		$start = ($total - q_singleval("SELECT count(*) FROM fud26_msg WHERE thread_id=".$th_id." AND apr=1 AND id>=".$reply_to));
		if ($start < 0) {
			$start = 0;
		}
		$msg_order_by = 'ASC';
	} else {
		$msg_order_by = 'DESC';
	}

	/* This is an optimization intended for topics with many messages */
	if ($total > 250) {
		q("CREATE TEMPORARY TABLE fud26__mtmp_".__request_timestamp__." AS SELECT id FROM fud26_msg WHERE thread_id=".$th_id." AND apr=1 ORDER BY id ".$msg_order_by." LIMIT " . qry_limit($count, $start));
	}

	$c = uq('SELECT m.*, t.thread_opt, t.root_msg_id, t.last_post_id, t.forum_id,
			u.id AS user_id, u.alias AS login, u.users_opt, u.last_visit AS time_sec,
			p.max_votes, p.expiry_date, p.creation_date, p.name AS poll_name,  p.total_votes
		FROM
			'.($total > 250 ? 'fud26__mtmp_'.__request_timestamp__.' mt INNER JOIN fud26_msg m ON m.id=mt.id' : ' fud26_msg m').'
			INNER JOIN fud26_thread t ON m.thread_id=t.id
			LEFT JOIN fud26_users u ON m.poster_id=u.id
			LEFT JOIN fud26_poll p ON m.poll_id=p.id' .
		($total > 250 ? ' ORDER BY m.id ASC' : " WHERE m.thread_id=".$th_id." AND m.apr=1 ORDER BY m.id ASC LIMIT " . qry_limit($count, $start)));

	$message_data='';
	$m_count = 0;
	while ($obj = db_rowobj($c)) {
		$message_data .= tmpl_drawmsg($obj, $usr, $perms, true, $m_count, '');
	}

	$minimsg_pager = tmpl_create_pager($start, $count, $total, "javascript: document.post_form.minimsg_pager_switch.value='%s'; document.post_form.submit();", null, false, false);
	$minimsg = '<br /><br />
<table cellspacing=0 cellpadding=3 class="wa dashed">
<tr><td class="miniMH">Topic View</td></tr>
<tr><td>
<table cellspacing="1" cellpadding="2" class="ContentTable">
'.$message_data.'
</table>
</td></tr>
<tr><td>'.$minimsg_pager.'</td></tr>
</table>
<input type="hidden" name="minimsg_pager_switch" value="'.$start.'">';
} else if ($th_id) {
	$minimsg = '<br /><br />
<table cellspacing=0 cellpadding=3 class="dashed wa">
<tr><td class="ac">[<a href="javascript: document.post_form.submit();">Reveal Thread</a>]</td></tr>
</table>
<input type="hidden" name="minimsg_pager_switch" value="'.$start.'">';
} else {
	$minimsg = '';
}
?>