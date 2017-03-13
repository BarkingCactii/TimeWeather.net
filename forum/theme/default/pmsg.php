<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: pmsg.php.t,v 1.50 2005/03/18 01:58:51 hackie Exp $
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
}$folders = array(1=>'Inbox', 2=>'Saved', 4=>'Draft', 3=>'Sent', 5=>'Trash');

function tmpl_cur_ppage($folder_id, $folders, $msg_subject='')
{
	if (!$folder_id || (!$msg_subject && $_GET['t'] == 'ppost')) {
		$user_action = 'Writing a Private Message';
	} else {
		$user_action = $msg_subject ? '<a href="index.php?t=pmsg&amp;folder_id='.$folder_id.'&amp;'._rsid.'">'.$folders[$folder_id].'</a> &raquo; '.$msg_subject : 'Browsing <b>'.$folders[$folder_id].'</b> folder';
	}

	return '<span class="SmallText"><a href="index.php?t=pmsg&amp;'._rsid.'">Private Messaging</a>&nbsp;&raquo;&nbsp;'.$user_action.'</span><br /><img src="blank.gif" alt="" height=4 width=1 /><br />';
}$GLOBALS['recv_user_id'] = array();

class fud_pmsg
{
	var	$id, $to_list, $ouser_id, $duser_id, $pdest, $ip_addr, $host_name, $post_stamp, $icon, $fldr,
		$subject, $attach_cnt, $pmsg_opt, $length, $foff, $login, $ref_msg_id, $body;

	function add($track='')
	{
		$this->post_stamp = __request_timestamp__;
		$this->ip_addr = get_ip();
		$this->host_name = $GLOBALS['FUD_OPT_1'] & 268435456 ? "'".addslashes(get_host($this->ip_addr))."'" : 'NULL';

		if ($this->fldr != 1) {
			$this->read_stamp = $this->post_stamp;
		}

		list($this->foff, $this->length) = write_pmsg_body($this->body);

		$this->id = db_qid("INSERT INTO fud26_pmsg (
			ouser_id,
			duser_id,
			pdest,
			to_list,
			ip_addr,
			host_name,
			post_stamp,
			icon,
			fldr,
			subject,
			attach_cnt,
			read_stamp,
			ref_msg_id,
			foff,
			length,
			pmsg_opt
			) VALUES(
				".$this->ouser_id.",
				".$this->ouser_id.",
				".(isset($GLOBALS['recv_user_id'][0]) ? intzero($GLOBALS['recv_user_id'][0]) : '0').",
				".strnull(addslashes($this->to_list)).",
				'".$this->ip_addr."',
				".$this->host_name.",
				".$this->post_stamp.",
				".strnull($this->icon).",
				".$this->fldr.",
				'".addslashes($this->subject)."',
				".(int)$this->attach_cnt.",
				".$this->read_stamp.",
				".strnull($this->ref_msg_id).",
				".(int)$this->foff.",
				".(int)$this->length.",
				".$this->pmsg_opt."
			)");

		if ($this->fldr == 3 && !$track) {
			$this->send_pmsg();
		}
	}

	function send_pmsg()
	{
		$this->pmsg_opt |= 16|32;
		$this->pmsg_opt &= 16|32|1|2|4;

		foreach($GLOBALS['recv_user_id'] as $v) {
			$id = db_qid("INSERT INTO fud26_pmsg (
				to_list,
				ouser_id,
				ip_addr,
				host_name,
				post_stamp,
				icon,
				fldr,
				subject,
				attach_cnt,
				foff,
				length,
				duser_id,
				ref_msg_id,
				pmsg_opt
			) VALUES (
				".strnull(addslashes($this->to_list)).",
				".$this->ouser_id.",
				'".$this->ip_addr."',
				".$this->host_name.",
				".$this->post_stamp.",
				".strnull($this->icon).",
				1,
				'".addslashes($this->subject)."',
				".intzero($this->attach_cnt).",
				".$this->foff.",
				".$this->length.",
				".$v.",
				".strnull($this->ref_msg_id).",
				".$this->pmsg_opt.")");
			$GLOBALS['send_to_array'][] = array($v, $id);
			$um[$v] = $id;
		}
		$c =  uq('SELECT id, email FROM fud26_users WHERE id IN('.implode(',', $GLOBALS['recv_user_id']).') AND users_opt>=64 AND (users_opt & 64) > 0');

		$from = reverse_fmt($GLOBALS['usr']->alias);
		$subject = reverse_fmt($this->subject);

		while ($r = db_rowarr($c)) {
			/* do not send notifications about messages sent to self */
			if ($r[0] == $this->ouser_id) {
				continue;
			}
			send_pm_notification($r[1], $um[$r[0]], $subject, $from);
		}
	}

