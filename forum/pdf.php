<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: pdf.php.t,v 1.37 2005/03/12 18:08:11 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

	require('./GLOBALS.php');
	fud_use('err.inc');
	fud_use('fpdf.inc', true);

class fud_pdf extends FPDF
{
	function begin_page($title)
	{
		$this->AddPage();
		$this->Bookmark($title);
	}

	function input_text($text)
	{
		$this->SetFont('helvetica','',12);
		$this->Write(5, $text);
		$this->Ln(5);
	}

	function draw_line()
	{
		$this->Line($this->lMargin, $this->y, ($this->w - $this->rMargin), $this->y);
	}

	function add_link($url, $caption=0)
	{
		$this->SetTextColor(0,0,255);
		$this->Write(5, $caption ? $caption : $url, $url);
		$this->SetTextColor(0);
	}

	function add_attacments($attch, $private)
	{
		$this->Ln(5);
		$this->SetFont('courier', '', 16);
		$this->Write(5, 'File Attachments');
		$this->Ln(5);
	
		$this->draw_line();

		$this->SetFont('', '', 14);

		$i = 0;
		foreach ($attch as $a) {
			$this->Write(5, ++$i . ') ');
			$this->add_link($GLOBALS['WWW_ROOT'] . 'index.php?t=getfile&id='.$a['id'] . ($private ? '&private=1' : ''), $a['name']);
			$this->Write(5, ', downloaded '.$a['nd'].' times');
			$this->Ln(5);
		}
	}

	function add_poll($name, $opts, $ttl_votes)
	{
		$this->Ln(6);
		$this->SetFont('courier', '', 16);
		$this->Write(5, $name);
		$this->SetFont('', '', 14);
		$this->Write(5, '(total votes: '.$ttl_votes.')');
		$this->Ln(6);
		$this->draw_line();
		$this->Ln(5);

		$p1 = ($this->w - $this->rMargin - $this->lMargin) * 0.6 / 100;

		// avoid /0 warnings and safe to do, since we'd be multiplying 0 since there are no votes
		if (!$ttl_votes) {
			$ttl_votes = 1;
		}

		$this->SetFont('helvetica', '', 14);

		foreach ($opts as $o) {
			$this->SetFillColor(52, 146, 40);
			$this->Cell((!$o['votes'] ? 1 : $p1 * (($o['votes'] / $ttl_votes) * 100)), 5, $o['name'] . "\t\t" . $o['votes'] . '/('.round(($o['votes'] / $ttl_votes) * 100).'%)', 1, 0, '', 1);
			$this->Ln(5);
		}
		$this->SetFillColor();
	}

	function message_header($subject, $author, $date, $id, $th)
	{
		$this->Rect($this->lMargin, $this->y, (int)($this->w - $this->lMargin - $this->lMargin), 1, 'F');
		$this->Ln(2);

		$this->SetFont('helvetica','',14);
		$this->Bookmark($subject, 1);
		$this->Write(5, 'Subject: ' . $subject);
		$this->Ln(5);
		
		$this->Write(5, 'Posted by ');
		$this->add_link($GLOBALS['WWW_ROOT'].'index.php?t=usrinfo&id='.$author[0], $author[1]);
		$this->Write(5, ' on '.gmdate('D, d M Y H:i:s \G\M\T', $date));
		if ($th) {
			$this->Ln(5);
			$this->add_link($GLOBALS['WWW_ROOT'].'index.php?t=rview&th='.$th.'&goto='.$id.'#msg_'.$id, 'View Forum Message');
			$this->Write(5, ' <> ');
			$this->add_link($GLOBALS['WWW_ROOT'].'index.php?t=post&reply_to='.$id, 'Reply to Message');
		}
		$this->Ln(5);
		$this->draw_line();
		$this->Ln(3);
	}

	function end_message()
	{
		$this->Ln(3);
		$this->Rect($this->lMargin, $this->y, (int)($this->w - $this->lMargin - $this->lMargin), 1, 'F');
		$this->Ln(10);
	}

	function header()
	{
	
	}
	
	function footer()
	{
		$this->SetFont('courier', '', 10);
		$this->Ln(10);
		$this->draw_line();
		$this->Write(5, 'Page '.$this->page.' of {fnb} ---- Generated from ');
		$this->add_link($GLOBALS['WWW_ROOT'].'index.php', $GLOBALS['FORUM_TITLE']);
		$this->Write(5, ' by FUDforum '.$GLOBALS['FORUM_VERSION']);
	}
}

	/* this potentially can be a longer form to generate */
	@set_time_limit($PDF_MAX_CPU);

	/* before we go on, we need to do some very basic activation checks */
	if (!($FUD_OPT_1 & 1)) {
		fud_use('errmsg.inc');
		exit($DISABLED_REASON . __fud_ecore_adm_login_msg);
	}
	if (!$FORUM_TITLE && @file_exists($WWW_ROOT_DISK.'install.php')) {
		fud_use('errmsg.inc');
	        exit(__fud_e_install_script_present_error);
	}

#define('fud_query_stats', 1);

function fud_sql_error_handler($query, $error_string, $error_number, $server_version)
{
	if (db_locked()) {
		if ((__dbtype__ == 'mysql' && $query != 'UNLOCK TABLES') || (__dbtype__ == 'pgsql' && $query != 'COMMIT WORK')) {
			db_unlock();
		}
	}

	$_SERVER['PATH_TRANSLATED'] = '';
	foreach (debug_backtrace() as $v) {
		$_SERVER['PATH_TRANSLATED'] .= "{$v['file']}:{$v['line']}<br />\n";
	}

	$error_msg = "(".$_SERVER['PATH_TRANSLATED'].") ".$error_number.": ".$error_string."<br />\n";
	$error_msg .= "Query: ".htmlspecialchars($query)."<br />\n";
	if (!empty($_GET)) {
		$error_msg .= "_GET: ";
		foreach ($_GET as $k => $v) {
			$error_msg .= "{$k}=".htmlspecialchars($v)."&";
		}
		$error_msg .= "<br />\n";
	}
	if (!empty($_POST)) {
		$error_msg .= "_POST: ";
		foreach ($_POST as $k => $v) {
			$error_msg .= "{$k}=".htmlspecialchars($v)."&";
		}
		$error_msg .= "<br />\n";
	}
	$error_msg .= "Server Version: ".$server_version."<br />\n";
	if (isset($_SERVER['HTTP_REFERER'])) {
		$error_msg .= "[Referring Page] ".htmlspecialchars($_SERVER['HTTP_REFERER'])."<br />\n";
	}

	if (!error_log('['.gmdate("D M j G:i:s T Y", __request_timestamp__).'] '.base64_encode($error_msg)."\n", 3, $GLOBALS['ERROR_PATH'].'sql_errors')) {
		echo "<b>UNABLE TO WRITE TO SQL LOG FILE</b><br>\n";
		echo $error_msg;
	} else {
		if (defined('forum_debug') || (isset($GLOBALS['usr']->users_opt) && $GLOBALS['usr']->users_opt & 1048576)) {
			echo $error_msg;
		} else {
			trigger_error('SQL Error has occurred, please contact the <a href="mailto:'.$GLOBALS['ADMIN_EMAIL'].'?subject=SQL%20Error">administrator</a> of the forum and have them review the forum&#39;s SQL query log', E_USER_ERROR);
			if (ini_get('display_errors') !== 1) {
				exit('SQL Error has occurred, please contact the <a href="mailto:'.$GLOBALS['ADMIN_EMAIL'].'?subject=SQL%20Error">administrator</a> of the forum and have them review the forum&#39;s SQL query log');
			}
		}
	}
	exit;
}

