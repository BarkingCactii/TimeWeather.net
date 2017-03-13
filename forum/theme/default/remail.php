<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: remail.php.t,v 1.28 2005/02/27 02:35:51 hackie Exp $
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
}$GLOBALS['__error__'] = 0;
$GLOBALS['__err_msg__'] = array();

function set_err($err, $msg)
{
	$GLOBALS['__err_msg__'][$err] = $msg;
	$GLOBALS['__error__'] = 1;
}

function is_post_error()
{
	return $GLOBALS['__error__'];
}

function get_err($err, $br=0)
{
	if (isset($err, $GLOBALS['__err_msg__'][$err])) {
		return ($br ? '<span class="ErrorText">'.$GLOBALS['__err_msg__'][$err].'</span><br />' : '<br /><span class="ErrorText">'.$GLOBALS['__err_msg__'][$err].'</span>');
	}
}

function post_check_images()
{
	if ($GLOBALS['MAX_IMAGE_COUNT'] && $GLOBALS['MAX_IMAGE_COUNT'] < count_images($_POST['msg_body'])) {
		return -1;
	}

	return 0;
}

function check_post_form()
{
	/* make sure we got a valid subject */
	if (!strlen(trim($_POST['msg_subject']))) {
		set_err('msg_subject', 'Subject required');
	}

	/* make sure the number of images [img] inside the body do not exceed the allowed limit */
	if (post_check_images()) {
		set_err('msg_body', 'No more than '.$GLOBALS['MAX_IMAGE_COUNT'].' images are allowed per message. Please reduce the number of images.');
	}

	if (defined('fud_bad_sq')) {
		unset($_POST['submitted']);
		set_err('msg_session', '<h4 align="center" class="ErrorText">Your session has expired. Please re-submit the form. Sorry for the inconvenience.</h4>');
	}

	return $GLOBALS['__error__'];
}

function check_ppost_form($msg_subject)
{
	if (!strlen(trim($msg_subject))) {
		set_err('msg_subject', 'Subject required');
	}

	if (post_check_images()) {
		set_err('msg_body', 'No more than '.$GLOBALS['MAX_IMAGE_COUNT'].' images are allowed per message. Please reduce the number of images.');
	}
	$GLOBALS['recv_user_id'] = array();
	/* hack for login names containing HTML entities ex. &#123; */
	if (($hack = strpos($_POST['msg_to_list'], '&#')) !== false) {
		$hack_str = preg_replace('!&#([0-9]+);!', '&#\1#', $_POST['msg_to_list']);
	} else {
		$hack_str = $_POST['msg_to_list'];
	}
	foreach(explode(';', $hack_str) as $v) {
		$v = trim($v);
		if (strlen($v)) {
			if ($hack !== false) {
				$v = preg_replace('!&#([0-9]+)#!', '&#\1;', $v);
			}
			if (!($obj = db_sab('SELECT u.users_opt, u.id, ui.ignore_id FROM fud26_users u LEFT JOIN fud26_user_ignore ui ON ui.user_id=u.id AND ui.ignore_id='._uid.' WHERE u.alias='.strnull(addslashes(char_fix(htmlspecialchars($v))))))) {
				set_err('msg_to_list', 'There is no user named "'.char_fix(htmlspecialchars($v)).'" in this forum.');
				break;
			}
			if (!empty($obj->ignore_id)) {
				set_err('msg_to_list', 'You cannot send a private message to "'.char_fix(htmlspecialchars($v)).'", because this person is ignoring you.');
				break;
			} else if (!($obj->users_opt & 32) && !$GLOBALS['is_a']) {
				set_err('msg_to_list', 'You cannot send a private message to "'.htmlspecialchars($v).'", because this person is not accepting private messages.');
				break;
			} else {
				$GLOBALS['recv_user_id'][] = $obj->id;
			}
		}
	}

	if (empty($_POST['msg_to_list'])) {
		set_err('msg_to_list', 'Cannot send a message, missing recipient');
	}

	if (defined('fud_bad_sq')) {
		unset($_POST['btn_action']);
		set_err('msg_session', '<h4 align="center" class="ErrorText">Your session has expired. Please re-submit the form. Sorry for the inconvenience.</h4>');
	}

	return $GLOBALS['__error__'];
}

function check_femail_form()
{
	if (empty($_POST['femail']) || validate_email($_POST['femail'])) {
		set_err('femail', 'Please enter a valid e-mail address for your friend.');
	}
	if (empty($_POST['subj'])) {
		set_err('subj', 'You cannot send an e-mail without a subject.');
	}
	if (empty($_POST['body'])) {
		set_err('body', 'You cannot send an e-mail without the message body.');
	}
	if (defined('fud_bad_sq')) {
		unset($_POST['posted']);
		set_err('msg_session', '<h4 align="center" class="ErrorText">Your session has expired. Please re-submit the form. Sorry for the inconvenience.</h4>');
	}

	return $GLOBALS['__error__'];
}

