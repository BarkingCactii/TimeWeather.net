<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: tree.php.t,v 1.77 2005/02/27 02:21:36 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}/* Handle poll votes if any are present */
function register_vote(&$options, $poll_id, $opt_id, $mid)
{
	/* invalid option or previously voted */
	if (!isset($options[$opt_id]) || q_singleval('SELECT id FROM fud26_poll_opt_track WHERE poll_id='.$poll_id.' AND user_id='._uid)) {
		return;
	}

	if (db_li('INSERT INTO fud26_poll_opt_track(poll_id, user_id, poll_opt) VALUES('.$poll_id.', '._uid.', '.$opt_id.')', $a)) {
		q('UPDATE fud26_poll_opt SET count=count+1 WHERE id='.$opt_id);
		q('UPDATE fud26_poll SET total_votes=total_votes+1 WHERE id='.$poll_id);
		poll_cache_rebuild($opt_id, $options);
		q('UPDATE fud26_msg SET poll_cache='.strnull(addslashes(serialize($options))).' WHERE id='.$mid);
	}

	return 1;
}

$query_type = (empty($_POST['poll_opt']) || !($_POST['poll_opt'] = (int)$_POST['poll_opt']) ? 'uq' : 'q');
$GLOBALS['__FMDSP__'] = array();

/* needed for message threshold & reveling messages */
if (isset($_GET['rev'])) {
	$_GET['rev'] = htmlspecialchars($_GET['rev']);
	foreach (explode(':', $_GET['rev']) as $v) {
		$GLOBALS['__FMDSP__'][(int)$v] = 1;
	}
	if ($GLOBALS['FUD_OPT_2'] & 32768) {
		define('reveal_lnk', '/' . $_GET['rev']);
	} else {
		define('reveal_lnk', '&amp;rev=' . $_GET['rev']);
	}
} else {
	define('reveal_lnk', '');
}

/* initialize buddy & ignore list for registered users */
if (_uid) {
	if ($usr->buddy_list) {
		$usr->buddy_list = unserialize($usr->buddy_list);
	}
	if ($usr->ignore_list) {
		$usr->ignore_list = unserialize($usr->ignore_list);
		if (isset($usr->ignore_list[1])) {
			$usr->ignore_list[0] =& $usr->ignore_list[1];
		}
	}

	/* handle temporarily un-hidden users */
	if (isset($_GET['reveal'])) {
		$_GET['reveal'] = htmlspecialchars($_GET['reveal']);
		foreach(explode(':', $_GET['reveal']) as $v) {
			$v = (int) $v;
			if (isset($usr->ignore_list[$v])) {
				$usr->ignore_list[$v] = 0;
			}
		}
		if ($GLOBALS['FUD_OPT_2'] & 32768) {
			define('unignore_tmp', '/' . $_GET['reveal']);
		} else {
			define('unignore_tmp', '&amp;reveal='.$_GET['reveal']);
		}
	} else {
		define('unignore_tmp', '');
	}
} else {
	define('unignore_tmp', '');
}

if ($GLOBALS['FUD_OPT_2'] & 2048) {
	$GLOBALS['affero_domain'] = parse_url($WWW_ROOT);
	$GLOBALS['affero_domain'] = $GLOBALS['affero_domain']['host'];
}

$_SERVER['QUERY_STRING_ENC'] = htmlspecialchars($_SERVER['QUERY_STRING']);

function make_tmp_unignore_lnk($id)
{
	if ($GLOBALS['FUD_OPT_2'] & 32768 && strpos($_SERVER['QUERY_STRING_ENC'], '?') === false) {
		$_SERVER['QUERY_STRING_ENC'] .= '?1=1';
	}

	if (!isset($_GET['reveal'])) {
		return $_SERVER['QUERY_STRING_ENC'] . '&amp;reveal='.$id;
	} else {
		return str_replace('&amp;reveal='.$_GET['reveal'], unignore_tmp . ':' . $id, $_SERVER['QUERY_STRING_ENC']);
	}
}

function make_reveal_link($id)
{
	if ($GLOBALS['FUD_OPT_2'] & 32768 && strpos($_SERVER['QUERY_STRING_ENC'], '?') === false) {
		$_SERVER['QUERY_STRING_ENC'] .= '?1=1';
	}

	if (empty($GLOBALS['__FMDSP__'])) {
		return $_SERVER['QUERY_STRING_ENC'] . '&amp;rev='.$id;
	} else {
		return str_replace('&amp;rev='.$_GET['rev'], reveal_lnk . ':' . $id, $_SERVER['QUERY_STRING_ENC']);
	}
}

/* Draws a message, needs a message object, user object, permissions array,
 * flag indicating wether or not to show controls and a variable indicating
 * the number of the current message (needed for cross message pager)
 * last argument can be anything, allowing forms to specify various vars they
 * need to.
 */
