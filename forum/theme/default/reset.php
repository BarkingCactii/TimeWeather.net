<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: reset.php.t,v 1.23 2004/12/08 17:20:21 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}class fud_user
{
	var $id, $login, $alias, $passwd, $plaintext_passwd, $name, $email, $location, $occupation, $interests,
	    $icq, $aim, $yahoo, $msnm, $jabber, $affero, $avatar, $avatar_loc, $posts_ppg, $time_zone, $bday, $home_page,
	    $sig, $bio, $posted_msg_count, $last_visit, $last_event, $conf_key, $user_image, $join_date, $theme, $last_read,
	    $mod_list, $mod_cur, $level_id, $u_last_post_id, $users_opt, $cat_collapse_status, $ignore_list, $buddy_list;
}

function make_alias($text)
{
	if (strlen($text) > $GLOBALS['MAX_LOGIN_SHOW']) {
		$text = substr($text, 0, $GLOBALS['MAX_LOGIN_SHOW']);
	}
	return char_fix(htmlspecialchars($text));
}

class fud_user_reg extends fud_user
{
	function html_fields()
	{
		foreach(array('name', 'location', 'occupation', 'interests', 'bio') as $v) {
			if ($this->{$v}) {
				$this->{$v} = char_fix(htmlspecialchars($this->$v));
			}
		}
	}

