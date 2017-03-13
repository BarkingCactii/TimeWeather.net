<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: drawpmsg.inc.t,v 1.42 2005/03/16 21:12:42 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

$GLOBALS['affero_domain'] = parse_url($GLOBALS['WWW_ROOT']);

function tmpl_drawpmsg($obj, $usr, $mini)
{
	$o1 =& $GLOBALS['FUD_OPT_1'];
	$o2 =& $GLOBALS['FUD_OPT_2'];
	$a = (int) $obj->users_opt;
	$b =& $usr->users_opt;

	if (!$mini) {
		$custom_tag = $obj->custom_status ? '<br />'.$obj->custom_status.'' : '';
		$c = (int) $obj->level_opt;

		if ($obj->avatar_loc && $a & 8388608 && $b & 8192 && $o1 & 28 && !($c & 2)) {
			if (!($c & 1)) {
				$level_name =& $obj->level_name;
				$level_image = $obj->level_img ? '&nbsp;<img src="images/'.$obj->level_img.'" alt="" />' : '';
			} else {
				$level_name = $level_image = '';
			}
		} else {
			$level_image = $obj->level_img ? '&nbsp;<img src="images/'.$obj->level_img.'" alt="" />' : '';
			$obj->avatar_loc = '';
			$level_name =& $obj->level_name;
		}
		$avatar = ($obj->avatar_loc || $level_image) ? '<td class="avatarPad wo">'.$obj->avatar_loc.$level_image.'</td>' : '';
		$dmsg_tags = ($custom_tag || $level_name) ? '<div class="ctags">'.$level_name.$custom_tag.'</div>' : '';

		if (($o2 & 32 && !($a & 32768)) || $b & 1048576) {
			$obj->login = $obj->alias;
			$online_indicator = (($obj->last_visit + $GLOBALS['LOGEDIN_TIMEOUT'] * 60) > __request_timestamp__) ? '<img src="theme/default/images/online.png" alt="'.$obj->login.' is currently online" title="'.$obj->login.' is currently online" />' : '<img src="theme/default/images/offline.png" alt="'.$obj->login.'  is currently offline" title="'.$obj->login.'  is currently offline" />';
		} else {
			$online_indicator = '';
		}

		$host_name = ($obj->host_name && $o1 & 268435456) ? '<b>From:</b> '.$obj->host_name.'<br />' : '';
		$ip_address = '';

		if ($obj->location) {
			if (strlen($obj->location) > $GLOBALS['MAX_LOCATION_SHOW']) {
				$location = substr($obj->location, 0, $GLOBALS['MAX_LOCATION_SHOW']) . '...';
			} else {
				$location = $obj->location;
			}
			$location = '<br /><b>Location:</b> '.$location;
		} else {
			$location = '';
		}
		$msg_icon = !$obj->icon ? '' : '<img src="images/message_icons/'.$obj->icon.'" alt="" />&nbsp;&nbsp;';
		$usr->buddy_list = $usr->buddy_list ? unserialize($usr->buddy_list) : array();
		if ($obj->user_id != _uid && $obj->user_id > 0) {
			$buddy_link = !isset($usr->buddy_list[$obj->user_id]) ? '<a href="index.php?t=buddy_list&amp;'._rsid.'&amp;add='.$obj->user_id.'&amp;SQ='.$GLOBALS['sq'].'">add to buddy list</a><br />' : '<br />[<a href="index.php?t=buddy_list&amp;del='.$obj->user_id.'&amp;redr=1&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">remove from buddy list</a>]';
		} else {
			$buddy_link = '';
		}
		/* show im buttons if need be */
		if ($b & 16384) {
			$im_icq		= $obj->icq ? '<a href="index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'#icq_msg"><img src="theme/default/images/icq.png" alt="" title="'.$obj->icq.'" /></a>&nbsp;' : '';
			$im_aim		= $obj->aim ? '<a href="aim:goim?screenname='.$obj->aim.'&amp;message=Hi.+Are+you+there?" target="_blank"><img src="theme/default/images/aim.png" title="'.$obj->aim.'" alt="" /></a>&nbsp;' : '';
			$im_yahoo	= $obj->yahoo ? '<a target="_blank" href="http://edit.yahoo.com/config/send_webmesg?.target='.$obj->yahoo.'&amp;.src=pg"><img src="theme/default/images/yahoo.png" alt="" title="'.$obj->yahoo.'" /></a>&nbsp;' : '';
			$im_msnm	= $obj->msnm ? '<a href="mailto:'.$obj->msnm.'"><img src="theme/default/images/msnm.png" title="'.$obj->msnm.'" alt="" /></a>' : '';
			$im_jabber	= $obj->jabber ? '<img src="theme/default/images/jabber.png" title="'.$obj->jabber.'" alt="" />' : '';
			if ($o2 & 2048) {
				$im_affero = $obj->affero ? '<a href="http://svcs.affero.net/rm.php?r='.$obj->affero.'&amp;ll=0.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;lp=0.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="theme/default/images/affero_reg.gif" /></a>' : '<a href="http://svcs.affero.net/rm.php?m='.urlencode($obj->email).'&amp;ll=0.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;lp=0.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="theme/default/images/affero_noreg.gif" /></a>';
			} else {
				$im_affero = '';
			}
			$dmsg_im_row = ($im_icq || $im_aim || $im_yahoo || $im_msnm || $im_jabber || $im_affero) ? ''.$im_icq.' '.$im_aim.' '.$im_yahoo.' '.$im_msnm.' '.$im_jabber.' '.$im_affero.'<br />' : '';
		} else {
			$dmsg_im_row = '';
		}
		if ($obj->ouser_id != _uid) {
			$user_profile = '<a href="index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'"><img src="theme/default/images/msg_about.gif" alt="" /></a>';
			$email_link = ($o1 & 4194304 && $a & 16) ? '<a href="index.php?t=email&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img src="theme/default/images/msg_email.gif" alt="" /></a>' : '';
			$private_msg_link = '<a href="index.php?t=ppost&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img title="Send a private message to this user" src="theme/default/images/msg_pm.gif" alt="" /></a>';
		} else {
			$user_profile = $email_link = $private_msg_link = '';
		}
		$edit_link = $obj->fldr == 4 ? '<a href="index.php?t=ppost&amp;msg_id='.$obj->id.'&amp;'._rsid.'"><img src="theme/default/images/msg_edit.gif" alt="" /></a>&nbsp;&nbsp;&nbsp;&nbsp;' : '';
		if ($obj->fldr == 1) {
			$reply_link = '<a href="index.php?t=ppost&amp;reply='.$obj->id.'&amp;'._rsid.'"><img src="theme/default/images/msg_reply.gif" alt="" /></a>&nbsp;';
			$quote_link = '<a href="index.php?t=ppost&amp;quote='.$obj->id.'&amp;'._rsid.'"><img src="theme/default/images/msg_quote.gif" alt="" /></a>&nbsp;';
		} else {
			$reply_link = $quote_link = '';
		}
		$profile_link = '<a href="index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'">'.$obj->alias.'</a>';
		$msg_toolbar = '<tr><td colspan="2" class="MsgToolBar"><table border=0 cellspacing=0 cellpadding=0 class="wa"><tr>
<td class="nw al">'.$user_profile.'&nbsp;'.$email_link.'&nbsp;'.$private_msg_link.'</td>
<td class="nw ar"><a href="index.php?t=pmsg&amp;'._rsid.'&amp;btn_delete=1&amp;sel='.$obj->id.'&amp;SQ='.$GLOBALS['sq'].'"><img src="theme/default/images/msg_delete.gif" alt="" /></a>&nbsp;'.$edit_link.$reply_link.$quote_link.'<a href="index.php?t=ppost&amp;forward='.$obj->id.'&amp;'._rsid.'"><img src="theme/default/images/msg_forward.gif" alt="" /></a></td>
</tr></table></td></tr>';
	} else {
		$dmsg_tags = $dmsg_im_row = $user_profile = $msg_toolbar = $buddy_link = $avatar = $online_indicator = $host_name = $location = $msg_icon = '';
		$profile_link = ''.$obj->alias;
	}
	$msg_body = $obj->length ? read_pmsg_body($obj->foff, $obj->length) : 'No Message Body';

	$file_attachments = '';
	if ($obj->attach_cnt) {
		$c = uq('SELECT a.id, a.original_name, a.dlcount, m.icon, a.fsize FROM fud26_attach a LEFT JOIN fud26_mime m ON a.mime_type=m.id WHERE a.message_id='.$obj->id.' AND attach_opt=1');
		while ($r = db_rowobj($c)) {
			$sz = $r->fsize/1024;
			$sz = $sz<1000 ? number_format($sz, 2).'KB' : number_format($sz / 1024 ,2).'MB';
			if(!$r->icon) {
				$r->icon = 'unknown.gif';
			}
			$file_attachments .= '<li />
<a href="index.php?t=getfile&amp;id='.$r->id.'&amp;'._rsid.'&amp;private=1"><img src="images/mime/'.$r->icon.'" class="at" alt="" /></a>
<span class="GenText fb">Attachment:</span> <a href="index.php?t=getfile&amp;id='.$r->id.'&amp;'._rsid.'&amp;private=1">'.$r->original_name.'</a><br />
<span class="SmallText">(Size: '.$sz.', Downloaded '.$r->dlcount.' time(s))<p /></span>';
		}
		if ($file_attachments) {
			$file_attachments = '<p />
<ul class="AttachmentsList">
'.$file_attachments.'
</ul>';
			/* append session to getfile */
			if ($o1 & 128 && !isset($_COOKIE[$GLOBALS['COOKIE_NAME']])) {
				$msg_body = str_replace('<img src="index.php?t=getfile', '<img src="index.php?t=getfile&amp;S='.s, $msg_body);
				$tap = 1;
			}
			if ($o2 & 32768 && (isset($tap) || $o2 & 8192)) {
				$pos = 0;
				while (($pos = strpos($msg_body, '<img src="index.php/fa/', $pos)) !== false) {
					$pos = strpos($msg_body, '"', $pos + 11);
					$msg_body = substr_replace($msg_body, _rsid, $pos, 0);
				}
			}
		}
	}

	$signature = ($obj->sig && $o1 & 32768 && $obj->pmsg_opt & 1 && $b & 4096) ? '<p /><hr class="sig" />'.$obj->sig.'' : '';

	return '<tr><td><table cellspacing=0 cellpadding=0 class="MsgTable">
<tr>
<td class="MsgR1 al vt MsgSubText">'.$msg_icon.$obj->subject.'</td>
<td class="MsgR1 vt ar DateText">'.strftime("%a, %d %B %Y %H:%M", $obj->post_stamp).'</td>
</tr>
<tr class="MsgR2"><td class="MsgR2" colspan=2><table cellspacing="0" cellpadding="0" class="ContentTable">
<tr class="MsgR2">
'.$avatar.'
<td class="msgud">'.$online_indicator.$profile_link.(!$mini ? '<br /><b>Messages:</b> '.$obj->posted_msg_count.'<br /><b>Registered:</b> '.strftime("%B %Y", $obj->join_date).' '.$location.'' : '' )  .'</td>
<td class="msgud">'.$dmsg_tags.'</td>
<td class="msgot">'.$buddy_link.$dmsg_im_row.'</td>
</tr></table></td>
</tr>
<tr><td class="MsgR3" colspan=2>'.$msg_body.$file_attachments.$signature.'</td></tr>
'.$msg_toolbar.'
<tr><td class="MsgR2 ac" colspan=2>'.$GLOBALS['dpmsg_prev_message'].' '.$GLOBALS['dpmsg_next_message'].'</td></tr>
</table></td></tr>';
}
?>