function tmpl_drawmsg($obj, $usr, $perms, $hide_controls, &$m_num, $misc)
{
	$o1 =& $GLOBALS['FUD_OPT_1'];
	$o2 =& $GLOBALS['FUD_OPT_2'];
	$a = (int) $obj->users_opt;
	$b =& $usr->users_opt;

	$next_page = $next_message = $prev_message = '';
	/* draw next/prev message controls */
	if (!$hide_controls && $misc) {
		/* tree view is a special condition, we only show 1 message per page */
		if ($_GET['t'] == 'tree') {
			$prev_message = $misc[0] ? '<a href="index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;mid='.$misc[0].'"><img src="theme/default/images/up.png" title="Go to previous message" alt="Go to previous message" width=16 height=11 /></a>' : '';
			$next_message = $misc[1] ? '<a href="index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;mid='.$misc[1].'"><img alt="Go to previous message" title="Go to next message" src="theme/default/images/down.png" width=16 height=11 /></a>' : '';
		} else {
			/* handle previous link */
			if (!$m_num && $obj->id > $obj->root_msg_id) { /* prev link on different page */
				$prev_message = '<a href="index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.($misc[0] - $misc[1]).reveal_lnk.unignore_tmp.'"><img src="theme/default/images/up.png" title="Go to previous message" alt="Go to previous message" width=16 height=11 /></a>';
			} else if ($m_num) { /* inline link, same page */
				$prev_message = '<a href="javascript://" onClick="chng_focus(\'#msg_num_'.$m_num.'\');"><img alt="Go to previous message" title="Go to previous message" src="theme/default/images/up.png" width=16 height=11 /></a>';
			}

			/* handle next link */
			if ($obj->id < $obj->last_post_id) {
				if ($m_num && !($misc[1] - $m_num - 1)) { /* next page link */
					$next_message = '<a href="index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.($misc[0] + $misc[1]).reveal_lnk.unignore_tmp.'"><img alt="Go to previous message" title="Go to next message" src="theme/default/images/down.png" width=16 height=11 /></a>';
					$next_page = '<a href="index.php?t='.$_GET['t'].'&amp;'._rsid.'&amp;prevloaded=1&amp;th='.$obj->thread_id.'&amp;start='.($misc[0] + $misc[1]).reveal_lnk.unignore_tmp.'">Next Page <img src="theme/default/images/goto.gif" alt="" /></a>';
				} else {
					$next_message = '<a href="javascript://" onClick="chng_focus(\'#msg_num_'.($m_num + 2).'\');"><img alt="Go to next message" title="Go to next message" src="theme/default/images/down.png" width=16 height=11 /></a>';
				}
			}
		}
		++$m_num;
	}

	$user_login = $obj->user_id ? $obj->login : $GLOBALS['ANON_NICK'];

	/* check if the message should be ignored and it is not temporarily revelead */
	if ($usr->ignore_list && !empty($usr->ignore_list[$obj->poster_id]) && !isset($GLOBALS['__FMDSP__'][$obj->id])) {
		return !$hide_controls ? '<tr><td><table border=0 cellspacing=0 cellpadding=0 class="MsgTable"><tr><td class="MsgIg al">
<a name="msg_num_'.$m_num.'"></a>
<a name="msg_'.$obj->id.'"></a>
'.($obj->user_id ? 'Message by <a href="index.php?t=usrinfo&amp;'._rsid.'&amp;id='.$obj->user_id.'">'.$obj->login.'</a> is ignored' : $GLOBALS['ANON_NICK'].' is ignored' )  .'&nbsp;
[<a href="index.php?'. make_reveal_link($obj->id).'">reveal message</a>]&nbsp;
[<a href="index.php?'.make_tmp_unignore_lnk($obj->poster_id).'">reveal all messages by '.$user_login.'</a>]&nbsp;
[<a href="index.php?t=ignore_list&amp;del='.$obj->poster_id.'&amp;redr=1&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">stop ignoring this user</a>]</td>
<td class="MsgIg" align="right">'.$prev_message.$next_message.'</td></tr>
</table></td></tr>' : '<tr class="MsgR1 GenText">
<td><a name="msg_num_'.$m_num.'"></a> <a name="msg_'.$obj->id.'"></a>Post by '.$user_login.' is ignored&nbsp;</td>
</tr>';
	}

	if ($obj->user_id) {
		if (!$hide_controls) {
			$custom_tag = $obj->custom_status ? '<br />'.$obj->custom_status : '';
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
				$online_indicator = (($obj->time_sec + $GLOBALS['LOGEDIN_TIMEOUT'] * 60) > __request_timestamp__) ? '<img src="theme/default/images/online.png" alt="'.$obj->login.' is currently online" title="'.$obj->login.' is currently online" />&nbsp;' : '<img src="theme/default/images/offline.png" alt="'.$obj->login.'  is currently offline" title="'.$obj->login.'  is currently offline" />&nbsp;';
			} else {
				$online_indicator = '';
			}

			$user_link = '<a href="index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'">'.$user_login.'</a>';

			$location = $obj->location ? '<br /><b>Location: </b>'.(strlen($obj->location) > $GLOBALS['MAX_LOCATION_SHOW'] ? substr($obj->location, 0, $GLOBALS['MAX_LOCATION_SHOW']) . '...' : $obj->location) : '';

			if (_uid && _uid != $obj->user_id) {
				$buddy_link	= !isset($usr->buddy_list[$obj->user_id]) ? '<a href="index.php?t=buddy_list&amp;add='.$obj->user_id.'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">add to buddy list</a><br />' : '<a href="index.php?t=buddy_list&amp;del='.$obj->user_id.'&amp;redr=1&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">remove from buddy list</a><br />';
				$ignore_link	= !isset($usr->ignore_list[$obj->user_id]) ? '<a href="index.php?t=ignore_list&amp;add='.$obj->user_id.'&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">ignore all messages by this user</a>' : '<a href="index.php?t=ignore_list&amp;del='.$obj->user_id.'&amp;redr=1&amp;'._rsid.'&amp;SQ='.$GLOBALS['sq'].'">stop ignoring messages by this user</a>';
				$dmsg_bd_il	= $buddy_link.$ignore_link.'<br />';
			} else {
				$dmsg_bd_il = '';
			}

			/* show im buttons if need be */
			if ($b & 16384) {
				$im_icq		= $obj->icq ? '<a href="index.php?t=usrinfo&amp;id='.$obj->poster_id.'&amp;'._rsid.'#icq_msg"><img title="'.$obj->icq.'" src="theme/default/images/icq.png" alt="" /></a>' : '';
				$im_aim		= $obj->aim ? '<a href="aim:goim?screenname='.$obj->aim.'&amp;message=Hi.+Are+you+there?" target="_blank"><img alt="" src="theme/default/images/aim.png" title="'.$obj->aim.'" /></a>' : '';
				$im_yahoo	= $obj->yahoo ? '<a target="_blank" href="http://edit.yahoo.com/config/send_webmesg?.target='.$obj->yahoo.'&amp;.src=pg"><img alt="" src="theme/default/images/yahoo.png" title="'.$obj->yahoo.'" /></a>' : '';
				$im_msnm	= $obj->msnm ? '<a href="mailto: '.$obj->msnm.'"><img alt="" src="theme/default/images/msnm.png" title="'.$obj->msnm.'" /></a>' : '';
				$im_jabber	= $obj->jabber ? '<img src="theme/default/images/jabber.png" title="'.$obj->jabber.'" alt="" />' : '';
				if ($o2 & 2048) {
					$im_affero = $obj->affero ? '<a href="http://svcs.affero.net/rm.php?r='.$obj->affero.'&amp;ll='.$obj->forum_id.'.'.$GLOBALS['affero_domain'].'&amp;lp='.$obj->forum_id.'.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="theme/default/images/affero_reg.gif" /></a>' : '<a href="http://svcs.affero.net/rm.php?m='.urlencode($obj->email).'&amp;ll='.$obj->forum_id.'.'.$GLOBALS['affero_domain'].'&amp;lp='.$obj->forum_id.'.'.urlencode($GLOBALS['affero_domain']['host']).'&amp;ls='.urlencode($obj->subject).'" target=_blank><img alt="" src="theme/default/images/affero_noreg.gif" /></a>';
				} else {
					$im_affero = '';
				}
				$dmsg_im_row = ($im_icq || $im_aim || $im_yahoo || $im_msnm || $im_jabber || $im_affero) ? $im_icq.' '.$im_aim.' '.$im_yahoo.' '.$im_msnm.' '.$im_jabber.' '.$im_affero.'<br />' : '';
			} else {
				$dmsg_im_row = '';
			}
		 } else {
		 	$user_link = $user_login;
		 	$dmsg_tags = $dmsg_im_row = $dmsg_bd_il = $location = $online_indicator = $avatar = '';
		 }
	} else {
		$user_link = $user_login;
		$dmsg_tags = $dmsg_im_row = $dmsg_bd_il = $location = $online_indicator = $avatar = '';
	}

	/* Display message body
	 * If we have message threshold & the entirity of the post has been revelead show a preview
	 * otherwise if the message body exists show an actual body
	 * if there is no body show a 'no-body' message
	 */
	if (!$hide_controls && $obj->message_threshold && $obj->length_preview && $obj->length > $obj->message_threshold && !isset($GLOBALS['__FMDSP__'][$obj->id])) {
		$msg_body = '<span class="MsgBodyText">'.read_msg_body($obj->offset_preview, $obj->length_preview, $obj->file_id_preview).'</span>
<br /><div class="ac">[<a href="index.php?'.make_reveal_link($obj->id).'">Show the rest of the message</a>]</div>';
	} else if ($obj->length) {
		$msg_body = '<span class="MsgBodyText">'.read_msg_body($obj->foff, $obj->length, $obj->file_id).'</span>';
	} else {
		$msg_body = 'No Message Body';
	}

	/* draw file attachments if there are any */
	$drawmsg_file_attachments = '';
	if ($obj->attach_cnt && !empty($obj->attach_cache)) {
		$atch = unserialize($obj->attach_cache);
		if (!empty($atch)) {
			foreach ($atch as $v) {
				$sz = $v[2] / 1024;
				$drawmsg_file_attachments .= '<li />
<a href="index.php?t=getfile&amp;id='.$v[0].'&amp;'._rsid.'"><img alt="" src="images/mime/'.$v[4].'" class="at" /></a>
<span class="GenText fb">Attachment:</span> <a href="index.php?t=getfile&amp;id='.$v[0].'&amp;'._rsid.'">'.$v[1].'</a><br />
<span class="SmallText">(Size: '.($sz < 1000 ? number_format($sz, 2).'KB' : number_format($sz/1024, 2).'MB').', Downloaded '.$v[3].' time(s))<p /></span>';
			}
			$drawmsg_file_attachments = '<p />
<ul class="AttachmentsList">
'.$drawmsg_file_attachments.'
</ul>';
		}
		/* append session to getfile */
		if (_uid) {
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

	if ($obj->poll_cache) {
		$obj->poll_cache = unserialize($obj->poll_cache);
	}

	/* handle poll votes */
	if (!empty($_POST['poll_opt']) && ($_POST['poll_opt'] = (int)$_POST['poll_opt']) && !($obj->thread_opt & 1) && $perms & 512) {
		if (register_vote($obj->poll_cache, $obj->poll_id, $_POST['poll_opt'], $obj->id)) {
			$obj->total_votes += 1;
			$obj->cant_vote = 1;
		}
		unset($_GET['poll_opt']);
	}

	/* display poll if there is one */
	if ($obj->poll_id && $obj->poll_cache) {
		/* we need to determine if we allow the user to vote or see poll results */
		$show_res = 1;

		if (isset($_GET['pl_view']) && !isset($_POST['pl_view'])) {
			$_POST['pl_view'] = $_GET['pl_view'];
		}

		/* various conditions that may prevent poll voting */
		if (!$hide_controls && !$obj->cant_vote &&
			(!isset($_POST['pl_view']) || $_POST['pl_view'] != $obj->poll_id) &&
			($perms & 512 && (!($obj->thread_opt & 1) || $perms & 4096)) &&
			(!$obj->expiry_date || ($obj->creation_date + $obj->expiry_date) > __request_timestamp__) &&
			/* check if the max # of poll votes was reached */
			(!$obj->max_votes || $obj->total_votes < $obj->max_votes)
		) {
			$show_res = 0;
		}

		$i = 0;

		$poll_data = '';
		foreach ($obj->poll_cache as $k => $v) {
			++$i;
			if ($show_res) {
				$length = ($v[1] && $obj->total_votes) ? round($v[1] / $obj->total_votes * 100) : 0;
				$poll_data .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td>'.$v[0].'</td><td><img src="theme/default/images/poll_pix.gif" alt="" height="10" width="'.$length.'" /> '.$v[1].' / '.$length.'%</td></tr>';
			} else {
				$poll_data .= '<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').'"><td>'.$i.'.</td><td colspan=2><input type="radio" name="poll_opt" value="'.$k.'">&nbsp;&nbsp;'.$v[0].'</td></tr>';
			}
		}

		if (!$show_res) {
			$poll = '<p>
<form action="index.php?'.$_SERVER['QUERY_STRING'].'#msg_'.$obj->id.'" method="post">'._hs.'
<table cellspacing=1 cellpadding=2 class="PollTable">
<tr><th class="nw" colspan=3>'.$obj->poll_name.'<span class="ptp">[ '.$obj->total_votes.' vote(s) ]</span></th></tr>
'.$poll_data.'
<tr class="'.alt_var('msg_poll_alt_clr','RowStyleB','RowStyleA').' ar"><td colspan=3><input type="submit" class="button" name="pl_vote" value="Vote">&nbsp;'.($obj->total_votes ? '<input type="submit" class="button" name="pl_res" value="View Results">' : '' )  .'</td></tr>
</table><input type="hidden" name="pl_view" value="'.$obj->poll_id.'"></form><p>';
		} else {
			$poll = '<p><table cellspacing=1 cellpadding=2 class="PollTable">
<tr><th class="nw" colspan=3>'.$obj->poll_name.'<span class="vt">[ '.$obj->total_votes.' vote(s) ]</span></th></tr>
'.$poll_data.'
</table><p>';
		}

		if (($p = strpos($msg_body, '{POLL}')) !== false) {
			$msg_body = substr_replace($msg_body, $poll, $p, 6);
		} else {
			$msg_body = $poll . $msg_body;
		}
	}

	/* Determine if the message was updated and if this needs to be shown */
	if ($obj->update_stamp) {
		if ($obj->updated_by != $obj->poster_id && $o1 & 67108864) {
			$modified_message = '<p>[Updated on: '.strftime("%a, %d %B %Y %H:%M", $obj->update_stamp).'] by Moderator';
		} else if ($obj->updated_by == $obj->poster_id && $o1 & 33554432) {
			$modified_message = '<p>[Updated on: '.strftime("%a, %d %B %Y %H:%M", $obj->update_stamp).']';
		} else {
			$modified_message = '';
		}
	} else {
		$modified_message = '';
	}

	$rpl = '';
	if (!$hide_controls) {
		if ($obj->reply_to && $obj->reply_to != $obj->id && $o2 & 536870912) {
			if ($_GET['t'] != 'tree' && $_GET['t'] != 'msg') {
				$lnk = d_thread_view;
			} else {
				$lnk =& $_GET['t'];
			}
			$rpl = '<span class="small"> [message #'.$obj->id.' <a href="index.php?t='.$lnk.'&amp;'._rsid.'&amp;th='.$obj->thread_id.'&amp;goto='.$obj->reply_to.'#msg_'.$obj->reply_to.'" class="small">is a reply to message #'.$obj->reply_to.'</a> ]</span>';
		} else {
			$rpl = '<span class="small"> [message #'.$obj->id.']</span>';
		}

		/* little trick, this variable will only be available if we have a next link leading to another page */
		if (empty($next_page)) {
			$next_page = '&nbsp;';
		}

		if (_uid && ($perms & 16 || (_uid == $obj->poster_id && (!$GLOBALS['EDIT_TIME_LIMIT'] || __request_timestamp__ - $obj->post_stamp < $GLOBALS['EDIT_TIME_LIMIT'] * 60)))) {
			$edit_link = '<a href="index.php?t=post&amp;msg_id='.$obj->id.'&amp;'._rsid.'"><img alt="" src="theme/default/images/msg_edit.gif" /></a>&nbsp;&nbsp;&nbsp;&nbsp;';
		} else {
			$edit_link = '';
		}

		if (!($obj->thread_opt & 1) || $perms & 4096) {
			$reply_link = '<a href="index.php?t=post&amp;reply_to='.$obj->id.'&amp;'._rsid.'"><img alt="" src="theme/default/images/msg_reply.gif" /></a>&nbsp;';
			$quote_link = '<a href="index.php?t=post&amp;reply_to='.$obj->id.'&amp;quote=true&amp;'._rsid.'"><img alt="" src="theme/default/images/msg_quote.gif" /></a>';
		} else {
			$reply_link = $quote_link = '';
		}
	}

	return '<tr><td class="MsgSpacer"><table cellspacing=0 cellpadding=0 class="MsgTable">
<tr>
<td class="MsgR1 vt al MsgSubText"><a name="msg_num_'.$m_num.'"></a><a name="msg_'.$obj->id.'"></a>'.($obj->icon && !$hide_controls ? '<img src="images/message_icons/'.$obj->icon.'" alt="'.$obj->icon.'" />&nbsp;&nbsp;' : '' )  .$obj->subject.$rpl.'</td>
<td class="MsgR1 vt ar"><span class="DateText">'.strftime("%a, %d %B %Y %H:%M", $obj->post_stamp).'</span> '.$prev_message.$next_message.'</td>
</tr>
<tr class="MsgR2"><td class="MsgR2" colspan=2><table cellspacing="0" cellpadding="0" class="ContentTable">
<tr class="MsgR2">
'.$avatar.'
<td class="msgud">'.$online_indicator.$user_link.(!$hide_controls ? ($obj->user_id ? '<br /><b>Messages:</b> '.$obj->posted_msg_count.'<br /><b>Registered:</b> '.strftime("%B %Y", $obj->join_date).' '.$location : '' )   : '' )  .'</td>
<td class="msgud">'.$dmsg_tags.'</td>
<td class="msgot">'.$dmsg_bd_il.$dmsg_im_row.(!$hide_controls ? (($obj->host_name && $o1 & 268435456) ? '<b>From:</b> '.$obj->host_name.'<br />' : '' )  .(($b & 1048576 || $usr->md || $o1 & 134217728) ? '<b>IP:</b> <a href="index.php?t=ip&amp;ip='.$obj->ip_addr.'&amp;'._rsid.'" target="_blank">'.$obj->ip_addr.'</a>' : '' )   : '' )  .'</td>
</tr></table></td>
</tr>
<tr><td colspan="2" class="MsgR3">
'.$msg_body.$drawmsg_file_attachments.'
'.$modified_message.(!$hide_controls ? (($obj->sig && $o1 & 32768 && $obj->msg_opt & 1 && $b & 4096 && !($a & 67108864)) ? '<p /><hr class="sig" />'.$obj->sig : '' )  .'<div class="ar"><a href="index.php?t=report&amp;msg_id='.$obj->id.'&amp;'._rsid.'">Report message to a moderator</a></div>' : '' )  .'
</td></tr>
'.(!$hide_controls ? '<tr><td colspan="2" class="MsgToolBar"><table border=0 cellspacing=0 cellpadding=0 class="wa"><tr>
<td class="al nw">'.($obj->user_id ? '<a href="index.php?t=usrinfo&amp;id='.$obj->user_id.'&amp;'._rsid.'"><img alt="" src="theme/default/images/msg_about.gif" /></a>&nbsp;'.(($o1 & 4194304 && $a & 16) ? '<a href="index.php?t=email&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img alt="" src="theme/default/images/msg_email.gif" /></a>&nbsp;' : '' )  .($o1 & 1024 ? '<a href="index.php?t=ppost&amp;toi='.$obj->user_id.'&amp;'._rsid.'"><img alt="Send a private message to this user" title="Send a private message to this user" src="theme/default/images/msg_pm.gif" /></a>' : '' )   : '' )  .'</td>
<td class="GenText wa ac">'.$next_page.'</td>
<td class="nw ar">'.($perms & 32 ? '<a href="index.php?t=mmod&amp;del='.$obj->id.'&amp;'._rsid.'"><img alt="" src="theme/default/images/msg_delete.gif" /></a>&nbsp;' : '' )  .$edit_link.$reply_link.$quote_link.'</td>
</tr></table></td></tr>' : '' )  .'
</table></td></tr>';
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
}function msg_get($id)
{
	if (($r = db_sab('SELECT * FROM fud26_msg WHERE id='.$id))) {
		$r->body = read_msg_body($r->foff, $r->length, $r->file_id);
		return $r;
	}
	error_dialog('Invalid Message', 'The message you are trying to view does not exist.');
}