	function add_user()
	{
		if (isset($_COOKIES['frm_referer_id']) && (int)$_COOKIES['frm_referer_id']) {
			$ref_id = (int)$_COOKIES['frm_referer_id'];
		} else {
			$ref_id = 0;
		}

		$md5pass = md5($this->plaintext_passwd);
		$o2 =& $GLOBALS['FUD_OPT_2'];

		$this->alias = make_alias((!($o2 & 128) || !$this->alias) ? $this->login : $this->alias);

		/* this used when utilities create users (aka nntp/mlist import) */
		if ($this->users_opt == -1) {
			$this->users_opt = 4|16|32|128|256|512|2048|4096|8192|16384|131072|4194304;
			$this->theme = q_singleval("SELECT id FROM fud26_themes WHERE theme_opt>=2 AND (theme_opt & 2) > 0 LIMIT 1");
			$this->time_zone =& $GLOBALS['SERVER_TZ'];
			$this->posts_ppg =& $GLOBALS['POSTS_PER_PAGE'];
			if (!($o2 & 4)) {
				$this->users_opt ^= 128;
			}
			if (!($o2 & 8)) {
				$this->users_opt ^= 256;
			}
			if ($o2 & 1) {
				$o2 ^= 1;
			}
			$reg_ip = "127.0.0.1";
		} else {
			$reg_ip = get_ip();
		}

		if (empty($this->join_date)) {
			$this->join_date = __request_timestamp__;
		}

		if ($o2 & 1) {
			$this->conf_key = md5(implode('', (array)$this) . __request_timestamp__ . getmypid());
		} else {
			$this->conf_key = '';
			$this->users_opt |= 131072;
		}
		$this->icq = (int)$this->icq ? (int)$this->icq : 'NULL';

		$this->html_fields();

		$this->id = db_qid("INSERT INTO
			fud26_users (
				login,
				alias,
				passwd,
				name,
				email,
				icq,
				aim,
				yahoo,
				msnm,
				jabber,
				affero,
				posts_ppg,
				time_zone,
				bday,
				last_visit,
				conf_key,
				user_image,
				join_date,
				location,
				theme,
				occupation,
				interests,
				referer_id,
				last_read,
				sig,
				home_page,
				bio,
				users_opt,
				reg_ip
			) VALUES (
				'".addslashes($this->login)."',
				'".addslashes($this->alias)."',
				'".$md5pass."',
				'".addslashes($this->name)."',
				'".addslashes($this->email)."',
				".$this->icq.",
				".ssn(urlencode($this->aim)).",
				".ssn(urlencode($this->yahoo)).",
				".ssn(urlencode($this->msnm)).",
				".ssn(htmlspecialchars($this->jabber)).",
				".ssn(urlencode($this->affero)).",
				".(int)$this->posts_ppg.",
				'".addslashes($this->time_zone)."',
				".(int)$this->bday.",
				".__request_timestamp__.",
				'".$this->conf_key."',
				".ssn(htmlspecialchars($this->user_image)).",
				".$this->join_date.",
				".ssn($this->location).",
				".(int)$this->theme.",
				".ssn($this->occupation).",
				".ssn($this->interests).",
				".(int)$ref_id.",
				".__request_timestamp__.",
				".ssn($this->sig).",
				".ssn(htmlspecialchars($this->home_page)).",
				".ssn($this->bio).",
				".$this->users_opt.",
				".ip2long($reg_ip)."
			)
		");

		return $this->id;
	}

	function sync_user()
	{
		$passwd = !empty($this->plaintext_passwd) ? "passwd='".md5($this->plaintext_passwd)."'," : '';

		$this->alias = make_alias((!($GLOBALS['FUD_OPT_2'] & 128) || !$this->alias) ? $this->login : $this->alias);
		$this->icq = (int)$this->icq ? (int)$this->icq : 'NULL';

		$rb_mod_list = (!($this->users_opt & 524288) && ($is_mod = q_singleval("SELECT id FROM fud26_mod WHERE user_id={$this->id}")) && (q_singleval("SELECT alias FROM fud26_users WHERE id={$this->id}") == $this->alias));

		$this->html_fields();

		q("UPDATE fud26_users SET ".$passwd."
			name='".addslashes($this->name)."',
			alias='".addslashes($this->alias)."',
			email='".addslashes($this->email)."',
			icq=".$this->icq.",
			aim=".ssn(urlencode($this->aim)).",
			yahoo=".ssn(urlencode($this->yahoo)).",
			msnm=".ssn(urlencode($this->msnm)).",
			jabber=".ssn(htmlspecialchars($this->jabber)).",
			affero=".ssn(urlencode($this->affero)).",
			posts_ppg='".(int)$this->posts_ppg."',
			time_zone='".addslashes($this->time_zone)."',
			bday=".(int)$this->bday.",
			user_image=".ssn(htmlspecialchars($this->user_image)).",
			location=".ssn($this->location).",
			occupation=".ssn($this->occupation).",
			interests=".ssn($this->interests).",
			avatar=".(int)$this->avatar.",
			theme=".(int)$this->theme.",
			avatar_loc=".ssn($this->avatar_loc).",
			sig=".ssn($this->sig).",
			home_page=".ssn(htmlspecialchars($this->home_page)).",
			bio=".ssn($this->bio).",
			users_opt=".$this->users_opt."
		WHERE id=".$this->id);

		if ($rb_mod_list) {
			rebuildmodlist();
		}
	}
}

function get_id_by_email($email)
{
	return q_singleval("SELECT id FROM fud26_users WHERE email='".addslashes($email)."'");
}

function get_id_by_login($login)
{
	return q_singleval("SELECT id FROM fud26_users WHERE login='".addslashes($login)."'");
}

function usr_email_unconfirm($id)
{
	$conf_key = md5(__request_timestamp__ . $id . get_random_value());
	q("UPDATE fud26_users SET users_opt=users_opt & ~ 131072, conf_key='".$conf_key."' WHERE id=".$id);
	return $conf_key;
}

function &usr_reg_get_full($id)
{
	if (($r = db_sab('SELECT * FROM fud26_users WHERE id='.$id))) {
		if (!extension_loaded("overload")) {
			$o = new fud_user_reg;
			foreach ($r as $k => $v) {
				$o->{$k} = $v;
			}
			$r = $o;
		} else {
			aggregate_methods($r, 'fud_user_reg');
		}
	}
	return $r;
}

function user_login($id, $cur_ses_id, $use_cookies)
{
	if (!$use_cookies && isset($_COOKIE[$GLOBALS['COOKIE_NAME']])) {
		/* remove cookie so it does not confuse us */
		setcookie($GLOBALS['COOKIE_NAME'], '', __request_timestamp__-100000, $GLOBALS['COOKIE_PATH'], $GLOBALS['COOKIE_DOMAIN']);
	}
	if ($GLOBALS['FUD_OPT_2'] & 256 && ($s = db_saq('SELECT ses_id, sys_id FROM fud26_ses WHERE user_id='.$id))) {
		if ($use_cookies) {
			setcookie($GLOBALS['COOKIE_NAME'], $s[0], __request_timestamp__+$GLOBALS['COOKIE_TIMEOUT'], $GLOBALS['COOKIE_PATH'], $GLOBALS['COOKIE_DOMAIN']);
		}
		if ($s[1]) {
			q("UPDATE fud26_ses SET sys_id='' WHERE ses_id='".$s[0]."'");
		}
		return $s[0];
	}

	/* if we can only have 1 login per account, 'remove' all other logins */
	q("DELETE FROM fud26_ses WHERE user_id=".$id." AND ses_id!='".$cur_ses_id."'");
	q("UPDATE fud26_ses SET user_id=".$id.", sys_id='".ses_make_sysid()."' WHERE ses_id='".$cur_ses_id."'");
	$GLOBALS['new_sq'] = regen_sq();
	q("UPDATE fud26_users SET sq='".$GLOBALS['new_sq']."' WHERE id=".$id);

	return $cur_ses_id;
}

function rebuildmodlist()
{
	$tbl =& $GLOBALS['DBHOST_TBL_PREFIX'];
	$lmt =& $GLOBALS['SHOW_N_MODS'];
	$c = uq('SELECT u.id, u.alias, f.id FROM '.$tbl.'mod mm INNER JOIN '.$tbl.'users u ON mm.user_id=u.id INNER JOIN '.$tbl.'forum f ON f.id=mm.forum_id ORDER BY f.id,u.alias');
	$u = $ar = array();
	
	while ($r = db_rowarr($c)) {
		$u[] = $r[0];
		if ($lmt < 1 || (isset($ar[$r[2]]) && count($ar[$r[2]]) >= $lmt)) {
			continue;
		}
		$ar[$r[2]][$r[0]] = $r[1];
	}

	q('UPDATE '.$tbl.'forum SET moderators=NULL');
	foreach ($ar as $k => $v) {
		q('UPDATE '.$tbl.'forum SET moderators='.strnull(addslashes(serialize($v))).' WHERE id='.$k);
	}
	q('UPDATE '.$tbl.'users SET users_opt=users_opt & ~ 524288 WHERE users_opt>=524288 AND (users_opt & 524288) > 0');
	if ($u) {
		q('UPDATE '.$tbl.'users SET users_opt=users_opt|524288 WHERE id IN('.implode(',', $u).') AND (users_opt & 1048576)=0');
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
}

	if (_uid) {
		if ($FUD_OPT_2 & 32768) {
			header('Location: http://timeweather.net/forum/index.php/i/' . _rsidl);
		} else {
			header('Location: http://timeweather.net/forum/index.php?t=index&' . _rsidl);
		}
		exit;
	}

	if (isset($_GET['reset_key'])) {
		if (($ui = db_saq("SELECT email, login, id FROM fud26_users WHERE reset_key='".addslashes($_GET['reset_key'])."'"))) {
			q("UPDATE fud26_users SET passwd='".md5(($passwd = dechex(get_random_value(32))))."', reset_key='0' WHERE id=".$ui[2]);
			send_email($NOTIFY_FROM, $ui[0], 'Reset Password', 'Hello,\n\nAs requested, your login information appears below:\n\nLogin: '.$ui[1].'\nPassword: '.$passwd.'\n\nPlease note that your password has been reset to the value above. If you wish\nto change your password you may do so via the user info control panel at:\nhttp://timeweather.net/forum/index.php?t=register\n\n\n\nIf you received this message in error, please ignore it. If you are receiving multiple copies of this e-mail, which you have not requested, please contact the forum administrator at '.$GLOBALS['ADMIN_EMAIL'].'\n\nThis request was initiated from: '.$_SERVER['REMOTE_ADDR'].'.\n\n');
			ses_putvar((int)$usr->sid, 'Your password has been e-mailed to you. You should receive it within the next few minutes.<br>');
			if ($FUD_OPT_2 & 32768) {
				header('Location: http://timeweather.net/forum/index.php/l/'._rsidl);
			} else {
				header('Location: http://timeweather.net/forum/index.php?t=login&'._rsidl);
			}
			exit;
		}
		error_dialog('ERROR', 'Invalid password reset key');
	}

	if (isset($_GET['email'])) {
		$email = $_GET['email'];
	} else if (isset($_POST['email'])) {
		$email = $_POST['email'];
	} else {
		$email = '';
	}

	if ($email) {
		if ($uobj = db_sab("SELECT id, users_opt FROM fud26_users WHERE email='".addslashes($email)."'")) {
			if ($FUD_OPT_2 & 1 && !($uobj->users_opt & 131072)) {
				$uent->conf_key= usr_email_unconfirm($uobj->id);
				send_email($NOTIFY_FROM, $email, 'Registration Confirmation', 'Thank you for registering,\nTo activate your account please go to the URL below:\n\nhttp://timeweather.net/forum/index.php?t=emailconf&conf_key='.$uent->conf_key.'\n\nOnce your account is activated you will be logged-into the forum and\nredirected to the main page.\n\nIf you received this message in error, please ignore it. If you are receiving multiple copies of this e-mail, which you have not requested, please contact the forum administrator at '.$GLOBALS['ADMIN_EMAIL'].'\n\nThis request was initiated from: '.$_SERVER['REMOTE_ADDR'].'.\n\n');
			} else {
				q("UPDATE fud26_users SET reset_key='".($key = md5(__request_timestamp__ . $uobj->id . get_random_value()))."' WHERE id=".$uobj->id);
				$url = 'http://timeweather.net/forum/index.php?t=reset&reset_key='.$key;
				send_email($NOTIFY_FROM, $email, 'Reset Password', 'Hello,\n\nYou have requested for your password to be reset. To complete the process,\nplease go to this URL:\n\n'.$url.'\n\nNOTE: This forum stores the passwords in a one-way encryption mechanism, which means that\nonce you have entered your password it is encoded so that there is NO WAY to get it back.\nThis works by comparing the encoded version we have on record with the encoded version of what you type into the Login prompt.\n(If you are interested in how this mechanism works, read up on MD5 HASH algorithm)\n\nIf you received this message in error, please ignore it. If you are receiving multiple copies of this e-mail, which you have not requested, please contact the forum administrator at '.$GLOBALS['ADMIN_EMAIL'].'\n\nThis request was initiated from: '.$_SERVER['REMOTE_ADDR'].'.\n\n');
			}
			error_dialog('Information', 'You should receive instructions in your e-mail in the next few minutes.');
		} else {
			$no_such_email = '<span class="ErrorText">E-mail address not found in database</span><br />';
		}
	} else {
		$no_such_email = '';
	}

	$TITLE_EXTRA = ': Reset Password';

if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
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
<div class="ctb"><form method="post" action="index.php?t=reset">
<table cellspacing="1" cellpadding="2" class="DialogTable">
<tr><th colspan=2>Password Reminder</th></tr>
<tr><td colspan=2 class="RowStyleA GenText">Your password will be reset and sent to you. If you have not yet confirmed your e-mail address, the confirmation request will be re-sent to you.</td></tr>
<tr class="RowStyleB"><td class="GenText">E-mail:</td><td><?php echo $no_such_email; ?><input type="text" name="email" value="<?php echo $email; ?>"></td></tr>
<tr class="RowStyleA"><td class="nw ar" colspan=2><input type="submit" class="button" name="reset_passwd" value="Reset Password"></td></tr>
</table><?php echo _hs; ?></form></div>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>