function count_images($text)
{
	$text = strtolower($text);
	$a = substr_count($text, '[img]');
	$b = substr_count($text, '[/img]');

	return (($a > $b) ? $b : $a);
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

	if (isset($_POST['done'])) {
		check_return($usr->returnto);
	}

	if (__fud_real_user__) {
		is_allowed_user($usr);
	} else if (is_ip_blocked(get_ip())) {
		invl_inp_err();
	}

	if ((isset($_GET['th']) && ($th = (int)$_GET['th'])) || (isset($_POST['th']) && ($th = (int)$_POST['th']))) {
		$data = db_sab('SELECT m.subject, t.id, mm.id AS md, (CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) AS gco
				FROM fud26_thread t
				INNER JOIN fud26_msg m ON t.root_msg_id=m.id
				LEFT JOIN fud26_mod mm ON mm.forum_id=t.forum_id AND mm.user_id='._uid.'
				INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=t.forum_id
				LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=t.forum_id
				WHERE t.id='.$th);
		if (!$data) {
			invl_inp_err();
		}
	} else {
		invl_inp_err();
	}

	if (!$is_a && !$data->md && !($data->gco & 2)) {
		std_error('access');
	}

if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
	}

	if (isset($_POST['posted']) && _uid && !check_femail_form()) {
		$to = empty($POST['fname']) ? $_POST['femail'] : $_POST['fname'].' <'.$_POST['femail'].'>';
		$from = $usr->alias. '<'.$usr->email.'>';
		send_email($from, $to, $_POST['subj'], $_POST['body']);

		error_dialog('E-mail was sent', 'The e-mail to your friend at '.htmlspecialchars($_POST['femail']).' about the '.$data->subject.' topic was successfully sent.');
	} else if (!isset($_POST['posted'])) {
		$def_thread_view = $FUD_OPT_2 & 4 ? 'msg' : 'tree';
	}

	$form_data = _uid ? '<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText nw">Your Name:</td><td width="100%">'.$usr->alias.'</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText nw">Your E-mail:</td><td width="100%">'.$usr->email.'</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText nw">Friend&#39;s Name:</td><td width="100%"><input type="text" name="fname" value="'.(isset($_POST['fname']) ? htmlspecialchars($_POST['fname']).'' : '' )  .'"></td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText nw vt SmallText">Friend&#39;s E-mail:<br /><i>required</i></td><td valign="top"><input type="text" name="femail" value="'.(isset($_POST['femail']) ? htmlspecialchars($_POST['femail']).'' : '' )  .'">'.get_err('femail').'</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText nw vt SmallText">Subject:<br /><i>required</i></td><td nowrap valign="top"><input type="text" name="subj" value="'.(isset($_POST['subject']) ? htmlspecialchars($_POST['subject']).'' : $data->subject.'' )  .'">'.get_err('subj').'</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText nw vt">Message:<span class="SmallText"><br /><i>required</i></span></td><td valign="top" nowrap><textarea name="body" rows="19" cols="78" wrap="PHYSICAL">'.(isset($_POST['body']) ? htmlspecialchars($_POST['body']).'' : 'Hello,\n\nThere is an interesting topic about "'.$data->subject.'" on '.$GLOBALS['FORUM_TITLE'].' forum that you may to want read. You can see the topic at:\n http://timeweather.net/forum/index.php?t=rview&amp;th='.$data->id.'&amp;rid='._uid.'\n\nYour friend,\n\n'.$usr->alias.'\n' ) .'</textarea>'.get_err('body').'</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText ar" colspan=2><input type="submit" class="button" name="submit" value="Send E-mail Now"></td></tr>' : '<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText ac SmallText">Copy this message into a mail client of your choice to send it to your friend(s).</td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText"><textarea name="body" rows="19" cols="78">'.(isset($_POST['body']) ? htmlspecialchars($_POST['body']).'' : 'Hello,\n\nThere is an interesting topic about "'.$data->subject.'" on '.$GLOBALS['FORUM_TITLE'].' forum that you may to want read. You can see the topic at:\n http://timeweather.net/forum/index.php?t=rview&amp;th='.$data->id.'&amp;rid='._uid.'\n\nYour friend,\n\n'.$usr->alias.'\n' ) .'</textarea></td></tr>
<tr class="'.alt_var('page_alt','RowStyleA','RowStyleB').'"><td class="GenText ar"><input type="submit" class="button" name="done" value="Done"></td></tr>';


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
<div class="ctb">
<?php echo (is_post_error() ? '<h4 class="ac ErrorText">There was an error</h4>' : ''); ?>
<form action="index.php?t=remail" name="remail" method="post"><input type="hidden" name="posted" value="1">
<table cellspacing="1" cellpadding="2" class="MiniTable">
<tr><th colspan=2>E-mail This Topic to a Friend</th></tr>
<?php echo str_replace('\n', "\n", $form_data); ?>
</table>
<?php echo _hs; ?><input type="hidden" name="th" value="<?php echo $th; ?>"></form>
</div>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>