function poll_cache_rebuild($poll_id, &$data)
{
	if (!$poll_id) {
		$data = null;
		return;
	}

	if (!$data) { /* rebuild from cratch */
		$c = uq('SELECT id, name, count FROM fud26_poll_opt WHERE poll_id='.$poll_id);
		while ($r = db_rowarr($c)) {
			$data[$r[0]] = array($r[1], $r[2]);
		}
		if (!$data) {
			$data = null;
		}
	} else { /* register single vote */
		$data[$poll_id][1] += 1;
	}
}function is_notified($user_id, $thread_id)
{
	return q_singleval('SELECT * FROM fud26_thread_notify WHERE thread_id='.$thread_id.' AND user_id='.$user_id);
}

function thread_notify_add($user_id, $thread_id)
{
	db_li('INSERT INTO fud26_thread_notify (user_id, thread_id) VALUES ('.$user_id.', '.$thread_id.')', $ret);
}

function thread_notify_del($user_id, $thread_id)
{
	q('DELETE FROM fud26_thread_notify WHERE thread_id='.$thread_id.' AND user_id='.$user_id);
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
}function get_prev_next_th_id(&$frm, &$prev, &$next)
{
	/* determine previous thread */
	if ($frm->th_page == 1 && $frm->th_pos == 1) {
		$prev = '';
	} else {
		if ($frm->th_pos - 1 == 0) {
			$page = $frm->th_page - 1;
			$pos = $GLOBALS['THREADS_PER_PAGE'];
		} else {
			$page = $frm->th_page;
			$pos = $frm->th_pos - 1;
		}

		$p = db_saq('SELECT m.id, m.subject FROM fud26_thread_view tv INNER JOIN fud26_thread t ON tv.thread_id=t.id INNER JOIN fud26_msg m ON t.root_msg_id=m.id WHERE tv.forum_id='.$frm->forum_id.' AND tv.page='.$page.' AND tv.pos='.$pos);

		$prev = $p ? '<tr><td class="ar GenText">Previous Topic:</td><td class="GenText al"><a href="index.php?t='.$_GET['t'].'&amp;goto='.$p[0].'&amp;'._rsid.'#msg_'.$p[0].'">'.$p[1].'</a></td></tr>' : '';
	}

	/* determine next thread */
	if ($frm->th_pos + 1 > $GLOBALS['THREADS_PER_PAGE']) {
		$page = $frm->th_page + 1;
		$pos = 1;
	} else {
		$page = $frm->th_page;
		$pos = $frm->th_pos + 1;
	}

	$n = db_saq('SELECT m.id, m.subject FROM fud26_thread_view tv INNER JOIN fud26_thread t ON tv.thread_id=t.id INNER JOIN fud26_msg m ON t.root_msg_id=m.id WHERE tv.forum_id='.$frm->forum_id.' AND tv.page='.$page.' AND tv.pos='.$pos);

	$next = $n ? '<tr><td class="GenText ar">Next Topic:</td><td class="GenText al"><a href="index.php?t='.$_GET['t'].'&amp;goto='.$n[0].'&amp;'._rsid.'#msg_'.$n[0].'">'.$n[1].'</a></td></tr>' : '';
}function th_lock($id, $lck)
{
	q("UPDATE fud26_thread SET thread_opt=(thread_opt|1)".(!$lck ? '& ~ 1' : '')." WHERE id=".$id);
}

