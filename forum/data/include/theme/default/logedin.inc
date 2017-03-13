<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: logedin.inc.t,v 1.35 2004/11/24 19:53:35 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

function rebuild_stats_cache($last_msg_id)
{
	$tm_expire = __request_timestamp__ - ($GLOBALS['LOGEDIN_TIMEOUT'] * 60);

	list($obj->last_user_id, $obj->user_count) = db_saq('SELECT MAX(id), count(*)-1 FROM fud26_users');

	$obj->online_users_anon	= q_singleval('SELECT count(*) FROM fud26_ses s WHERE time_sec>'.$tm_expire.' AND user_id>2000000000');
	$obj->online_users_hidden = q_singleval('SELECT count(*) FROM fud26_ses s INNER JOIN fud26_users u ON u.id=s.user_id WHERE s.time_sec>'.$tm_expire.' AND (u.users_opt & 32768) > 0');
	$obj->online_users_reg = q_singleval('SELECT count(*) FROM fud26_ses s INNER JOIN fud26_users u ON u.id=s.user_id WHERE s.time_sec>'.$tm_expire.' AND (u.users_opt & 32768)=0');
	$c = uq('SELECT u.id, u.alias, u.users_opt, u.custom_color FROM fud26_ses s INNER JOIN fud26_users u ON u.id=s.user_id WHERE s.time_sec>'.$tm_expire.' AND (u.users_opt & 32768)=0 ORDER BY s.time_sec DESC LIMIT '.$GLOBALS['MAX_LOGGEDIN_USERS']);
	$obj->online_users_text = array();
	while ($r = db_rowarr($c)) {
		$obj->online_users_text[$r[0]] = draw_user_link($r[1], $r[2], $r[3]);
	}

	q('UPDATE fud26_stats_cache SET
		cache_age='.__request_timestamp__.',
		last_user_id='.(int)$obj->last_user_id.',
		user_count='.(int)$obj->user_count.',
		online_users_anon='.(int)$obj->online_users_anon.',
		online_users_hidden='.(int)$obj->online_users_hidden.',
		online_users_reg='.(int)$obj->online_users_reg.',
		online_users_text='.strnull(addslashes(serialize($obj->online_users_text))));

	$obj->last_user_alias = q_singleval('SELECT alias FROM fud26_users WHERE id='.$obj->last_user_id);
	$obj->last_msg_subject = q_singleval('SELECT subject FROM fud26_msg WHERE id='.$last_msg_id);

	return $obj;
}

$logedin = $forum_info = '';

if ($FUD_OPT_1 & 1073741824 || $FUD_OPT_2 & 16) {
	if (!($st_obj = db_sab('SELECT sc.*,m.subject AS last_msg_subject, u.alias AS last_user_alias FROM fud26_stats_cache sc INNER JOIN fud26_users u ON u.id=sc.last_user_id INNER JOIN fud26_msg m ON m.id='.$last_msg_id.' WHERE sc.cache_age>'.(__request_timestamp__ - $STATS_CACHE_AGE)))) {
		$st_obj =& rebuild_stats_cache($last_msg_id);
	} else if ($st_obj->online_users_text) {
		$st_obj->online_users_text = unserialize($st_obj->online_users_text);
	}

	if ($FUD_OPT_1 & 1073741824) {
		if (!empty($st_obj->online_users_text)) {
			foreach($st_obj->online_users_text as $k => $v) {
				$logedin .= '<a href="index.php?t=usrinfo&amp;id='.$k.'&amp;'._rsid.'">'.$v.'</a> ';
			}
		}
		$logedin = '<tr><th class="wa">Logged in users list '.($FUD_OPT_1 & 536870912 ? '[<a href="index.php?t=actions&amp;'._rsid.'" class="thLnk">show what people are doing</a>] [<a href="index.php?t=online_today&amp;'._rsid.'" class="thLnk">Today&#39;s Visitors</a>]' : '' ) .'</th></tr>
<tr><td class="RowStyleA">
<span class="SmallText">There are <b>'.$st_obj->online_users_reg.'</b> members(s), <b>'.$st_obj->online_users_hidden.'</b> invisible members and <b>'.$st_obj->online_users_anon.'</b> guest(s) visiting this board.&nbsp;&nbsp;&nbsp;<span class="adminColor">[Administrator]</span>&nbsp;&nbsp;<span class="modsColor">[Moderator]</span></span><br />
'.$logedin.'
</td></tr>';
	}
	if ($FUD_OPT_2 & 16) {
		$forum_info = '<tr><td class="RowStyleB SmallText">
Our users have posted a total of <b>'.$post_count.'</b> messages inside <b>'.$thread_count.'</b> topics.<br />
We have <b>'.$st_obj->user_count.'</b> registered user(s).<br />
The newest registered user is <a href="index.php?t=usrinfo&amp;id='.$st_obj->last_user_id.'&amp;'._rsid.'"><b>'.$st_obj->last_user_alias.'</b></a>'.($last_msg_id ? '<br />Last message on the forum: <a href="index.php?t='.d_thread_view.'&amp;goto='.$last_msg_id.'&amp;'._rsid.'#msg_'.$last_msg_id.'"><b>'.$st_obj->last_msg_subject.'</b></a>' : '' ) .'</td></tr>';
	}
}
?>