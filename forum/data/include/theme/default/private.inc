<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: private.inc.t,v 1.39 2005/03/09 00:24:14 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

$GLOBALS['recv_user_id'] = array();

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
}
?>