if (!defined('fud_sql_lnk')) {
	$connect_func = $GLOBALS['FUD_OPT_1'] & 256 ? 'mysql_pconnect' : 'mysql_connect';

	$conn = $connect_func($GLOBALS['DBHOST'], $GLOBALS['DBHOST_USER'], $GLOBALS['DBHOST_PASSWORD']) or die (fud_sql_error_handler("Initiating $connect_func", mysql_error(fud_sql_lnk), mysql_errno(fud_sql_lnk), "Unknown"));
	define('fud_sql_lnk', $conn);
	mysql_select_db($GLOBALS['DBHOST_DBNAME'], fud_sql_lnk) or die (fud_sql_error_handler("Opening database ".$GLOBALS['DBHOST_DBNAME'], mysql_error(fud_sql_lnk), mysql_errno(fud_sql_lnk), get_version()));

	define('__dbtype__', 'mysql');
	define('__FUD_SQL_CONCAT__', 'CONCAT');
}

function get_version()
{
	if (!defined('__FUD_SQL_VERSION__')) {
		define('__FUD_SQL_VERSION__', @current(mysql_fetch_row(mysql_query('SELECT VERSION()', fud_sql_lnk))));
	}
	return __FUD_SQL_VERSION__;
}


function db_lock($tables)
{
	if (!empty($GLOBALS['__DB_INC_INTERNALS__']['db_locked'])) {
		fud_sql_error_handler("Recursive Lock", "internal", "internal", get_version());
	} else {
		q('LOCK TABLES '.$tables);
		$GLOBALS['__DB_INC_INTERNALS__']['db_locked'] = 1;
	}
}

function db_unlock()
{
	if (empty($GLOBALS['__DB_INC_INTERNALS__']['db_locked'])) {
		unset($GLOBALS['__DB_INC_INTERNALS__']['db_locked']);
		fud_sql_error_handler("DB_UNLOCK: no previous lock established", "internal", "internal", get_version());
	}
	
	if (--$GLOBALS['__DB_INC_INTERNALS__']['db_locked'] < 0) {
		unset($GLOBALS['__DB_INC_INTERNALS__']['db_locked']);
		fud_sql_error_handler("DB_UNLOCK: unlock overcalled", "internal", "internal", get_version());
	}
	unset($GLOBALS['__DB_INC_INTERNALS__']['db_locked']);
	q('UNLOCK TABLES', fud_sql_lnk);
}

function db_locked()
{
	return isset($GLOBALS['__DB_INC_INTERNALS__']['db_locked']);
}

function db_affected()
{
	return mysql_affected_rows(fud_sql_lnk);	
}

if (!defined('fud_query_stats')) {
	function q($query)
	{
		$r = mysql_query($query, fud_sql_lnk) or die (fud_sql_error_handler($query, mysql_error(fud_sql_lnk), mysql_errno(fud_sql_lnk), get_version()));
		return $r;
	}
	function uq($query)
	{
		$r = mysql_unbuffered_query($query,fud_sql_lnk) or die (fud_sql_error_handler($query, mysql_error(fud_sql_lnk), mysql_errno(fud_sql_lnk), get_version()));
		return $r;
	}
} else {
	function q($query)
	{
		if (!isset($GLOBALS['__DB_INC_INTERNALS__']['query_count'])) {
			$GLOBALS['__DB_INC_INTERNALS__']['query_count'] = 1;
		} else {
			++$GLOBALS['__DB_INC_INTERNALS__']['query_count'];
		}
	
		if (!isset($GLOBALS['__DB_INC_INTERNALS__']['total_sql_time'])) {
			$GLOBALS['__DB_INC_INTERNALS__']['total_sql_time'] = 0;
		}
	
		$s = gettimeofday();
		$result = mysql_query($query, fud_sql_lnk) or die (fud_sql_error_handler($query, mysql_error(fud_sql_lnk), mysql_errno(fud_sql_lnk), get_version()));
		$e = gettimeofday(); 

		$GLOBALS['__DB_INC_INTERNALS__']['last_time'] = ($e['sec'] - $s['sec'] + (($e['usec'] - $s['usec'])/1000000));
		$GLOBALS['__DB_INC_INTERNALS__']['total_sql_time'] += $GLOBALS['__DB_INC_INTERNALS__']['last_time'];
		$GLOBALS['__DB_INC_INTERNALS__']['last_query'] = $query;

		echo '<pre>'.preg_replace('!\s+!', ' ', $query).'</pre>';
		echo '<pre>query count: '.$GLOBALS['__DB_INC_INTERNALS__']['query_count'].' time taken: '.$GLOBALS['__DB_INC_INTERNALS__']['last_time'].'</pre>';
		echo '<pre>Affected rows: '.db_affected().'</pre>';
		echo '<pre>total sql time: '.$GLOBALS['__DB_INC_INTERNALS__']['total_sql_time'].'</pre>';

		return $result; 
	}

	function uq($query)
	{
		return q($query);
	}
}

function db_count($result)
{
	return (int) mysql_num_rows($result);
}
function db_seek($result, $pos)
{
	return mysql_data_seek($result, $pos);
}
function &db_rowobj($result)
{
	return mysql_fetch_object($result);
}
function &db_rowarr($result)
{
	return mysql_fetch_row($result);
}

function q_singleval($query)
{
	if (($res = mysql_fetch_row(q($query))) !== false) {
		return $res[0];
	}
}

function qry_limit($limit, $off)
{
	return $off.','.$limit;
}

function get_fud_table_list($tbl='')
{
	if (!$tbl) {
		$r = uq("SHOW TABLES LIKE '".str_replace("_", "\\_", $GLOBALS['DBHOST_TBL_PREFIX'])."%'");
		while (list($ret[]) = db_rowarr($r));
		array_pop($ret);
	} else {
		return q_singleval("SHOW TABLES LIKE '".str_replace("_", "\\_", $tbl)."'");
	}

	return $ret;	
}

function optimize_tables($tbl_list=null)
{
	if (!$tbl_list) {
		$tbl_list = get_fud_table_list();
	}

	q('OPTIMIZE TABLE '. implode(', ', $tbl_list));
}