	function sync()
	{
		$this->post_stamp = __request_timestamp__;
		$this->ip_addr = get_ip();
		$this->host_name = $GLOBALS['FUD_OPT_1'] & 268435456 ? "'".addslashes(get_host($this->ip_addr))."'" : 'NULL';

		list($this->foff, $this->length) = write_pmsg_body($this->body);

		q("UPDATE fud26_pmsg SET
			to_list=".strnull(addslashes($this->to_list)).",
			icon=".strnull($this->icon).",
			ouser_id=".$this->ouser_id.",
			duser_id=".$this->ouser_id.",
			post_stamp=".$this->post_stamp.",
			subject='".addslashes($this->subject)."',
			ip_addr='".$this->ip_addr."',
			host_name=".$this->host_name.",
			attach_cnt=".(int)$this->attach_cnt.",
			fldr=".$this->fldr.",
			foff=".(int)$this->foff.",
			length=".(int)$this->length.",
			pmsg_opt=".$this->pmsg_opt."
		WHERE id=".$this->id);

		if ($this->fldr == 3) {
			$this->send_pmsg();
		}
	}
}

function set_nrf($nrf, $id)
{
	q("UPDATE fud26_pmsg SET pmsg_opt=(pmsg_opt & ~ 96) | ".$nrf." WHERE id=".$id);
}

function write_pmsg_body($text)
{
	if (($ll = !db_locked())) {
		db_lock('fud26_fl_pm WRITE');
	}

	$fp = fopen($GLOBALS['MSG_STORE_DIR'].'private', 'ab');

	fseek($fp, 0, SEEK_END);
	if (!($s = ftell($fp))) {
		$s = __ffilesize($fp);
	}

	if (($len = fwrite($fp, $text)) !== strlen($text)) {
		exit("FATAL ERROR: system has ran out of disk space<br>\n");
	}
	fclose($fp);

	if ($ll) {
		db_unlock();
	}

	if (!$s) {
		chmod($GLOBALS['MSG_STORE_DIR'].'private', ($GLOBALS['FUD_OPT_2'] & 8388608 ? 0600 : 0666));
	}

	return array($s, $len);
}

function read_pmsg_body($offset, $length)
{
	if (!$length) {
		return;
	}

	$fp = fopen($GLOBALS['MSG_STORE_DIR'].'private', 'rb');
	fseek($fp, $offset, SEEK_SET);
	$str = fread($fp, $length);
	fclose($fp);

	return $str;
}

function pmsg_move($mid, $fid, $validate)
{
	if (!$validate && !q_singleval('SELECT id FROM fud26_pmsg WHERE duser_id='._uid.' AND id='.$mid)) {
		return;
	}

	q('UPDATE fud26_pmsg SET fldr='.$fid.' WHERE duser_id='._uid.' AND id='.$mid);
}

function pmsg_del($mid, $fldr=0)
{
	if (!$fldr && !($fldr = q_singleval('SELECT fldr FROM fud26_pmsg WHERE duser_id='._uid.' AND id='.$mid))) {
		return;
	}

	if ($fldr != 5) {
		pmsg_move($mid, 5, 0);
	} else {
		q('DELETE FROM fud26_pmsg WHERE id='.$mid);
		$c = uq('SELECT id FROM fud26_attach WHERE message_id='.$mid.' AND attach_opt=1');
		while ($r = db_rowarr($c)) {
			@unlink($GLOBALS['FILE_STORE'] . $r[0] . '.atch');
		}
		q('DELETE FROM fud26_attach WHERE message_id='.$mid.' AND attach_opt=1');
	}
}

function send_pm_notification($email, $pid, $subject, $from)
{
	send_email($GLOBALS['NOTIFY_FROM'], $email, '['.$GLOBALS['FORUM_TITLE'].'] New Private Message Notification', 'You have a new private message titled "'.$subject.'", from "'.$from.'", in the forum "'.$GLOBALS['FORUM_TITLE'].'".\nTo view the message, click here: http://timeweather.net/forum/index.php?t=pmsg_view&id='.$pid.'\n\nTo stop future notifications, disable "Private Message Notification" in your profile.');
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
}

