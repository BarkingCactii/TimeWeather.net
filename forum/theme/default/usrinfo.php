<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: usrinfo.php.t,v 1.46 2004/11/24 19:53:37 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}

function convert_bdate($val, $month_fmt)
{
	$ret['year'] = substr($val, 0, 4);
	if (!(int)$ret['year']) {
		$ret['year'] = '';
	}

	$ret['day'] = substr($val, 6, 2);
	if (!(int)$ret['day']) {
		$ret['day'] = '';
	}

	if (!($month = (int)substr($val, 4, 2))) {
		$ret['month'] = '';
	} else {
		$ret['month'] = strftime($month_fmt, mktime(1, 1, 1, $month, 11, 2000));
	}

	return $ret;
}

	if (!isset($_GET['id']) || !(int)$_GET['id']) {
		invl_inp_err();
	}
	if ($FUD_OPT_3 & 32 && !_uid) {
		if (__fud_real_user__) {
			is_allowed_user($usr);
		} else {
			std_error('login');
		}
	}

	if (!($u = db_sab('SELECT u.*, l.name AS level_name, l.level_opt, l.img AS level_img FROM fud26_users u LEFT JOIN fud26_level l ON l.id=u.level_id WHERE u.id='.(int)$_GET['id']))) {
		std_error('user');
	}
	if (!_uid) {
		header("Last-Modified: " .  gmdate("D, d M Y H:i:s", $u->last_visit) . " GMT");
	}

	if ($FUD_OPT_1 & 28 && $u->users_opt & 8388608 && $u->level_opt & (2|1) == 1) {
		$level_name = $level_image = '';
	} else {
		$level_name = $u->level_name ? $u->level_name.'<br />' : '';
		$level_image = $u->level_img ? '<img src="images/'.$u->level_img.'" /><br />' : '';
	}

	if (!$is_a) {
		$frm_perms = get_all_read_perms(_uid, ($usr->users_opt & 524288));
		$forum_list = implode(',', array_keys($frm_perms, 2));
	} else {
		$forum_list = 1;
	}

	$moderation = '';
	if ($u->users_opt & 524288 && $forum_list) {
		$c = uq('SELECT f.id, f.name FROM fud26_mod mm INNER JOIN fud26_forum f ON mm.forum_id=f.id INNER JOIN fud26_cat c ON f.cat_id=c.id WHERE '.($is_a ? '' : 'f.id IN('.$forum_list.') AND ').'mm.user_id='.$u->id);
		while ($r = db_rowarr($c)) {
			$moderation .= '<a href="index.php?t='.t_thread_view.'&amp;frm_id='.$r[0].'&amp;'._rsid.'">'.$r[1].'</a>&nbsp;';
		}
		if ($moderation) {
			$moderation = 'Moderator of:&nbsp;'.$moderation;
		}
	}