function &db_saq($q)
{
	return mysql_fetch_row(q($q));
}
function &db_sab($q)
{
	return mysql_fetch_object(q($q));
}
function db_qid($q)
{
	q($q);
	return mysql_insert_id(fud_sql_lnk);
}
function &db_arr_assoc($q)
{
	return mysql_fetch_array(q($q), MYSQL_ASSOC);
}

function db_li($q, &$ef, $li=0)
{
	$r = mysql_query($q, fud_sql_lnk);
	if ($r) {
		return ($li ? mysql_insert_id(fud_sql_lnk) : $r);
	}

	/* duplicate key */
	if (mysql_errno(fud_sql_lnk) == 1062) {
		$ef = ltrim(strrchr(mysql_error(fud_sql_lnk), ' '));
		return null;
	} else {
		die(fud_sql_error_handler($q, mysql_error(fud_sql_lnk), mysql_errno(fud_sql_lnk), get_version()));
	}
}

function ins_m($tbl, $flds, $vals, $type=0)
{
	if (!$type) {
		q("INSERT IGNORE INTO ".$tbl." (".$flds.") VALUES (".implode('),(', $vals).")");
	} else {
		q("INSERT INTO ".$tbl." (".$flds.") VALUES (".implode('),(', $vals).")");
	}
}function ses_make_sysid()
{
	if ($GLOBALS['FUD_OPT_2'] & 256) {
		return;
	}

	$keys = array('HTTP_USER_AGENT', 'SERVER_PROTOCOL', 'HTTP_ACCEPT_CHARSET', 'HTTP_ACCEPT_ENCODING', 'HTTP_ACCEPT_LANGUAGE');
	if ($GLOBALS['FUD_OPT_3'] & 16 && strpos($_SERVER['HTTP_USER_AGENT'], 'AOL') === false) {
		$keys[] = 'HTTP_X_FORWARDED_FOR';
		$keys[] = 'REMOTE_ADDR';
	}
	$pfx = '';
	foreach ($keys as $v) {
		if (isset($_SERVER[$v])) {
			$pfx .= $_SERVER[$v];
		}
	}
	return md5($pfx);
}