	if (!($FUD_OPT_1 & 1024)) {
		error_dialog('ERROR: Private Messaging Disabled', 'You cannot use the private messaging system. It has been disabled by the administrator.');
	}

	if (__fud_real_user__) {
		is_allowed_user($usr);
	} else {
		std_error('login');
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

	/* empty trash */
	if (isset($_POST['btn_trash'])) {
		$c = q("SELECT id FROM fud26_pmsg WHERE duser_id="._uid." AND fldr=5");
		while ($r = db_rowarr($c)) {
			pmsg_del((int)$r[0], 5);
		}
		unset($c, $_POST['sel'], $_GET['sel']); /* prevent message selection cofusion */
	}

	$all_v = empty($_GET['all']);

	/* moving or deleting a message */
	if (isset($_POST['sel']) || isset($_GET['sel'])) {
		if (!empty($_POST['btn_pdf'])) {
			header("Location: http://timeweather.net/forum/pdf.php?sel[]=".implode("&sel[]=", $_POST['sel'])."&"._rsidl);
			exit;		
		}
		$sel = isset($_POST['sel']) ? (array)$_POST['sel'] : (array)$_GET['sel'];
		$move_to = (!isset($_POST['btn_delete']) && isset($_POST['moveto'], $folders[$_POST['moveto']])) ? (int) $_POST['moveto'] : 0;
		foreach ($sel as $m) {
			if ($move_to) {
				pmsg_move((int)$m, $move_to, 0);
			} else {
				pmsg_del((int)$m);
			}
		}
	}

	if (isset($_GET['folder_id'], $folders[$_GET['folder_id']])) {
		$folder_id = $_GET['folder_id'];
	} else if (isset($_POST['folder_id'], $folders[$_POST['folder_id']])) {
		$folder_id = $_POST['folder_id'];
	} else {
		$folder_id = 1;
	}

	ses_update_status($usr->sid, 'Using private messaging');

	$select_options_cur_folder = tmpl_draw_select_opt(implode("\n", array_keys($folders)), implode("\n", $folders), $folder_id);

	$disk_usage = q_singleval('SELECT SUM(length) FROM fud26_pmsg WHERE duser_id='._uid);
	$percent_full = ceil($disk_usage / $MAX_PMSG_FLDR_SIZE * 100);
	$full_indicator = ceil($percent_full * 1.69);

	if ($percent_full < 90) {
		$full_indicator = '<td class="pmSn"><img src="blank.gif" alt="" width='.$full_indicator.' height="8" /></td>';
	} else if ($percent_full >= 90 && $percent_full < 100) {
		$full_indicator = '<td class="pmSa"><img src="blank.gif" alt="" width='.$full_indicator.' height="8" /></td>';
	} else {
		$full_indicator = '<td class="pmSf"><img src="blank.gif" alt="" width='.$full_indicator.' height="8" /></td>';
	}

	$ttl = q_singleval("SELECT count(*) FROM fud26_pmsg WHERE duser_id="._uid." AND fldr=".$folder_id);
	$count = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;
	$start = (empty($_GET['start']) || $_GET['start'] >= $ttl) ? 0 : (int) $_GET['start'];

	$c = uq('SELECT p.id, p.read_stamp, p.post_stamp, p.duser_id, p.ouser_id, p.subject, p.pmsg_opt, p.fldr, p.pdest, p.to_list,
			u.users_opt, u.alias, u.last_visit AS time_sec,
			u2.users_opt AS users_opt2, u2.alias AS alias2, u2.last_visit AS time_sec2
		FROM fud26_pmsg p
		INNER JOIN fud26_users u ON p.ouser_id=u.id
		LEFT JOIN fud26_users u2 ON p.pdest=u2.id
		WHERE duser_id='._uid.' AND fldr='.$folder_id.' ORDER BY post_stamp DESC LIMIT '.qry_limit($count, $start));

	$private_msg_entry = '';
	while ($obj = db_rowobj($c)) {
		switch ($obj->fldr) {
			case 1:
			case 2:
				$action = '<a href="index.php?t=ppost&amp;'._rsid.'&amp;reply='.$obj->id.'"><img src="theme/default/images/msg_reply.gif" alt="" /></a>&nbsp;<a href="index.php?t=ppost&amp;quote='.$obj->id.'&amp;'._rsid.'"><img src="theme/default/images/msg_quote.gif" alt="" /></a>&nbsp;<a href="index.php?t=ppost&amp;forward='.$obj->id.'&amp;'._rsid.'"><img src="theme/default/images/msg_forward.gif" alt="" /></a>';
				break;
			case 3:
				$obj->users_opt = $obj->users_opt2;
				$obj->alias = $obj->alias2;
				$obj->time_sec = $obj->time_sec2;
				$obj->ouser_id = $obj->pdest;
				$action = '';
				break;
			case 5:
				$action = '<a href="index.php?t=ppost&amp;'._rsid.'&amp;forward='.$obj->id.'"><img src="theme/default/images/msg_forward.gif" alt="" /></a>';
				break;
			case 4:
				$action = '<a href="index.php?t=ppost&amp;'._rsid.'&amp;msg_id='.$obj->id.'"><img src="theme/default/images/msg_edit.gif" alt="" /></a>';
				break;
		}

		if ($FUD_OPT_2 & 32768 && !empty($_SERVER['PATH_INFO'])) {
			$goto = $folder_id != 4 ? 'index.php/pmv/'.$obj->id.'/'._rsid : 'index.php/pmm/msg_id/'.$obj->id.'/'._rsid;
		} else {
			$goto = $folder_id != 4 ? 'index.php?t=pmsg_view&amp;'._rsid.'&amp;id='.$obj->id : 'index.php?t=ppost&amp;'._rsid.'&amp;msg_id='.$obj->id;
		}

		if ($FUD_OPT_2 & 32 && (!($obj->users_opt & 32768) || $is_a)) {
			$obj->login =& $obj->alias;
			if (($obj->time_sec + $LOGEDIN_TIMEOUT * 60) > __request_timestamp__) {
				$online_indicator = '<img src="theme/default/images/online.png" alt="'.$obj->login.' is currently online" title="'.$obj->login.' is currently online" />&nbsp;';
			} else {
				$online_indicator = '<img src="theme/default/images/offline.png" alt="'.$obj->login.'  is currently offline" title="'.$obj->login.'  is currently offline" />&nbsp;';
			}
		} else {
			$online_indicator = '';
		}

		if ($obj->pmsg_opt & 64) {
			$msg_type ='<span class="SmallText">(replied)</span>&nbsp;';
		} else if ($obj->pmsg_opt & 32) {
			$msg_type = '';
		} else {
			$msg_type ='<span class="SmallText">(forwarded)</span>&nbsp;';
		}

		$private_msg_entry .= '<tr class="RowStyleB">
<td class="ac GenText"><input type="checkbox" name="sel[]" value="'.$obj->id.'"'.(!$all_v ? ' checked' : '' ) .'></td>
<td>'.($obj->read_stamp ? '<img src="theme/default/images/pmsg_read.png" alt="Read private message" title="Read private message" />' : '<img src="theme/default/images/pmsg_unread.png" alt="Unread private message" title="Unread private message" />' ) .'</td><td width="100%" class="GenText">'.$msg_type.'<a href="'.$goto.'">'.$obj->subject.'</a>'.(($obj->pmsg_opt & 4 && $obj->pmsg_opt & 16 && $obj->duser_id == _uid && $obj->ouser_id != _uid) ? '<span class="SmallText">&nbsp;&nbsp;[<a href="index.php?t=pmsg_view&amp;'._rsid.'&amp;dr=1&amp;id='.$obj->id.'" title="Do not send a confirmation that you&#39;ve read this message">deny receipt</a>]</span>' : '' ) .'</td>
<td class="nw GenText">'.$online_indicator.'<a href="index.php?t=usrinfo&amp;'._rsid.'&amp;id='.$obj->ouser_id.'" title="'.char_fix(htmlspecialchars($obj->to_list)).'">'.$obj->alias.'</a></td>
<td class="nw DateText">'.strftime("%a, %d %B %Y %H:%M", $obj->post_stamp).'</td>
<td class="nw ac GenText">'.$action.'</td>
</tr>';
	}

	if (!$private_msg_entry) {
		$private_msg_entry = '<tr class="RowStyleC"><td colspan="6" class="ac">There are no messages in this folder.</td></tr>';
		$private_tools = '';
	} else {
		if ($folder_id == 5) {
			$btn_action = 'Restore To:';
			$btn_del_name = 'btn_trash';
			$btn_del_title = 'Empty Trash';
		} else {
			$btn_action = 'Move To:';
			$btn_del_name = 'btn_delete';
			$btn_del_title = 'Delete';
		}
		$tmp = $folders;
		unset($tmp[$folder_id]);
		$moveto_list = tmpl_draw_select_opt(implode("\n", array_keys($tmp)), implode("\n", $tmp), '');
		$private_tools = '<tr class="RowStyleB"><td colspan=6 class="GenText ar">
<input type="submit" class="button" name="btn_move" value="'.$btn_action.'">
<select name="moveto">'.$moveto_list.'</select>
&nbsp;&nbsp;&nbsp;<input type="submit" class="button" name="'.$btn_del_name.'" value="'.$btn_del_title.'">'.($FUD_OPT_2 & 2097152 ? '&nbsp;&nbsp;&nbsp;<input type="submit" class="button" name="btn_pdf" value="Make PDF Archive">' : '' )  .'</td></tr>';
	}

	if ($FUD_OPT_2 & 32768) {
		$page_pager = tmpl_create_pager($start, $count, $ttl, 'index.php/pdm/' . $folder_id . '/0/', '/' . _rsid);
	} else {
		$page_pager = tmpl_create_pager($start, $count, $ttl, 'index.php?t=pmsg&amp;folder_id=' . $folder_id . '&amp;'. _rsid);
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
<?php echo tmpl_cur_ppage($folder_id, $folders); ?>
<table cellspacing="1" cellpadding="2" class="pmDu">
<tr>
	<td colspan="3" class="RowStyleA wa nw SmallText">Your private message folders are <?php echo $percent_full; ?>% full.</td>
</tr>
<tr>
	<td colspan="3" class="RowStyleB wa"><table cellspacing="0" cellpadding="1" border="0"><tr><?php echo $full_indicator; ?></tr></table></td>
</tr>
<tr class="RowStyleA">
	<td class="SmallText" width="58" >0%</td>
	<td class="SmallText ac" width="58">50%</td>
	<td width="58" class="ar"><table cellspacing=0 cellpadding=0 border=0><tr><td width=58 class="SmallText ar">100%</td></tr></table></td>
</tr>
</table>
<div class="ar"><a href="index.php?t=ppost&amp;<?php echo _rsid; ?>"><img src="theme/default/images/new_pm.gif" alt="" /></a></div>
<?php echo $tabs; ?>
<form action="index.php?t=pmsg" method="post" name="priv_frm"><?php echo _hs; ?>
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr class="RowStyleB"><td colspan=6 class="GenText ar">
Folder: <select name="folder_id" onChange="javascript: document.priv_frm.submit();">
<?php echo $select_options_cur_folder; ?>
</select> <input type="submit" class="button" name="sel_folder" value="Go">
</td></tr>
<tr>
	<th class="nw">Selected [<a href="index.php?t=pmsg&amp;folder_id=<?php echo $folder_id; ?>&amp;<?php echo _rsid; ?>&amp;all=<?php echo $all_v; ?>&amp;start=<?php echo $start; ?>" class="thLnk"><?php echo ($all_v ? 'all' : 'none'); ?></a>]</th>
	<th>&nbsp;</th>
	<th class="wa">Subject</th>
	<th class="ac"><?php echo ($folder_id == 3 ? 'Recipient' : 'Author'); ?></th>
	<th class="ac">Time</th>
	<th class="ac">Action</th>
</tr>
<?php echo $private_msg_entry; ?>
<?php echo $private_tools; ?>
</table></form>
<?php echo $page_pager; ?>
<div class="ar pmL"><a href="index.php?t=ppost&amp;<?php echo _rsid; ?>"><img src="theme/default/images/new_pm.gif" alt="" /></a></div>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>