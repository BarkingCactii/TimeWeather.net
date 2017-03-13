<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: login.php.t,v 1.72 2005/03/20 15:22:43 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}function logaction($user_id, $res, $res_id=0, $action=null)
{
	q('INSERT INTO fud26_action_log (logtime, logaction, user_id, a_res, a_res_id)
		VALUES('.__request_timestamp__.', '.strnull($action).', '.$user_id.', '.strnull($res).', '.(int)$res_id.')');
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

	/* Remove old unconfirmed users */
	if ($FUD_OPT_2 & 1) {
		$account_expiry_date = __request_timestamp__ - (86400 * $UNCONF_USER_EXPIRY);
		$c = uq("SELECT id FROM fud26_users WHERE (users_opt & 131072)=0 AND join_date<".$account_expiry_date." AND posted_msg_count=0 AND last_visit<".$account_expiry_date." AND id!=1 AND (users_opt & 1048576)=0");
		$list = array();
		while ($r = db_rowarr($c)) {
			$list[] = $r[0];
		}
		if ($list) {
			fud_use('private.inc');
			fud_use('users_adm.inc', true);
			usr_delete($list);
		}
		unset($c, $list);
	}

	if (!empty($_GET['logout']) && sq_check(0, $usr->sq)) {
		if ($usr->returnto) {
			parse_str($usr->returnto, $tmp);
			$page = isset($tmp['t']) ? $tmp['t'] : '';
		} else {
			$page = '';
		}

		switch ($page) {
			case 'register':
			case 'pmsg_view':
			case 'pmsg':
			case 'subscribed':
			case 'referals':
			case 'buddy_list':
			case 'ignore_list':
			case 'modque':
			case 'mvthread':
			case 'groupmgr':
			case 'post':
			case 'ppost':
			case 'finduser':
			case 'error':
			case '':
				$returnto = '';
				break;
			default:
				if ($page == 'msg' || $page == 'tree') {
					if (empty($tmp['th'])) {
						if (empty($tmp['goto']) || !q_singleval("SELECT t.forum_id
								FROM fud26_msg m
								INNER JOIN fud26_thread t ON m.thread_id=t.id
								INNER JOIN fud26_group_cache g ON g.user_id=0 AND g.resource_id=t.forum_id AND (g.group_cache_opt & 2) > 0
								WHERE m.id=".(int)$tmp['goto'])) {
							$returnto = '';
							break;
						}
					} else {
						if (!q_singleval("SELECT t.forum_id
								FROM fud26_thread t
								INNER JOIN fud26_group_cache g ON g.user_id=0 AND g.resource_id=t.forum_id AND (g.group_cache_opt & 2) > 0
								WHERE t.id=".(int)$tmp['th'])) {
							$returnto = '';
							break;
						}
					}
				} else if ($page == 'thread' || $page == 'threadt') {
					if (!q_singleval("SELECT id FROM fud26_group_cache WHERE user_id=0 AND resource_id=".(isset($tmp['frm_id']) ? (int) $tmp['frm_id'] : 0)." AND (group_cache_opt & 2) > 0")) {
						$returnto = '';
						break;
					}
				}

				if (isset($tmp['S'])) {
					$returnto = str_replace('S='.$tmp['S'], '', $usr->returnto);
				} else {
					$returnto = $usr->returnto;
				}
				break;
		}

		ses_delete($usr->sid);
		if ($FUD_OPT_2 & 32768 && $returnto && $returnto[0] == '/') {
			header('Location: http://timeweather.net/forum/index.php'. $returnto);
		} else {
			header('Location: http://timeweather.net/forum/index.php?'. str_replace(array('?', '&&'), array('&', '&'), $returnto));
		}
		exit;
	}

	if (_uid) { /* send logged in users to profile page if they are not logging out */
		if ($FUD_OPT_2 & 32768) {
			header('Location: http://timeweather.net/forum/index.php/re/'._rsidl);
		} else {
			header('Location: http://timeweather.net/forum/index.php?t=register&'._rsidl);
		}
		exit;
	}

function login_php_set_err($type, $val)
{
	$GLOBALS['_ERROR_'] = 1;
	$GLOBALS['_ERROR_MSG_'][$type] = $val;
}

function login_php_get_err($type)
{
	if (empty($GLOBALS['_ERROR_MSG_'][$type])) {
		return;
	}
	return '<span class="ErrorText">'.$GLOBALS['_ERROR_MSG_'][$type].'</span><br />';
}

function error_check()
{
	$_POST['login'] = trim($_POST['login']);
	$_POST['password'] = trim($_POST['password']);

	if (!strlen($_POST['login'])) {
		login_php_set_err('login', 'Login name is required');
	}

	if (!strlen($_POST['password'])) {
		login_php_set_err('password', 'Password is required');
	}

	return $GLOBALS['_ERROR_'];
}

	$_ERROR_ = 0;
	$_ERROR_MSG_ = array();

	/* deal with quicklogin from if needed */
	if (isset($_POST['quick_login']) && isset($_POST['quick_password'])) {
		$_POST['login'] = $_POST['quick_login'];
		$_POST['password'] = $_POST['quick_password'];
		$_POST['use_cookie'] = isset($_POST['quick_use_cookies']);
	}

	if (isset($_POST['login']) && !error_check()) {
		if ($usr->data) {
			ses_putvar((int)$usr->sid, null);
		}

		if (!($usr_d = db_sab("SELECT id, passwd, login, email, users_opt FROM fud26_users WHERE login='".addslashes($_POST['login'])."'"))) {
			login_php_set_err('login', 'Invalid login/password combination');
		} else if ($usr_d->passwd != md5($_POST['password'])) {
			logaction($usr_d->id, 'WRONGPASSWD', 0, ($usr_d->users_opt & 1048576 ? 'ADMIN: ' : '')."Invalid Password \'".htmlspecialchars(addslashes($_POST['password']))."\' for login \'".htmlspecialchars(addslashes($_POST['login']))."\'. IP: ".get_ip());
			login_php_set_err('login', 'Invalid login/password combination');
		} else { /* Perform check to ensure that the user is allowed to login */
			$usr_d->users_opt = (int) $usr_d->users_opt;

			/* Login & E-mail Filter & IP */
			if (is_login_blocked($usr_d->login) || is_email_blocked($usr_d->email) || $usr_d->users_opt & 65536 || is_ip_blocked(get_ip())) {
				setcookie($COOKIE_NAME.'1', 'd34db33fd34db33fd34db33fd34db33f', __request_timestamp__+63072000, $COOKIE_PATH, $COOKIE_DOMAIN);
				error_dialog('Your account has been banned.', 'Your account has been banned from this forum. If do not already know the reason behind this action contact the forum&#39;s administrator(s).');
			}

			$ses_id = user_login($usr_d->id, $usr->ses_id, ((empty($_POST['use_cookie']) && $FUD_OPT_1 & 128) ? false : true));

			if (!($usr_d->users_opt & 131072)) {
				error_dialog('ERROR: Your account is not yet confirmed', 'You have not confirmed your account via e-mail yet<br><table border=0><tr><td><ol><li>If you have not received a confirmation e-mail, <a href="index.php?t=reset&amp;email='.$usr_d->email.'&amp;S='.$ses_id.'">click here</a><li>If '.$usr_d->email.' is not your correct e-mail address, <a href="index.php?t=register&amp;S='.$ses_id.'">click here</a></ol></td></tr></table>', null, $ses_id);
			}
			if ($usr_d->users_opt & 2097152) {
				error_dialog('Unapproved Account', 'The administrator of the forum has chosen to confirm each new account manually before activation. Your account has not yet been confirmed, therefore you will not be able to access some of the features available to confirmed members.', null, $ses_id);
			}

			if (!empty($_POST['adm']) && $usr_d->users_opt & 1048576) {
				header('Location: http://timeweather.net/forum/adm/admglobal.php?S='.$ses_id.'&SQ='.$new_sq);
				exit;
			}

			if (!$usr->returnto) { /* nothing to do, send to front page */
				check_return('');
			}

			if (s && ($sesp = strpos($usr->returnto, s)) !== false) { /* replace old session with new session */
				$usr->returnto = str_replace(s, $ses_id, $usr->returnto);
			}

			if ($usr->returnto{0} != '/') { /* no GET vars or no PATH_INFO */
				$ret =& $usr->returnto;
				parse_str($ret, $args);
				$args['SQ'] = $new_sq;

				if ($FUD_OPT_1 & 128) { /* if URL sessions are supported */
					$args['S'] = $ses_id;
				}

				$ret = '';
				foreach ($args as $k => $v) {
					$ret .= $k.'='.$v.'&';
				}
			} else { /* PATH_INFO url or GET url with no args */
				if ($FUD_OPT_1 & 128 && $FUD_OPT_2 & 32768 && !$sesp) {
					if (preg_match('!([a-z0-9]{32})!', $usr->returnto, $m)) {
						$usr->returnto = str_replace($m[1], $ses_id, $usr->returnto);
					}
				}
				$usr->returnto .= '?SQ='.$new_sq.'&S='.$ses_id;
			}

			check_return($usr->returnto);
		}
	}

	ses_update_status($usr->sid, 'Login Form', 0, 0);
	$TITLE_EXTRA = ': Login Form';

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
<form name="login" method="post" action="index.php?t=login"<?php echo ($FUD_OPT_3 & 256 ? ' autocomplete="off"' : '').'>
<div class="ctb">
<table cellspacing="1" cellpadding="2" class="DialogTable">
<tr><th colspan=3>Login Form</th></tr>
<tr><td class="RowStyleA" colspan=3><span class="GenText">'.((!empty($usr->data) && is_string($usr->data)) ? $usr->data : '').' You are not logged in. This could be due to one of several reasons:</span>
<ol>
<li><span class="GenText">Your cookie has expired, and you need to login to renew your cookie.</span>
<li><span class="GenText">You do not have permission to access the requested resource as an anonymous user. You must login to gain permission.</span>
</ol></td></tr>
<tr class="RowStyleB"><td class="GenText">Login</td><td>'.login_php_get_err('login').'<input type="text" tabindex="1" name="login"></td><td nowrap><a href="index.php?t=register&amp;'._rsid.'">Want to register?</a></td></tr>
<tr class="RowStyleA"><td class="GenText">Password:</td><td>'.login_php_get_err('password').'<input type="password" tabindex="2" name="password"></td><td nowrap><a href="index.php?t=reset&amp;'._rsid.'">Forgot password</a></td></tr>
'.($FUD_OPT_1 & 128 ? '<tr class="RowStyleB"><td colspan=3 class="al"><input type="checkbox" name="use_cookie" value="Y" checked> Use cookies<br><span class="SmallText">If you&#39;re using a public terminal such as a computer in a library, school, or Internet cafe, it is recommended that you uncheck this option for greater security.<br>If you leave this option selected then you will be automatically logged-into the forum the next time you visit.</span></td></tr>' : ''); ?>
<tr><td colspan=3 class="RowStyleA ar"><input type="submit" class="button" tabindex="3" value="Login"></td></tr>
</table></div><?php echo _hs; ?><input type="hidden" name="adm" value="<?php echo (isset($_GET['adm']) ? '1' : ''); ?>"></form>
<script>
<!--
document.login.login.focus();
//-->
</script>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>
<?php
	/* clear old sessions */
	q('DELETE FROM fud26_ses WHERE time_sec<'.(__request_timestamp__- ($FUD_OPT_3 & 1 ? $SESSION_TIMEOUT : $COOKIE_TIMEOUT)));
?>