function &ses_get($id=0)
{
	if (!$id) {
		if (!empty($_COOKIE[$GLOBALS['COOKIE_NAME']])) {
			$q_opt = "s.ses_id='".addslashes($_COOKIE[$GLOBALS['COOKIE_NAME']])."'";
		} else if ((isset($_GET['S']) || isset($_POST['S'])) && $GLOBALS['FUD_OPT_1'] & 128) {
			$url_s = 1;
			$q_opt = "s.ses_id='".addslashes((isset($_GET['S']) ? $_GET['S'] : $_POST['S']))."'";
			/* do not validate against expired URL sessions */
			$q_opt .= " AND s.time_sec > ".(__request_timestamp__ - $GLOBALS['SESSION_TIMEOUT']);
		} else {
			return;
		}
		if ($GLOBALS['FUD_OPT_3'] & 4 && isset($_SERVER['HTTP_REFERER']) && strncmp($_SERVER['HTTP_REFERER'], $GLOBALS['WWW_ROOT'], strlen($GLOBALS['WWW_ROOT']))) {
			/* more checks, we need those because some proxies mangle referer field */
			$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
			/* $p > 8 https:// or http:// */
			if (($p = strpos($_SERVER['HTTP_REFERER'], $host)) === false || $p > 8) {
				$q_opt .= " AND s.user_id > 2000000000 ";
			}
		}
	} else {
		$q_opt = "s.id='".$id."'";
	}

	$u = db_sab('SELECT
		s.id AS sid, s.ses_id, s.data, s.returnto, s.sys_id,
		t.id AS theme_id, t.lang, t.name AS theme_name, t.locale, t.theme, t.pspell_lang, t.theme_opt,
		u.alias, u.posts_ppg, u.time_zone, u.sig, u.last_visit, u.last_read, u.cat_collapse_status, u.users_opt,
		u.ignore_list, u.ignore_list, u.buddy_list, u.id, u.group_leader_list, u.email, u.login, u.sq
	FROM fud26_ses s
		INNER JOIN fud26_users u ON u.id=(CASE WHEN s.user_id>2000000000 THEN 1 ELSE s.user_id END)
		INNER JOIN fud26_themes t ON t.id=u.theme
	WHERE '.$q_opt);

	/* anon user, no session or login */
	if (!$u || $u->id == 1 || $id) {
		return $u;
	}

	if ($u->sys_id == ses_make_sysid()) {
		return $u;
	} else if ($GLOBALS['FUD_OPT_3'] & 16 || isset($url_s)) {
		/* URL sessions must validate sys_id check and SESSION_IP_CHECK must be disabled */
		return;
	}

	/* try doing a strict SQ match in last-ditch effort to make things 'work' */
	if (isset($_POST['SQ']) && $_POST['SQ'] == $u->sq) {
		return $u;
	}

	return;
}

function &ses_anon_make()
{
	do {
		$uid = 2000000000 + mt_rand(1, 147483647);
		$ses_id = md5($uid . __request_timestamp__ . getmypid());
	} while (!($id = db_li("INSERT INTO fud26_ses (ses_id, time_sec, sys_id, user_id) VALUES ('".$ses_id."', ".__request_timestamp__.", '".ses_make_sysid()."', ".$uid.")", $ef, 1)));

	/* when we have an anon user, we set a special cookie allowing us to see who referred this user */
	if (isset($_GET['rid']) && !isset($_COOKIE['frm_referer_id']) && $GLOBALS['FUD_OPT_2'] & 8192) {
		setcookie($GLOBALS['COOKIE_NAME'].'_referer_id', $_GET['rid'], __request_timestamp__+31536000, $GLOBALS['COOKIE_PATH'], $GLOBALS['COOKIE_DOMAIN']);
	}
	setcookie($GLOBALS['COOKIE_NAME'], $ses_id, __request_timestamp__+$GLOBALS['COOKIE_TIMEOUT'], $GLOBALS['COOKIE_PATH'], $GLOBALS['COOKIE_DOMAIN']);

	return ses_get($id);
}

function ses_update_status($ses_id, $str=null, $forum_id=0, $ret='')
{
	q('UPDATE fud26_ses SET sys_id=\''.ses_make_sysid().'\', forum_id='.$forum_id.', time_sec='.__request_timestamp__.', action='.($str ? "'".addslashes($str)."'" : 'NULL').', returnto='.(!is_int($ret) ? strnull(addslashes($_SERVER['QUERY_STRING'])) : 'returnto').' WHERE id='.$ses_id);
}

function ses_putvar($ses_id, $data)
{
	$cond = is_int($ses_id) ? 'id='.(int)$ses_id : "ses_id='".$ses_id."'";

	if (empty($data)) {
		q('UPDATE fud26_ses SET data=NULL WHERE '.$cond);
	} else {
		q("UPDATE fud26_ses SET data='".addslashes(serialize($data))."' WHERE ".$cond);
	}
}

function ses_delete($ses_id)
{
	if (!($GLOBALS['FUD_OPT_2'] & 256)) {
		q('DELETE FROM fud26_ses WHERE id='.$ses_id);
	}
	setcookie($GLOBALS['COOKIE_NAME'], '', __request_timestamp__-100000, $GLOBALS['COOKIE_PATH'], $GLOBALS['COOKIE_DOMAIN']);

	return 1;
}

function ses_anonuser_auth($id, $error)
{
	q("UPDATE fud26_ses SET data='".addslashes(serialize($error))."', returnto=".strnull(addslashes($_SERVER['QUERY_STRING']))." WHERE id=".$id);
	if ($GLOBALS['FUD_OPT_2'] & 32768) {
		header("Location: http://timeweather.net/forum/index.php/l/"._rsidl);
	} else {
		header("Location: http://timeweather.net/forum/index.php?t=login&"._rsidl);
	}
	exit;
}function init_user()
{
	$o1 =& $GLOBALS['FUD_OPT_1'];
	$o2 =& $GLOBALS['FUD_OPT_2'];

	/* we need to parse S & rid right away since they are used during user init */
	if ($o2 & 32768 && !empty($_SERVER['PATH_INFO'])) {
		$pb = $p = explode('/', substr($_SERVER['PATH_INFO'], 1, -1));
		if ($o1 & 128) {
			$_GET['S'] = array_pop($p);
		}
		if ($o2 & 8192) {
			$_GET['rid'] = array_pop($p);
		}
		$_SERVER['QUERY_STRING'] = htmlspecialchars($_SERVER['PATH_INFO']) . '?' . $_SERVER['QUERY_STRING'];

		/* continuation of path info parsing */
		if (!isset($p[0])) {
			$p[0] = 'i';
		}
		switch ($p[0]) {
			case 'm': /* goto specific message */
				$_GET['t'] = 0;
				$_GET['goto'] = $p[1];
				if (isset($p[2])) {
					$_GET['th'] = $p[2];
					if (isset($p[3])) {
						$_GET['start'] = $p[3];
						if ($p[3]) {
							$_GET['t'] = 'msg';
							unset($_GET['goto']);
						}

						if (isset($p[4])) {
							if ($p[4] === 'prevloaded') {
								$_GET['prevloaded'] = 1;
								$i = 5;
							} else {
								$i = 4;
							}

							if (isset($p[$i])) {
								$_GET['rev'] = $p[$i];
								if (isset($p[$i+1])) {
									$_GET['reveal'] = $p[$i+1];
								}
							}
						}
					}
				}
				break;

			case 't': /* view thread */
				$_GET['t'] = 0;
				$_GET['th'] = $p[1];
				if (isset($p[2])) {
					$_GET['start'] = $p[2];
					if (!empty($p[3])) {
						$_GET[$p[3]] = 1;
					}
				}
				break;

			case 'f': /* view forum */
				$_GET['t'] = 1;
				$_GET['frm_id'] = $p[1];
				if (isset($p[2])) {
					$_GET['start'] = $p[2];
					if (isset($p[3])) {
						if ($p[3] === '0') {
							$_GET['sub'] = 1;
						} else {
							$_GET['unsub'] = 1;
						}
					}
				}
				break;

			case 'r':
				$_GET['t'] = 'post';
				$_GET[$p[1]] = $p[2];
				if (isset($p[3])) {
					$_GET['reply_to'] = $p[3];
					if (isset($p[4])) {
						if ($p[4]) {
							$_GET['quote'] = 'true';
						}
						if (isset($p[5])) {
							$_GET['start'] = $p[5];
						}
					}
				}
				break;

			case 'u': /* view user's info */
				$_GET['t'] = 'usrinfo';
				$_GET['id'] = $p[1];
				break;

			case 'i':
				$_GET['t'] = 'index';
				if (isset($p[1])) {
					$_GET['cat'] = (int) $p[1];
					if (isset($p[2])) {
						$_GET['c'] = $p[2];
					}
				}
				break;

			case 'fa':
				$_GET['t'] = 'getfile';
				$_GET['id'] = isset($p[1]) ? $p[1] : $pb[1];
				if (!empty($p[2])) {
					$_GET['private'] = 1;
				}
				break;

			case 'sp': /* show posts */
				$_GET['t'] = 'showposts';
				$_GET['id'] = $p[1];
				if (isset($p[2])) {
					$_GET['so'] = $p[2];
					if (isset($p[3])) {
						$_GET['start'] = $p[3];
					}
				}
				break;

			case 'l': /* login/logout */
				$_GET['t'] = 'login';
				if (isset($p[1])) {
					$_GET['logout'] = 1;
				}
				break;

			case 'e':
				$_GET['t'] = 'error';
				break;

			case 'st':
				$_GET['t'] = $p[1];
				$_GET['th'] = $p[2];
				$_GET['notify'] = $p[3];
				$_GET['opt'] = $p[4] ? 'on' : 'off';
				$_GET['start'] = $p[5];
				break;

			case 'sf':
				$_GET['t'] = $p[1];
				$_GET['frm_id'] = $p[2];
				$_GET[$p[3]] = 1;
				$_GET['start'] = $p[4];
				break;

			case 'sl':
				$_GET['t'] = 'subscribed';
				if ($p[1] == 'start') {
					$_GET['start'] = $p[2];
				} else {
					if (isset($p[2])) {
						$_GET['th'] = $p[2];
					} else if (isset($p[1])) {
						$_GET['frm_id'] = $p[1];
					}
				}
				break;

			case 'pmm':
				$_GET['t'] = 'ppost';
				if (isset($p[1])) {
					$_GET[$p[1]] = $p[2];
				}
				break;

			case 'pmv':
				$_GET['t'] = 'pmsg_view';
				$_GET['id'] = $p[1];
				if (isset($p[2])) {
					$_GET['dr'] = 1;
				}
				break;

			case 'pdm':
				$_GET['t'] = 'pmsg';
				if (isset($p[1])) {
					if ($p[1] !== 'btn_delete') {
						$_GET['folder_id'] = $p[1];
						if (isset($p[2]) && (int) $p[2]) {
							$_GET['all'] = 1;
						}
					} else {
						$_GET['btn_delete'] = 1;
						$_GET['sel'] = $p[2];
					}
					if (isset($p[3])) {
						$_GET['start'] = $p[3];
					}
				}
				break;

			case 'pl': /* poll list */
				$_GET['t'] = 'polllist';
				if (isset($p[1])) {
					$_GET['uid'] = $p[1];
					if (isset($p[2])) {
						$_GET['start'] = $p[2];
						if (isset($p[3])) {
							$_GET['oby'] = $p[3];
						}
					}
				}
				break;

			case 'ml': /* member list */
				$_GET['t'] = 'finduser';
				if (isset($p[1])) {
					if ($p[1] == '1') { /* order by reg date */
						$_GET['pc'] = 1;
					} else if ($p[1] == '2') { /* order by login */
						$_GET['us'] = 1;
					} /* else order by date */
					if (isset($p[2])) {
						$_GET['start'] = $p[2];
						if (isset($p[3])) {
							$_GET['usr_login'] = urldecode($p[3]);
							if (isset($p[4])) {
								$_GET['js_redr'] = $p[5];
							}
						}
					}
				}
				break;

			case 'h': /* help */
				$_GET['t'] = 'help_index';
				if (isset($p[1])) {
					$_GET['section'] = $p[1];
				}
				break;

			case 'cv': /* change thread view mode */
				$_GET['t'] = $p[1];
				$_GET['frm_id'] = $p[2];
				break;

			case 'mv': /* change message view mode */
				$_GET['t'] = $p[1];
				$_GET['th'] = $p[2];
				if (isset($p[3])) {
					if ($p[3] !== '0') {
						$_GET['goto'] = $p[3];
					} else {
						$_GET['prevloaded'] = 1;
						$_GET['start'] = $p[4];
						if (isset($p[5])) {
							$_GET['rev'] = $p[5];
							if (isset($p[6])) {
								$_GET['reveal'] = $p[6];
							}
						}
					}
				}
				break;

			case 'pv':
				$_GET['t'] = 0;
				if (isset($p[1])) {
					$_GET['goto'] = q_singleval("SELECT id FROM fud26_msg WHERE poll_id=".(int)$p[1]);
					$_GET['pl_view'] = empty($p[2]) ? 0 : (int)$p[2];
				}
				break;

			case 'rm': /* report message */
				$_GET['t'] = 'report';
				$_GET['msg_id'] = $p[1];
				break;

			case 'rl': /* list of reported messages */
				$_GET['t'] = 'reported';
				if (isset($p[1])) {
					$_GET['del'] = $p[1];
				}
				break;

			case 'd': /* delete thread/message */
				$_GET['t'] = 'mmod';
				$_GET['del'] = $p[1];
				if (isset($p[2])) {
					$_GET['th'] = $p[2];
				}
				break;

			case 'em': /* email forum member */
				$_GET['t'] = 'email';
				$_GET['toi'] = $p[1];
				break;

			case 'mar': /* mark all/forum read */
				$_GET['t'] = 'markread';
				if (isset($p[1])) {
					$_GET['id'] = $p[1];
					if (isset($p[2])) {
						$_GET['cat'] = $p[2];
					}
				}
				break;

			case 'bl': /* buddy list */
				$_GET['t'] = 'buddy_list';
				if (isset($p[1])) {
					if (!empty($p[2])) {
						$_GET['add'] = $p[1];
					} else {
						$_GET['del'] = $p[1];
					}
					if (isset($p[3])) {
						$_GET['redr'] = 1;
					}
				}
				break;

			case 'il': /* ignore list */
				$_GET['t'] = 'ignore_list';
				if (isset($p[1])) {
					if (!empty($p[2])) {
						$_GET['add'] = $p[1];
					} else {
						$_GET['del'] = $p[1];
					}
					if (isset($p[3])) {
						$_GET['redr'] = 1;
					}
				}
				break;

			case 'lk': /* lock/unlock thread */
				$_GET['t'] = 'mmod';
				$_GET['th'] = $p[1];
				$_GET[$p[2]] = 1;
				break;

			case 'stt': /* split thread */
				$_GET['t'] = 'split_th';
				if (isset($p[1])) {
					$_GET['th'] = $p[1];
				}
				break;

			case 'ef': /* email to friend */
				$_GET['t'] = 'remail';
				$_GET['th'] = $p[1];
				break;

			case 'lr': /* list referers */
				$_GET['t'] = 'list_referers';
				if (isset($p[1])) {
					$_GET['start'] = $p[1];
				}
				break;

			case 'a':
				$_GET['t'] = 'actions';
				break;

			case 's':
				$_GET['t'] = 'search';
				if (isset($p[1])) {
					$_GET['srch'] = urldecode($p[1]);
					$_GET['field'] = $p[2];
					$_GET['search_logic'] = $p[3];
					$_GET['sort_order'] = $p[4];
					$_GET['forum_limiter'] = $p[5];
					$_GET['start'] = $p[6];
					$_GET['author'] = $p[7];
				}
				break;

			case 'p':
				if (!is_numeric($p[1])) {
					$_GET[$p[1]] = $p[2];
				} else {
					$_GET['frm'] = $p[1];
					$_GET['page'] = $p[2];
				}
				break;

			case 'ot':
				$_GET['t'] = 'online_today';
				break;

			case 're':
				$_GET['t'] = 'register';
				if (isset($p[1])) {
					$_GET['reg_coppa'] = $p[1];
				}
				break;

			case 'tt':
				$_GET['t'] = $p[1];
				$_GET['frm_id'] = $p[2];
				break;

			case 'mh':
				$_GET['t'] = 'mvthread';
				$_GET['th'] = $p[1];
				if (isset($p[2], $p[3])) {
					$_GET[$p[2]] = $p[3];
				}
				break;

			case 'mn':
				$_GET['t'] = $p[1];
				$_GET['th'] = $p[2];
				$_GET['notify'] = $p[3];
				$_GET['opt'] = $p[4];
				if ($p[1] == 'msg') {
					$_GET['start'] = $p[5];
				} else {
					$_GET['mid'] = $p[5];
				}
				break;

			case 'tr':
				$_GET['t'] = 'ratethread';
				break;

			case 'gm':
				$_GET['t'] = 'groupmgr';
				if (isset($p[1], $p[2], $p[3])) {
					$_GET[$p[1]] = $p[2];
					$_GET['group_id'] = $p[3];
				}
				break;

			case 'te':
				$_GET['t'] = 'thr_exch';
				if (isset($p[1], $p[2])) {
					$_GET[$p[1]] = $p[2];
				}
				break;

			case 'mq':
				$_GET['t'] = 'modque';
				if (isset($p[1], $p[2])) {
					$_GET[$p[1]] = $p[2];
				}
				break;

			case 'pr':
				$_GET['t'] = 'pre_reg';
				$_GET['coppa'] = $p[1];
				break;

			case 'qb':
				$_GET['t'] = 'qbud';
				if (!empty($p[1])) {
					$_GET['all'] = 1;
				}
				break;

			case 'po':
				$_GET['t'] = 'poll';
				$_GET['frm_id'] = $p[1];
				if (isset($p[2])) {
					$_GET['pl_id'] = $p[2];
					if (isset($p[3], $p[4])) {
						$_GET[$p[3]] = $p[4];
					}
				}
				break;

			case 'sm':
				$_GET['t'] = 'smladd';
				break;

			case 'mk':
				$_GET['t'] = 'mklist';
				$_GET['tp'] = $p[1];
				break;

			case 'rp':
				$_GET['t'] = 'rpasswd';
				break;

			case 'as':
				$_GET['t'] = 'avatarsel';
				break;

			case 'sel':
				$_GET['t'] = 'selmsg';
				$c = (count($p) - 1) / 2;
				$j = 0;
				for ($i = 0; $i < $c; $i++) {
					@$_GET[$p[++$j]] = @$p[++$j];
				}
				break;

			case 'pml':
				$_GET['t'] = 'pmuserloc';
				$_GET['js_redr'] = $p[1];
				if (isset($p[2])) {
					$_GET['overwrite'] = 1;
				}
				break;

			case 'rst':
				$_GET['t'] = 'reset';
				if (isset($p[1])) {
					$_GET['email'] = urldecode($p[1]);
				}
				break;

			case 'cpf':
				$_GET['t'] = 'coppa_fax';
				break;

			case 'cp':
				$_GET['t'] = 'coppa';
				break;

			case 'rc':
				$_GET['t'] = 'reg_conf';
				break;

			case 'ma':
				$_GET['t'] = 'mnav';
				if (isset($p[1])) {
					$_GET['rng'] = isset($p[1]) ? $p[1] : 0;
					$_GET['rng2'] = isset($p[2]) ? $p[2] : 0;
					$_GET['u'] = isset($p[3]) ? $p[3] : 0;
					$_GET['start'] = isset($p[4]) ? $p[4] : 0;
				}
				break;

			case 'ip':
				$_GET['t'] = 'ip';
				if (isset($p[1])) {
					$_GET[($p[1][0] == 'i' ? 'ip' : 'user')] = isset($p[2]) ? $p[2] : '';
				}
				break;

			case 'met':
				$_GET['t'] = 'merge_th';
				if (isset($p[1])) {
					$_GET['frm'] = $p[1];
				}
				break;

			case 'uc':
				$_GET['t'] = 'uc';
				if (isset($p[1], $p[2])) {
					$_GET[$p[1]] = $p[2];
				}
				break;

			case 'mmd':
				$_GET['t'] = 'mmd';
				break;

			default:
				$_GET['t'] = 'index';
				break;
		}
		$GLOBALS['t'] = $_GET['t'];
	} else if (isset($_GET['t'])) {
		$GLOBALS['t'] = $_GET['t'];
	} else if (isset($_POST['t'])) {
		$GLOBALS['t'] = $_POST['t'];
	} else {
		$GLOBALS['t'] = 'index';
	}

	header('P3P: CP="ALL CUR OUR IND UNI ONL INT CNT STA"'); /* P3P Policy */

	$sq = 0;
	/* fetch an object with the user's session, profile & theme info */
	if (!($u = ses_get())) {
		/* new anon user */
		$u = ses_anon_make();
	} else if ($u->id != 1 && (!$GLOBALS['is_post'] || sq_check(1, $u->sq, $u->id, $u->ses_id))) { /* store the last visit date for registered user */
		header("Expires: Mon, 21 Jan 1980 06:01:01 GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");

		q('UPDATE fud26_users SET last_visit='.__request_timestamp__.' WHERE id='.$u->id);
		if ($GLOBALS['FUD_OPT_3'] & 1) {
			setcookie($GLOBALS['COOKIE_NAME'], $u->ses_id, 0, $GLOBALS['COOKIE_PATH'], $GLOBALS['COOKIE_DOMAIN']);
		}
		if (!$u->sq || __request_timestamp__ - $u->last_visit > 180) {
			$u->sq = $sq = regen_sq($u->id);
			if (!$GLOBALS['is_post']) {
				$_GET['SQ'] = $sq;
			} else {
				$_POST['SQ'] = $sq;
			}
		} else {
			$sq =& $u->sq;
		}
	}
	if ($u->data) {
		$u->data = unserialize($u->data);
	}
	$uo = $u->users_opt = (int) $u->users_opt;

	/* this should allow path_info & normal themes to work properly within 1 forum */
	if ($o2 & 32768 && !($u->theme_opt & 4)) {
		$o2 ^= 32768;
	}

	/* handle PM disabling for users */
	if (!($GLOBALS['is_a'] = $uo & 1048576) && $uo & 33554432) {
		$o1 = $o1 &~ 1024;
	}

	/* set timezone */
	if ($u->time_zone && !($GLOBALS['FUD_OPT_3'] & 512)) {
		@putenv('TZ=' . $u->time_zone);
	}
	/* set locale */
	$GLOBALS['good_locale'] = setlocale(LC_ALL, $u->locale);

	/* view format for threads & messages */
	define('d_thread_view', $uo & 256 ? 'msg' : 'tree');
	define('t_thread_view', $uo & 128 ? 'thread' : 'threadt');
	if ($GLOBALS['t'] === 0) {
		$GLOBALS['t'] = $_GET['t'] = d_thread_view;
	} else if ($GLOBALS['t'] === 1) {
		$GLOBALS['t'] = $_GET['t'] = t_thread_view;
	}

	/* theme path */
	@define('fud_theme', 'theme/' . ($u->theme_name ? $u->theme_name : 'default') . '/');

	/* define _uid, which, will tell us if this is a 'real' user or not */
	define('__fud_real_user__', ($u->id != 1 ? $u->id : 0));
	define('_uid', __fud_real_user__ && ($uo & 131072) && !($uo & 2097152) ? $u->id : 0);

	$GLOBALS['sq'] = $sq;

	/* define constants used to track URL sessions & referrals */
	if ($o1 & 128) {
		define('s', $u->ses_id); define('_hs', '<input type="hidden" name="S" value="'.s.'"><input type="hidden" name="SQ" value="'.$sq.'">');
		if ($o2 & 8192) {
			if ($o2 & 32768) {
				define('_rsid', __fud_real_user__ . '/' . s.'/');
			} else {
				define('_rsid', 'rid='.__fud_real_user__.'&amp;S='.s);
			}
		} else {
			if ($o2 & 32768) {
				define('_rsid', s.'/');
			} else {
				define('_rsid',  'S='.s);
			}
		}
	} else {
		define('s', ''); define('_hs', '<input type="hidden" name="SQ" value="'.$sq.'">');
		if ($o2 & 8192) {
			if ($o2 & 32768) {
				define('_rsid', __fud_real_user__.'/');
			} else {
				define('_rsid', 'rid='.__fud_real_user__);
			}
		} else {
			define('_rsid', '');
		}
	}
	define('_rsidl', ($o2 & 32768 ? _rsid : str_replace('&amp;', '&', _rsid)));

	return $u;
}

function user_register_forum_view($frm_id)
{
	if ($GLOBALS['FUD_OPT_3'] & 1024) {
		q('INSERT INTO fud26_forum_read (forum_id, user_id, last_view) VALUES ('.$frm_id.', '._uid.', '.__request_timestamp__.') ON DUPLICATE KEY UPDATE last_view=VALUES(last_view)');
		return;
	}
	
	if (!db_li('INSERT INTO fud26_forum_read (forum_id, user_id, last_view) VALUES ('.$frm_id.', '._uid.', '.__request_timestamp__.')', $ef)) {
		q('UPDATE fud26_forum_read SET last_view='.__request_timestamp__.' WHERE forum_id='.$frm_id.' AND user_id='._uid);
	}
}

function user_register_thread_view($thread_id, $tm=__request_timestamp__, $msg_id=0)
{
	if ($GLOBALS['FUD_OPT_3'] & 1024) {
		q('INSERT INTO fud26_read (last_view, msg_id, thread_id, user_id) VALUES('.$tm.', '.$msg_id.', '.$thread_id.', '._uid.') ON DUPLICATE KEY UPDATE last_view=VALUES(last_view), msg_id=VALUES(msg_id)');
		return;
	}

	if (!db_li('INSERT INTO fud26_read (last_view, msg_id, thread_id, user_id) VALUES('.$tm.', '.$msg_id.', '.$thread_id.', '._uid.')', $ef)) {
		q('UPDATE fud26_read SET last_view='.$tm.', msg_id='.$msg_id.' WHERE thread_id='.$thread_id.' AND user_id='._uid);
	}
}

function user_set_post_count($uid)
{
	$pd = db_saq("SELECT MAX(id),count(*) FROM fud26_msg WHERE poster_id=".$uid." AND apr=1");
	$level_id = (int) q_singleval('SELECT id FROM fud26_level WHERE post_count <= '.$pd[1].' ORDER BY post_count DESC LIMIT 1');
	q('UPDATE fud26_users SET u_last_post_id='.(int)$pd[0].', posted_msg_count='.(int)$pd[1].', level_id='.$level_id.' WHERE id='.$uid);
}

function user_mark_all_read($id)
{
	q('UPDATE fud26_users SET last_read='.__request_timestamp__.' WHERE id='.$id);
	q('DELETE FROM fud26_read WHERE user_id='.$id);
	q('DELETE FROM fud26_forum_read WHERE user_id='.$id);
}

function user_mark_forum_read($id, $fid, $last_view)
{
	if (__dbtype__ == 'mysql') {
		q('REPLACE INTO fud26_read (user_id, thread_id, msg_id, last_view) SELECT '.$id.', id, last_post_id, '.__request_timestamp__.' FROM fud26_thread WHERE forum_id='.$fid.' AND last_post_date > '.$last_view);
	} else {
		if (!db_li('INSERT INTO fud26_read (user_id, thread_id, msg_id, last_view) SELECT '.$id.', id, last_post_id, '.__request_timestamp__.' FROM fud26_thread WHERE forum_id='.$fid.' AND last_post_date > '.$last_view, $ef)) {
			q("UPDATE fud26_read SET user_id=".$id.", msg_id=t.last_post_id, last_view=".__request_timestamp__." FROM (SELECT id, last_post_id FROM fud26_thread WHERE forum_id=".$fid." AND last_post_date > ".$last_view.") t WHERE user_id=".$id." AND thread_id=t.id");
		}
	}
	user_register_forum_view($fid);
}

function sq_check($post, &$sq, $uid=__fud_real_user__, $ses=s)
{
	/* no sequence # check for anonymous users */
	if (!$uid) {
		return 1;
	}

	if ($post && isset($_POST['SQ'])) {
		$s = $_POST['SQ'];
	} else if (!$post && isset($_GET['SQ'])) {
		$s = $_GET['SQ'];
	} else {
		$s = 0;
	}

	if ($sq !== $s) {
		if ($GLOBALS['t'] == 'post' || $GLOBALS['t'] == 'ppost') {
			define('fud_bad_sq', 1);
			$sq = regen_sq($uid);
			return 1;
		}
		header('Location: http://timeweather.net/forum/index.php?S='.$ses);
		exit;
	}

	return 1;
}

function regen_sq($uid=__fud_real_user__)
{
	$sq = md5(get_random_value(128));
	q("UPDATE fud26_users SET sq='".$sq."' WHERE id=".$uid);
	return $sq;
}

if (isset($_SERVER['REMOTE_ADDR']) || !defined('forum_debug')) {
	$GLOBALS['usr'] =& init_user();
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
}$GLOBALS['__SML_CHR_CHK__'] = array("\n"=>1, "\r"=>1, "\t"=>1, " "=>1, "]"=>1, "["=>1, "<"=>1, ">"=>1, "'"=>1, '"'=>1, "("=>1, ")"=>1, "."=>1, ","=>1, "!"=>1, "?"=>1);

function smiley_to_post($text)
{
	$text_l = strtolower($text);
	include $GLOBALS['FORUM_SETTINGS_PATH'].'sp_cache';

	foreach ($SML_REPL as $k => $v) {
		$a = 0;
		$len = strlen($k);
		while (($a = strpos($text_l, $k, $a)) !== false) {
			if ((!$a || isset($GLOBALS['__SML_CHR_CHK__'][$text_l[$a - 1]])) && ((@$ch = $text_l[$a + $len]) == "" || isset($GLOBALS['__SML_CHR_CHK__'][$ch]))) {
				$text_l = substr_replace($text_l, $v, $a, $len);
				$text = substr_replace($text, $v, $a, $len);
				$a += strlen($v) - $len;
			} else {
				$a += $len;
			}
		}
	}

	return $text;
}

function post_to_smiley($text)
{
	/* include once since draw_post_smiley_cntrl() may use it too */
	include_once $GLOBALS['FORUM_SETTINGS_PATH'].'ps_cache';
	if (isset($PS_SRC)) {
		$GLOBALS['PS_SRC'] = $PS_SRC;
		$GLOBALS['PS_DST'] = $PS_DST;
	} else {
		$PS_SRC = $GLOBALS['PS_SRC'];
		$PS_DST = $GLOBALS['PS_DST'];
	}

	/* check for emoticons */
	foreach ($PS_SRC as $k => $v) {
		if (strpos($text, $v) === false) {
			unset($PS_SRC[$k], $PS_DST[$k]);
		}
	}

	return $PS_SRC ? str_replace($PS_SRC, $PS_DST, $text) : $text;
}


	if (!($FUD_OPT_2 & 134217728)) {
		std_error('disabled');
	}

	if ($FUD_OPT_2 & 16384) {
		ob_start(array('ob_gzhandler', $PHP_COMPRESSION_LEVEL));
	}

	$forum	= isset($_GET['frm']) ? (int)$_GET['frm'] : 0;
	$thread	= isset($_GET['th']) ? (int)$_GET['th'] : 0;
	$msg	= isset($_GET['msg']) ? (int)$_GET['msg'] : 0;
	$page	= isset($_GET['page']) ? (int)$_GET['page'] : 0;
	$sel	= isset($_GET['sel']) ? (array)$_GET['sel'] : 0;

	if ($forum) {
		if (!($FUD_OPT_2 & 268435456)) {
			 std_error('disabled');		
		}

		if (!$page) {
			$page = 1;
		}

		if ($page) {
			$join = 'FROM fud26_thread_view tv
				INNER JOIN fud26_thread t ON t.id=tv.thread_id
				INNER JOIN fud26_forum f ON f.id='.$forum.'
				INNER JOIN fud26_msg m ON m.thread_id=t.id
				LEFT JOIN fud26_users u ON m.poster_id=u.id
				LEFT JOIN fud26_poll p ON m.poll_id=p.id
			';
			$lmt = ' AND tv.forum_id='.$forum.' AND tv.page='.$page;
		} else {
			$join = 'FROM fud26_forum f
				INNER JOIN fud26_thread t ON t.forum_id=f.id
				INNER JOIN fud26_msg m ON m.thread_id=t.id
				LEFT JOIN fud26_users u ON m.poster_id=u.id
				LEFT JOIN fud26_poll p ON m.poll_id=p.id
			';
			$lmt = ' AND f.id='.$forum;
		}
	} else if ($thread) {
		$join = 'FROM fud26_msg m
				INNER JOIN fud26_thread t ON t.id=m.thread_id
				INNER JOIN fud26_forum f ON f.id=t.forum_id
				LEFT JOIN fud26_users u ON m.poster_id=u.id
				LEFT JOIN fud26_poll p ON m.poll_id=p.id
			';
		$lmt = ' AND m.thread_id='.$thread;
	} else if ($msg) {
		$lmt = ' AND m.id='.$msg;
		$join = 'FROM fud26_msg m
				INNER JOIN fud26_thread t ON t.id=m.thread_id
				INNER JOIN fud26_forum f ON f.id=t.forum_id
				LEFT JOIN fud26_users u ON m.poster_id=u.id
				LEFT JOIN fud26_poll p ON m.poll_id=p.id
			';
	} else if ($sel) { /* PM handling */
		foreach ($sel as $k => $v) {
			if ($v = (int)$v) {
				$sel[$k] = $v;
			} else {
				unset($sel[$k]);
			}
		}
		if (!$sel || !q_singleval("SELECT count(*) FROM fud26_pmsg WHERE id IN(".implode(',', $sel).") AND duser_id="._uid)) {
			invl_inp_err();
		}
		fud_use('private.inc');
	} else {
		invl_inp_err();
	}

	$re = array();
	$c = uq('SELECT code, '.__FUD_SQL_CONCAT__.'(\'images/smiley_icons/\', img), descr FROM fud26_smiley');
	while ($r = db_rowarr($c)) {
		$im = '<img src="'.$r[1].'" border=0 alt="'.$r[2].'">';
		$re[$im] = (($p = strpos($r[0], '~')) !== false) ? substr($r[0], 0, $p) : $r[0];
	}

	if (_uid) {
		if (!$is_a) {
			$join .= '	INNER JOIN fud26_group_cache g1 ON g1.user_id=2147483647 AND g1.resource_id=f.id
					LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
					LEFT JOIN fud26_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.' ';
			$lmt .= " AND (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 2) > 0)";
		}
	} else {
		$join .= ' INNER JOIN fud26_group_cache g1 ON g1.user_id=0 AND g1.resource_id=f.id ';
		$lmt .= " AND (g1.group_cache_opt & 2) > 0";
	}

	if ($forum) {
		$subject = q_singleval('SELECT name FROM fud26_forum WHERE id='.$forum);
	}

	if (!$sel) {
		$c = uq('SELECT
				m.id, m.thread_id, m.subject, m.post_stamp,
				m.attach_cnt, m.attach_cache, m.poll_cache,
				m.foff, m.length, m.file_id, u.id AS uid,
				(CASE WHEN u.alias IS NULL THEN \''.$ANON_NICK.'\' ELSE u.alias END) as alias,
				p.name AS poll_name, p.total_votes
			'.$join.'
			WHERE
				t.moved_to=0 AND m.apr=1 '.$lmt.' ORDER BY m.post_stamp, m.thread_id');
	} else {
		$c = q("SELECT p.*, u.alias, p.duser_id AS uid FROM fud26_pmsg p 
				LEFT JOIN fud26_users u ON p.ouser_id=u.id
				WHERE p.id IN(".implode(',', $sel).") AND p.duser_id="._uid);
	}

	if (!($o = db_rowobj($c))) {
		invl_inp_err();
	}

	if ($thread || $msg) {
		$subject = $o->subject;
	} else if ($sel) {
		$subject = 'Private Message Archive';
	}

	$fpdf = new fud_pdf('FUDforum ' . $FORUM_VERSION, $FORUM_TITLE, $subject, $PDF_PAGE, $PDF_WMARGIN, $PDF_HMARGIN);
	$fpdf->begin_page($subject);
	do {
		/* write message header */
		$fpdf->message_header(reverse_fmt($o->subject), array($o->uid, reverse_fmt($o->alias)), $o->post_stamp, $o->id, (isset($o->thread_id) ? $o->thread_id : 0));

		/* write message body */
		if (!$sel) {
			$body = read_msg_body($o->foff, $o->length, $o->file_id);
		} else {
			$body = read_pmsg_body($o->foff, $o->length);
		}

		$fpdf->input_text(reverse_fmt(strip_tags(post_to_smiley($body, $re))));

		/* handle attachments */
		if ($o->attach_cnt) {
			if (!empty($o->attach_cache) && ($a = unserialize($o->attach_cache))) {
				$attch = array();
				foreach ($a as $i) {
					$attch[] = array('id' => $i[0], 'name' => $i[1], 'nd' => $i[3]);
				}
				$fpdf->add_attacments($attch);
			} else if ($sel) {
				$attch = array();
				$c2 = uq("SELECT id, original_name, dlcount FROM fud26_attach WHERE message_id={$o->id} AND attach_opt=1");
				while ($r2 = db_rowarr($c2)) {
					$attch[] = array('id' => $r2[0], 'name' => $r2[1], 'nd' => $r2[2]);
				}
				if ($attch) {
					$fpdf->add_attacments($attch, 1);
				}
			}
		}

		/* handle polls */
		if (!empty($o->poll_name) && $o->poll_cache && ($pc = unserialize($o->poll_cache))) {
			$votes = array();
			foreach ($pc as $opt) {
				$votes[] = array('name' => reverse_fmt(strip_tags(post_to_smiley($opt[0], $re))), 'votes' => $opt[1]);
			}
			$fpdf->add_poll(reverse_fmt($o->poll_name), $votes, $o->total_votes);
		}

		$fpdf->end_message();
	} while (($o = db_rowobj($c)));

	$fpdf->Output('FUDforum'.date('Ymd').'.pdf', 'I');
?>