if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
	}

	$TITLE_EXTRA = ': User Information '.$u->alias;

	ses_update_status($usr->sid, 'Looking at <a href="index.php?t=usrinfo&amp;id='.$u->id.'">'.$u->alias.'&#39;s</a> profile');

	$avg = round($u->posted_msg_count / ((__request_timestamp__ - $u->join_date) / 86400), 2);
	if ($avg > $u->posted_msg_count) {
		$avg = $u->posted_msg_count;
	}

	$last_post = '';
	if ($u->u_last_post_id) {
		$r = db_saq('SELECT m.subject, m.id, m.post_stamp, t.forum_id FROM fud26_msg m INNER JOIN fud26_thread t ON m.thread_id=t.id WHERE m.id='.$u->u_last_post_id);
		if ($is_a || !empty($frm_perms[$r[3]])) {
			$last_post = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="vt nw GenText">Last Message:</td><td class="GenText"><span class="DateText">'.strftime("%a, %d %B %Y %H:%M", $r[2]).'</span><br /><a href="index.php?t='.d_thread_view.'&amp;goto='.$r[1].'&amp;'._rsid.'#msg_'.$r[1].'">'.$r[0].'</a></td></tr>';
		}
	}

	if ($u->users_opt & 1) {
		$email_link = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="GenText nw">E-mail:</td><td class="GenText"><a href="mailto:'.$u->email.'">'.$u->email.'</a></td></tr>';
	} else if ($FUD_OPT_2 & 1073741824) {
		$email_link = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">E-mail:</td><td class="GenText">[<a href="index.php?t=email&amp;toi='.$u->id.'&amp;'._rsid.'">Click here to e-mail the user</a>]</td></tr>';
	} else {
		$email_link = '';
	}

	if ($FUD_OPT_2 & 8192 && ($referals = q_singleval('SELECT count(*) FROM fud26_users WHERE referer_id='.$u->id))) {
		$referals = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Referred Users:</td><td class="GenText"><a href="index.php?t=list_referers&amp;'._rsid.'">'.$referals.' Members</a></td></tr>';
	} else {
		$referals = '';
	}

	if (_uid && _uid != $u->id && !q_singleval("SELECT id FROM fud26_buddy WHERE user_id="._uid." AND bud_id=".$u->id)) {
		$buddy = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Buddy:</td><td class="GenText"><a href="index.php?t=buddy_list&amp;add='.$u->id.'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">add to buddy list</a></td></tr>';
	} else {
		$buddy = '';
	}

	if ($forum_list && ($polls = q_singleval('SELECT count(*) FROM fud26_poll p INNER JOIN fud26_forum f ON p.forum_id=f.id WHERE p.owner='.$u->id.' AND f.cat_id>0 '.($is_a ? '' : ' AND f.id IN('.$forum_list.')')))) {
		$polls = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Polls:</td><td class="GenText"><a href="index.php?t=polllist&amp;uid='.$u->id.'&amp;'._rsid.'">'.$polls.'</a></td></tr>';
	} else {
		$polls = '';
	}

	if ($u->users_opt & 1024) {
		$gender = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Gender:</td><td class="GenText">Male</td></tr>';
	} else if (!($u->users_opt & 512)) {
		$gender = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Gender:</td><td class="GenText">Female</td></tr>';
	} else {
		$gender = '';
	}

	if ($u->bday) {
		$bday = convert_bdate($u->bday, '%B');
		$birth_date = '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Date Of Birth:</td><td class="GenText">'.$bday['month'].' '.$bday['day'].', '.$bday['year'].'</td></tr>';
	} else {
		$birth_date = '';
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
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th colspan=2 class="wa "><?php echo $u->alias; ?>&#39;s Profile</th></tr>
<tr class="RowStyleA"><td class="nw GenText">Date Registered:</td><td class="wa DateText"><?php echo strftime("%a, %B %d, %Y", $u->join_date); ?></td></tr>
<tr class="RowStyleB"><td class="vt nw GenText">Message Count:</td><td class="GenText"><?php echo $u->posted_msg_count; ?> Messages(s) (<?php echo $avg; ?> average messages per day)<br /><a href="index.php?t=showposts&amp;id=<?php echo $u->id; ?>&amp;<?php echo _rsid; ?>">Show all messages by <?php echo $u->alias; ?></a></td></tr>
<?php echo ($u->users_opt & 32768 ? '' : '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Real Name:</td><td class="GenText">'.$u->name.'</td></tr>'); ?>
<?php echo (($level_name || $moderation || $level_image || $u->custom_status) ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw vt GenText">Status:</td><td class="GenText">
<span class="LevelText">
'.$level_name.'
'.$level_image.'
'.($u->custom_status ? $u->custom_status.'<br />' : '' )  .'
</span>
'.$moderation.'
</td></tr>' : ''); ?>
<?php echo (($FUD_OPT_1 & 28 && $u->users_opt & 8388608 && !($u->level_opt & 2)) ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="vt nw GenText">Avatar:</td><td class="GenText">'.$u->avatar_loc.'</td></tr>' : ''); ?>
<?php echo $last_post; ?>
<?php echo ($u->last_visit && !($u->users_opt & 32768) ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="vt nw GenText">Last Visited:</td><td class="GenText DateText">'.strftime("%a, %d %B %Y %H:%M", $u->last_visit).'</td></tr>' : ''); ?>
<?php echo $polls; ?>
<?php echo (($FUD_OPT_2 & 65536 && $u->user_image && strpos($u->user_image, '://')) ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="vt nw GenText">Image:</td><td class="GenText"><img src="'.$u->user_image.'" /></td></tr>' : ''); ?>
<?php echo $email_link; ?>
<?php echo (($FUD_OPT_1 & 1024 && _uid) ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Private Message:</td><td class="GenText"><a href="index.php?t=ppost&amp;'._rsid.'&amp;toi='.$u->id.'"><img src="theme/default/images/msg_pm.gif" /></a></td></tr>' : ''); ?>
<?php echo $buddy; ?>
<?php echo $referals; ?>
<?php echo ($u->home_page ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Homepage:</td><td class="GenText"><a href="'.$u->home_page.'" target="_blank">'.$u->home_page.'</a></td></tr>' : ''); ?>
<?php echo $gender; ?>
<?php echo ($u->location ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Location:</td><td class="GenText">'.$u->location.'</td></tr>' : ''); ?>
<?php echo ($u->occupation ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Occupation:</td><td class="GenText">'.$u->occupation.'</td></tr>' : ''); ?>
<?php echo ($u->interests ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Interests:</td><td class="GenText">'.$u->interests.'</td></tr>' : ''); ?>
<?php echo ($u->bio ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Biography:</td><td class="GenText">'.$u->bio.'</td></tr>' : ''); ?>
<?php echo $birth_date; ?>
<?php echo ($u->icq ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw vt GenText"><a name="icq_msg">ICQ Message Form:</a></td><td class="GenText">
		'.$u->icq.' <img src="http://web.icq.com/whitepages/online?icq='.$u->icq.'&amp;img=5" /><br />
			<table class="icqCP">
			<tr><td colspan=2>
				<form action="http://wwp.icq.com/scripts/WWPMsg.dll" method="post" target=_blank>
				<b>ICQ Online-Message Panel</b>
			</td></tr>
			<tr>
				<td>
					Sender Name:<br />
					<input type="text" name="from" value="" size="15" maxlength="40" onfocus="this.select()">
				</td>
				<td>
					Sender E-mail:<br />
					<input type="text" name="fromemail" value="" size="15" maxlength="40" onfocus="this.select()">
				</td>
			</tr>
			<tr>
				<td colspan=2>
					Subject<br />
					<input type="text" name="subject" value="" size="32"><br />
					Message<br />
					<textarea name="body" rows="3" cols="32" wrap="Virtual"></textarea>
					<input type="hidden" name="to" value="'.$u->icq.'"><br />
				</td>
			</tr>
			<tr><td colspan=2 align=right><input type="submit" class="button" name="Send" value="Send"></td></tr>
			</form>
			</table>
			</td>
</tr>' : ''); ?>
<?php echo ($u->aim ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">AIM Handle:</td><td class="GenText"><a href="aim:goim?screenname='.$u->aim.'&amp;message=Hello+Are+you+there?">'.$u->aim.'</a></td></tr>' : ''); ?>
<?php echo ($u->yahoo ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Yahoo Messenger:</td><td class="GenText"><a href="http://edit.yahoo.com/config/send_webmesg?.target='.$u->yahoo.'&amp;.src=pg">'.$u->yahoo.'</a></td></tr>' : ''); ?>
<?php echo ($u->msnm ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">MSN Messenger:</td><td class="GenText">'.htmlspecialchars(urldecode($u->msnm)).'</td></tr>' : ''); ?>
<?php echo ($u->jabber ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Jabber:</td><td class="GenText">'.$u->jabber.'</td></tr>' : ''); ?>
<?php echo (($FUD_OPT_2 & 2048 && $u->affero) ? '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="nw GenText">Affero Username</td><td class="GenText"><a href="http://svcs.affero.net/user-history.php?u='.$u->affero.'" target="_blank">'.htmlspecialchars(urldecode($u->affero)).'</a></td></tr>' : ''); ?>
<tr class="RowStyleC"><td class="nw ar GenText" colspan=2><a href="index.php?t=showposts&amp;id=<?php echo $u->id; ?>&amp;<?php echo _rsid; ?>">Show all messages by <?php echo $u->alias; ?></a></td></tr>
</table>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>