function th_inc_view_count($id)
{
	q('UPDATE fud26_thread SET views=views+1 WHERE id='.$id);
}

function th_inc_post_count($id, $r, $lpi=0, $lpd=0)
{
	if ($lpi && $lpd) {
		q('UPDATE fud26_thread SET replies=replies+'.$r.', last_post_id='.$lpi.', last_post_date='.$lpd.' WHERE id='.$id);
	} else {
		q('UPDATE fud26_thread SET replies=replies+'.$r.' WHERE id='.$id);
	}
}

function th_frm_last_post_id($id, $th)
{
	return (int) q_singleval('SELECT fud26_thread.last_post_id FROM fud26_thread INNER JOIN fud26_msg ON fud26_thread.root_msg_id=fud26_msg.id WHERE fud26_thread.forum_id='.$id.' AND fud26_thread.id!='.$th.' AND fud26_thread.moved_to=0 AND fud26_msg.apr=1 ORDER BY fud26_thread.last_post_date DESC LIMIT 1');
}

	if ($FUD_OPT_3 & 2) {
		std_error('disabled');
	}

	if (!isset($_GET['th']) || !($th = (int)$_GET['th'])) {
		$th = 0;
	}
	if (!isset($_GET['mid']) || !($mid = (int)$_GET['mid'])) {
		$mid = 0;
	}

	if (isset($_GET['goto'])) {
		if (($mid = (int)$_GET['goto']) && !$th) {
			$th = q_singleval('SELECT thread_id FROM fud26_msg WHERE id='.$mid);
		} else if ($_GET['goto'] == 'end' && $th) {
			$mid = q_singleval('SELECT last_post_id FROM fud26_thread WHERE id='.$th);
		} else if ($th) {
			$mid = (int)$_GET['goto'];
		} else {
			invl_inp_err();
		}
	}
	if (!$th) {
		invl_inp_err();
	}
	if (!$mid && isset($_GET['unread']) && _uid) {
		$mid = q_singleval('SELECT m.id FROM fud26_msg m LEFT JOIN fud26_read r ON r.thread_id=m.thread_id AND r.user_id='._uid.' WHERE m.thread_id='.$th.' AND m.apr=1 AND m.post_stamp > r.last_view AND m.post_stamp > '.$usr->last_read.' ORDER BY m.post_stamp ASC LIMIT 1');
		if (!$mid) {
			$mid = q_singleval('SELECT root_msg_id FROM fud26_thread WHERE id='.$th);
		}
	}

	/* we create a BIG object frm, which contains data about forum,
	 * category, current thread, subscriptions, permissions, moderation status,
	 * rating possibilites and if we will need to update last_view field for registered user
	 */
	make_perms_query($fields, $join);

	$frm = db_sab('SELECT
			c.id AS cat_id,
			f.name,
			m.subject,
			t.id, t.forum_id, t.replies, t.rating, t.n_rating, t.root_msg_id, t.moved_to, t.thread_opt, t.root_msg_id, t.last_post_date, '.
			(_uid ? ' tn.thread_id AS subscribed, mo.forum_id AS md, tr.thread_id AS cant_rate, r.last_view, r2.last_view AS last_forum_view, ' : ' 0 AS md, 1 AS cant_rate, ').'
			tv.pos AS th_pos, tv.page AS th_page,
			m2.thread_id AS last_thread,
			'.$fields.'
		FROM fud26_thread t
			INNER JOIN fud26_msg		m ON m.id=t.root_msg_id
			INNER JOIN fud26_forum		f ON f.id=t.forum_id
			INNER JOIN fud26_cat		c ON f.cat_id=c.id
			INNER JOIN fud26_thread_view	tv ON tv.forum_id=t.forum_id AND tv.thread_id=t.id
			INNER JOIN fud26_msg 		m2 ON f.last_post_id=m2.id
			'.(_uid ? 'LEFT  JOIN fud26_thread_notify 	tn ON tn.user_id='._uid.' AND tn.thread_id='.$th.'
			LEFT  JOIN fud26_mod 		mo ON mo.user_id='._uid.' AND mo.forum_id=t.forum_id
			LEFT  JOIN fud26_thread_rate_track 	tr ON tr.thread_id='.$th.' AND tr.user_id='._uid.'
			LEFT  JOIN fud26_read 		r ON r.thread_id=t.id AND r.user_id='._uid.'
			LEFT  JOIN fud26_forum_read 	r2 ON r2.forum_id=t.forum_id AND r2.user_id='._uid : '')
			.$join.'
		WHERE t.id='.$th);

	if (!$frm) { /* bad thread, terminate request */
		invl_inp_err();
	}

	if ($frm->moved_to) { /* moved thread, we could handle it, but this case is rather rare, so it's cleaner to redirect */
		if ($FUD_OPT_2 & 32768) {
			header('Location: http://timeweather.net/forum/index.php/mv/tree/'.$frm->root_msg_id.'/'._rsidl.'#msg_'.$frm->root_msg_id);
		} else {
			header('Location: http://timeweather.net/forum/index.php?t=tree&goto='.$frm->root_msg_id.'&'._rsidl.'#msg_'.$frm->root_msg_id);
		}
		exit;
	}

	$perms = perms_from_obj($frm, $is_a);

	if (!($perms & 2)) {
		if (!isset($_GET['logoff'])) {
			std_error('login');
		}
		if ($FUD_OPT_2 & 32768) {
			header('Location: http://timeweather.net/forum/index.php/i/' . _rsidl);
		} else {
			header('Location: http://timeweather.net/forum/index.php?t=index&' . _rsidl);
		}
		exit;
	}

	if (_uid) {
		/* Deal with thread subscriptions */
		if (isset($_GET['notify'], $_GET['opt']) && sq_check(0, $usr->sq)) {
			if (($frm->subscribed = ($_GET['opt'] == 'on'))) {
				thread_notify_add(_uid, $_GET['th']);
			} else {
				thread_notify_del(_uid, $_GET['th']);
			}
		}
		$subscribe_status = $frm->subscribed ? '| <a href="index.php?t=tree&amp;th='.$th.'&amp;notify='.$usr->id.'&amp;'._rsid.'&amp;opt=off&amp;mid='.$mid.'&amp;SQ='.$GLOBALS['sq'].'" title="Stop receiving notifications about new messages in this topic">Unsubscribe from topic</a>&nbsp;' : '| <a href="index.php?t=tree&amp;th='.$th.'&amp;notify='.$usr->id.'&amp;'._rsid.'&amp;opt=on&amp;mid='.$mid.'&amp;SQ='.$GLOBALS['sq'].'" title="Receive notification about new messages inside this topic">Subscribe to topic</a>&nbsp;';
	} else {
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", $frm->last_post_date) . " GMT");
		$subscribe_status = '';
	}

	if (!$mid) {
		$mid = $frm->root_msg_id;
	}

	$msg_obj = db_sab('SELECT
		m.*,
		t.thread_opt, t.root_msg_id, t.last_post_id, t.forum_id,
		f.message_threshold,
		u.id AS user_id, u.alias AS login, u.avatar_loc, u.email, u.posted_msg_count, u.join_date, u.location,
		u.sig, u.custom_status, u.icq, u.jabber, u.affero, u.aim, u.msnm, u.yahoo, u.last_visit AS time_sec, u.users_opt,
		l.name AS level_name, l.level_opt, l.img AS level_img,
		p.max_votes, p.expiry_date, p.creation_date, p.name AS poll_name, p.total_votes,
		'.(_uid ? ' pot.id AS cant_vote ' : ' 1 AS cant_vote ').'
	FROM
		fud26_msg m
		INNER JOIN fud26_thread t ON m.thread_id=t.id
		INNER JOIN fud26_forum f ON t.forum_id=f.id
		LEFT JOIN fud26_users u ON m.poster_id=u.id
		LEFT JOIN fud26_level l ON u.level_id=l.id
		LEFT JOIN fud26_poll p ON m.poll_id=p.id'.
		(_uid ? ' LEFT JOIN fud26_poll_opt_track pot ON pot.poll_id=p.id AND pot.user_id='._uid : ' ').'
	WHERE
		m.id='.$mid.' AND m.apr=1');

	if (!isset($_GET['prevloaded'])) {
		th_inc_view_count($th);
		if (_uid) {
			if ($frm->last_view < $msg_obj->post_stamp) {
				user_register_thread_view($th, $msg_obj->post_stamp, $mid);
			}
			if ($frm->last_forum_view < $msg_obj->post_stamp) {
				user_register_forum_view($frm->forum_id);
			}
		}
	}
	ses_update_status($usr->sid, 'Browsing topic (tree view) <a href="index.php?t=tree&amp;th='.$frm->id.'&amp;'._rsid.'">'.$frm->subject.'</a>', $frm->id);

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
	}function tmpl_create_forum_select($frm_id, $mod)
{
	if (!isset($_GET['t']) || ($_GET['t'] != 'thread' && $_GET['t'] != 'threadt')) {
		$dest = t_thread_view;
	} else {
		$dest = $_GET['t'];
	}

	if ($mod) { /* admin optimization */
		$c = uq('SELECT f.id, f.name, c.id FROM fud26_fc_view v INNER JOIN fud26_forum f ON f.id=v.f INNER JOIN fud26_cat c ON f.cat_id=c.id ORDER BY v.id');
	} else {
		$c = uq('SELECT f.id, f.name, c.id
			FROM fud26_fc_view v
			INNER JOIN fud26_forum f ON f.id=v.f
			INNER JOIN fud26_cat c ON f.cat_id=c.id
			INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id ' .
			(_uid ? ' LEFT JOIN fud26_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.' LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id WHERE mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 1) > 0 '  : ' WHERE (g1.group_cache_opt & 1) > 0 ').
			'ORDER BY v.id');
	}
	$f = array($frm_id => 1);

	$oldc = $selection_options = '';
	while ($r = db_rowarr($c)) {
		if ($oldc != $r[2]) {
			while (list($k, $i) = each($GLOBALS['cat_cache'])) {
				$selection_options .= '<option value="0">- '.($tabw = ($i[0] ? str_repeat('&nbsp;&nbsp;&nbsp;', $i[0]) : '')).$i[1].'</option>';
				if ($k == $r[2]) {
					break;
				}
			}
			$oldc = $r[2];
		}
		$selection_options .= '<option value="'.$r[0].'"'.(isset($f[$r[0]]) ? ' selected' : '').'>'.$tabw.'&nbsp;&nbsp;'.$r[1].'</option>';
	}
	
	return '<span class="SmallText fb">Goto Forum:</span>
<form action="index.php" name="frmquicksel" method="get" onSubmit="javascript: if (document.frmquicksel.frm_id.value < 1) document.frmquicksel.frm_id.value='.$frm_id.';">
<input type="hidden" name="t" value="'.$dest.'">'._hs.'<input type="hidden" name="forum_redr" value="1">
<select class="SmallText" name="frm_id" onChange="javascript: if ( this.value==0 ) return false; document.frmquicksel.submit();">
'.$selection_options.'
</select>&nbsp;&nbsp;<input type="submit" class="button" name="frm_goto" value="Go" ></form>';
}if (!isset($th)) {
	$th = 0;
}
if (!isset($frm->id)) {
	$frm->id = 0;
}require $GLOBALS['FORUM_SETTINGS_PATH'].'cat_cache.inc';

function draw_forum_path($cid, $fn='', $fid=0, $tn='')
{
	global $cat_par, $cat_cache;

	$data = '';
	do {
		$data = '&nbsp;&raquo; <a href="index.php?t=i&amp;cat='.$cid.'&amp;'._rsid.'">'.$cat_cache[$cid][1].'</a>' . $data;
	} while (($cid = $cat_par[$cid]) > 0);

	if ($fid) {
		$data .= '&nbsp;&raquo; <a href="index.php?t='.t_thread_view.'&amp;frm_id='.$fid.'&amp;'._rsid.'">'.$fn.'</a>';
	} else if ($fn) {
		$data .= '&nbsp;&raquo; <b>'.$fn.'</b>';
	}

	return '<a href="index.php?t=i&amp;'._rsid.'">Home</a>'.$data.($tn ? '&nbsp;&raquo; <b>'.$tn.'</b>' : '');
}

	$TITLE_EXTRA = ': '.$frm->name.' =&gt; '.$frm->subject;

	if ($FUD_OPT_2 & 4096) {
		$thread_rating = $frm->rating ? '&nbsp;(<img src="theme/default/images/'.$frm->rating.'stars.gif" alt="'.$frm->rating.'" />) '.$frm->n_rating.' Vote(s)' : '';
		$rate_thread = ($perms & 1024 && !$frm->cant_rate) ? '<table border=0 cellspacing=0 cellpadding=0><tr><form action="index.php?t=ratethread" name="vote_frm" method="post"><td nowrap>
<select name="sel_vote" onChange="javascript: if ( !this.value ) return false; document.vote_frm.submit();">
<option>Rate Topic</option>
<option value="1">1 Worst</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5 Best</option>
</select>
</td><td>&nbsp;<input type="submit" class="button" name="btn_vote" value="Vote">
<input type="hidden" name="rate_thread_id" value="'.$frm->id.'">'._hs.'
</td></form></tr></table>' : '';
	} else {
		$rate_thread = $thread_rating = '';
	}

	if ($perms & 4096) {
		$lock_thread = !($frm->thread_opt & 1) ? '<a href="index.php?t=mmod&amp;'._rsid.'&amp;th='.$th.'&amp;lock=1&amp;SQ='.$GLOBALS['sq'].'">Lock Topic</a>&nbsp;|&nbsp;' : '<a href="index.php?t=mmod&amp;'._rsid.'&amp;th='.$th.'&amp;unlock=1&amp;SQ='.$GLOBALS['sq'].'">Unlock Topic</a>&nbsp;|&nbsp;';
	} else {
		$lock_thread = '';
	}

	$tree = $stack = $arr = null;
	$c = uq('SELECT m.poster_id, m.subject, m.reply_to, m.id, m.poll_id, m.attach_cnt, m.post_stamp, u.alias, u.last_visit FROM fud26_msg m INNER JOIN fud26_thread t ON m.thread_id=t.id LEFT JOIN fud26_users u ON m.poster_id=u.id WHERE m.thread_id='.$th.' AND m.apr=1 ORDER BY m.id');
	error_reporting(0);
	while ($r = db_rowobj($c)) {
		$arr[$r->id] = $r;
		$arr[$r->reply_to]->kiddie_count++;
		$arr[$r->reply_to]->kiddies[] = &$arr[$r->id];

		if ($r->reply_to == 0) {
			$tree->kiddie_count++;
			$tree->kiddies[] = &$arr[$r->id];
		}
	}
	error_reporting(2047);

	$prev_msg = $next_msg = 0;
	$rev = isset($_GET['rev']) ? $_GET['rev'] : '';
	$reveal = isset($_GET['reveal']) ? $_GET['reveal'] : '';
	$tree_data = '';

	if($arr) {
		reset($tree->kiddies);
		$stack[0] = &$tree;
		$stack_cnt = isset($tree->kiddie_count) ? $tree->kiddie_count : 0;
		$j = $lev = $prev_id = 0;

		while ($stack_cnt > 0) {
			$cur = &$stack[$stack_cnt-1];

			if (isset($cur->subject) && empty($cur->sub_shown)) {
				if (isset($cur->kiddies) && $cur->kiddie_count) {
					$tree_data .= $cur->id == $mid ? '<tr class="RowStyleC">
<td>'.((_uid && $cur->post_stamp > $usr->last_read && $cur->post_stamp > $frm->last_view) ? '<img src="theme/default/images/unread.png" alt="Unread Message" title="Unread Message" />' : '<img src="theme/default/images/read.png" alt="Read Message" title="Read Message" />' ) .'</td>
<td class="Gentext nw wa vt" style="padding-left: '.(15 * ($lev - 1)).'px">
<a href="index.php?t=tree&amp;th='.$th.'&amp;mid='.$cur->id.'&amp;'._rsid.'&amp;rev='.$rev.'&amp;reveal='.$reveal.'" class="big">'.$cur->subject.'</a><a name="tree_view">&nbsp;</a>
<div class="TopBy">By: '.($cur->poster_id ? '<a href="index.php?t=usrinfo&amp;id='.$cur->poster_id.'&amp;'._rsid.'">'.$cur->alias.'</a>' : $GLOBALS['ANON_NICK'].'' ) .' on '.strftime("%a, %d %B %Y %H:%M", $cur->post_stamp).'</div></td>' : '<tr class="'.alt_var('tree_alt','RowStyleA','RowStyleB').'">
<td>'.((_uid && $cur->post_stamp > $usr->last_read && $cur->post_stamp > $frm->last_view) ? '<img src="theme/default/images/unread.png" alt="Unread Message" title="Unread Message" />' : '<img src="theme/default/images/read.png" alt="Read Message" title="Read Message" />' ) .'</td>
<td class="Gentext nw wa vt" style="padding-left: '.(15 * ($lev - 1)).'px">
<a href="index.php?t=tree&amp;th='.$th.'&amp;mid='.$cur->id.'&amp;'._rsid.'&amp;rev='.$rev.'&amp;reveal='.$reveal.'" class="big">'.$cur->subject.'</a>
<div class="TopBy">By: '.($cur->poster_id ? '<a href="index.php?t=usrinfo&amp;id='.$cur->poster_id.'&amp;'._rsid.'">'.$cur->alias.'</a>' : $GLOBALS['ANON_NICK'].'' ) .' on '.strftime("%a, %d %B %Y %H:%M", $cur->post_stamp).'</div></td>
</tr>';
				} else {
					$tree_data .= $cur->id == $mid ? '<tr class="RowStyleC">
<td>'.((_uid && $cur->post_stamp > $usr->last_read && $cur->post_stamp > $frm->last_view) ? '<img src="theme/default/images/unread.png" alt="Unread Message" title="Unread Message" />' : '<img src="theme/default/images/read.png" alt="Read Message" title="Read Message" />' ) .'</td>
<td class="Gentext nw wa vt" style="padding-left: '.(15 * ($lev - 1)).'px">
<a href="index.php?t=tree&amp;th='.$th.'&amp;mid='.$cur->id.'&amp;'._rsid.'&amp;rev='.$rev.'&amp;reveal='.$reveal.'" class="big">'.$cur->subject.'</a><a name="tree_view">&nbsp;</a>
<div class="TopBy">By: '.($cur->poster_id ? '<a href="index.php?t=usrinfo&amp;id='.$cur->poster_id.'&amp;'._rsid.'">'.$cur->alias.'</a>' : $GLOBALS['ANON_NICK'].'' ) .' on '.strftime("%a, %d %B %Y %H:%M", $cur->post_stamp).'</div></td>' : '<tr class="'.alt_var('tree_alt','RowStyleA','RowStyleB').'">
<td>'.((_uid && $cur->post_stamp > $usr->last_read && $cur->post_stamp > $frm->last_view) ? '<img src="theme/default/images/unread.png" alt="Unread Message" title="Unread Message" />' : '<img src="theme/default/images/read.png" alt="Read Message" title="Read Message" />' ) .'</td>
<td class="Gentext nw wa vt" style="padding-left: '.(15 * ($lev - 1)).'px">
<a href="index.php?t=tree&amp;th='.$th.'&amp;mid='.$cur->id.'&amp;'._rsid.'&amp;rev='.$rev.'&amp;reveal='.$reveal.'" class="big">'.$cur->subject.'</a>
<div class="TopBy">By: '.($cur->poster_id ? '<a href="index.php?t=usrinfo&amp;id='.$cur->poster_id.'&amp;'._rsid.'">'.$cur->alias.'</a>' : $GLOBALS['ANON_NICK'].'' ) .' on '.strftime("%a, %d %B %Y %H:%M", $cur->post_stamp).'</div></td>';
				}
				$cur->sub_shown = 1;

				if ($cur->id == $mid) {
					$prev_msg = $prev_id;
				}
				if ($prev_id == $mid) {
					$next_msg = $cur->id;
				}

				$prev_id = $cur->id;
			}

			if (!isset($cur->kiddie_count)) {
				$cur->kiddie_count = 0;
			}

			if ($cur->kiddie_count && isset($cur->kiddie_pos)) {
				++$cur->kiddie_pos;
			} else {
				$cur->kiddie_pos = 0;
			}

			if ($cur->kiddie_pos < $cur->kiddie_count) {
				++$lev;
				$stack[$stack_cnt++] = &$cur->kiddies[$cur->kiddie_pos];
			} else { // unwind the stack if needed
				unset($stack[--$stack_cnt]);
				--$lev;
			}

			unset($cur);
		}
	}
	$n = 0; $_GET['start'] = '';
	$usr->md = $frm->md;

	get_prev_next_th_id($frm, $prev_thread_link, $next_thread_link);

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
<?php echo $admin_cp; ?>
<a name="page_top"> </a>
<?php echo draw_forum_path($frm->cat_id, $frm->name, $frm->forum_id, $frm->subject); ?> <?php echo $thread_rating; ?>
<table cellspacing=0 cellpadding=0 border=0 class="wa">
<tr>
<td class="GenText al"><span class="GenText fb">Show:</span> <a href="index.php?t=selmsg&amp;date=today&amp;<?php echo _rsid; ?>&amp;frm_id=<?php echo $frm->id; ?>&amp;th=<?php echo $th; ?>" title="Show all messages that were posted today">Today's Messages</a>&nbsp;<?php echo (_uid ? '<b>::</b> <a href="index.php?t=selmsg&amp;unread=1&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all unread messages">Unread Messages</a>&nbsp;' : '' ) .(!$th ? '<b>::</b> <a href="index.php?t=selmsg&amp;reply_count=0&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all messages, which have no replies">Unanswered Messages</a>' : ''); ?> <b>::</b> <a href="index.php?t=polllist&amp;<?php echo _rsid; ?>">Show Polls</a> <b>::</b> <a href="index.php?t=mnav&amp;<?php echo _rsid; ?>">Message Navigator</a><br /><img src="blank.gif" alt="" height=2 /><?php echo (($frm->replies && $perms & 2048) ? '<a href="index.php?t=split_th&amp;'._rsid.'&amp;th='.$th.'">Split Topic</a>&nbsp;|&nbsp;' : '' ) .$lock_thread.($FUD_OPT_2 & 1073741824 ? '<a href="index.php?t=remail&amp;th='.$th.'&amp;'._rsid.'" title="Send the URL to this page to your friend(s) via e-mail">E-mail to friend</a>&nbsp;' : '' ) .$subscribe_status.'</td>
<td class="vb ar"><a href="index.php?t=msg&amp;th='.$th.'&amp;'._rsid.'#msg_'.$mid.'"><img alt="Return to the default flat view" title="Return to the default flat view" src="theme/default/images/flat_view.gif" /></a>&nbsp;<a href="index.php?t=post&amp;frm_id='.$frm->forum_id.'&amp;'._rsid.'"><img alt="Create a new topic" src="theme/default/images/new_thread.gif" /></a>'.((!($frm->thread_opt & 1) || $perms & 4096) ? '&nbsp;<a href="index.php?t=post&amp;th_id='.$th.'&amp;reply_to='.$mid.'&amp;'._rsid.'"><img alt="Submit Reply" src="theme/default/images/post_reply.gif" /></a>' : ''); ?></td>
</tr>
</table>
<table cellspacing="0" cellpadding="0" class="ContentTable"><?php echo tmpl_drawmsg($msg_obj, $usr, $perms, false, $n, array($prev_msg, $next_msg)); ?></table>
<div class="ac GenText">[<a href="javascript://" onClick="chng_focus('tree_view');" title="View the message list">Message index</a>]</div>
<p>
<table cellspacing="1" cellpadding="2" class="MsgTable">
<?php echo $tree_data; ?>
</table>
<table border=0 cellspacing=1 cellpadding=1 align="right">
<?php echo $prev_thread_link; ?>
<?php echo $next_thread_link; ?>
</table>
<?php echo $rate_thread; ?>
<?php echo tmpl_create_forum_select((isset($frm->forum_id) ? $frm->forum_id : $frm->id), $usr->users_opt & 1048576); ?>
<br /><div class="wa ac">-=] <a href="javascript://" onClick="chng_focus('page_top');">Back to Top</a> [=-</div>
<div class="ar SmallText"><?php echo ($FUD_OPT_2 & 2097152 ? '[ <a href="'.$GLOBALS['WWW_ROOT'].'pdf.php?msg='.$mid.'">Generate printable PDF</a> ]' : ''); ?> <?php echo ($FUD_OPT_2 & 1048576 ? '[ <a href="index.php?t=help_index&amp;section=boardusage#syndicate">Syndicate this forum (XML)</a> ]' : ''); ?></div>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>