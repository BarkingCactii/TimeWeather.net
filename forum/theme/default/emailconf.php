<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: emailconf.php.t,v 1.22 2004/11/24 19:53:34 hackie Exp $
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


	if (empty($_GET['conf_key'])) {
		error_dialog('Error', 'Invalid confirmation key.  Please check your e-mail to make sure that you have entered the URL correctly.');
	}

	/* it is possible that a user may access the email confirmation URL twice, for such a 'rare' case,
	 * we have this check to prevent a confusing error message being thrown at the hapeless user
	 */
	if (_uid && $usr->users_opt & 131072) {
		check_return($usr->returnto);
	}

	$uid = q_singleval("SELECT id FROM fud26_users WHERE conf_key='".addslashes($_GET['conf_key'])."'");
	if (!$uid || (__fud_real_user__ && __fud_real_user__ != $uid)) {
		error_dialog('Error', 'Invalid confirmation key.  Please check your e-mail to make sure that you have entered the URL correctly.');
	}
	q("UPDATE fud26_users SET users_opt=users_opt|131072, conf_key='0' WHERE id=".$uid);
	if (!__fud_real_user__) {
		$usr->ses_id = user_login($uid, $usr->ses_id, true);
		$usr->users_opt = (int) q_singleval("SELECT users_opt FROM fud26_users WHERE id=".$uid);
	}
	if ($usr->users_opt & 2097152) {
		header('Location: http://timeweather.net/forum/index.php' . ($FUD_OPT_2 & 32768 ? '/rc/' : '?t=reg_conf&') . _rsidl);
		return;
	}
	check_return($usr->returnto);
?>