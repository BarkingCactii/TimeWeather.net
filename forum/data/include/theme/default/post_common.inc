<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: post_common.inc.t,v 1.24 2005/02/07 15:09:32 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

function draw_post_smiley_cntrl()
{
	global $PS_SRC, $PS_DST; /* import from global scope, if possible */

	include_once $GLOBALS['FORUM_SETTINGS_PATH'].'ps_cache';

	/* nothing to do */
	if ($GLOBALS['MAX_SMILIES_SHOWN'] < 1 || !$PS_SRC) {
		return;
	}
	$limit = count($PS_SRC);
	if ($limit > $GLOBALS['MAX_SMILIES_SHOWN']) {
		$limit = $GLOBALS['MAX_SMILIES_SHOWN'];
	}

	$data = '';
	$i = 0;
	while ($i < $limit) {
		$data .= '<a href="javascript: insertTag(document.post_form.msg_body, \'\', \' '.$PS_DST[$i].' \');">'.$PS_SRC[$i++].'</a>&nbsp;';
	}
	return '<tr class="RowStyleA"><td class="nw vt GenText">Smiley Shortcuts:
	<br /><span class="small">[<a href="javascript://" onClick="javascript: window_open(\'http://timeweather.net/forum/index.php?t=smladd\', \'sml_list\', 220, 200);">list all smilies</a>]</span>
</td>
<td class="vt"><table border=0 cellspacing=5 cellpadding=0><tr class="vb"><td>'.$data.'</td></tr></table></td></tr>';
}

function draw_post_icons($msg_icon)
{
	include $GLOBALS['FORUM_SETTINGS_PATH'].'icon_cache';

 	/* nothing to do */
	if (!$ICON_L) {
		return;
	}

	$tmp = $data = '';
	$rl = (int) $GLOBALS['POST_ICONS_PER_ROW'];

	foreach ($ICON_L as $k => $f) {
		if ($k && !($k % $rl)) {
			$data .= '<tr>'.$tmp.'</tr>';
			$tmp = '';
		}
		$tmp .= '<td nowrap valign="middle"><input type="radio" name="msg_icon" value="'.$f.'"'.($f == $msg_icon ? ' checked' : '' ) .'><img src="images/message_icons/'.$f.'" alt="" /></td>';
	}
	if ($tmp) {
		$data .= '<tr>'.$tmp.'</tr>';
	}

	return '<tr class="RowStyleA"><td class="vt GenText">Message Icon:</td><td>
<table border=0 cellspacing=0 cellpadding=2>
<tr><td class="GenText" colspan='.$GLOBALS['POST_ICONS_PER_ROW'].'><input type="radio" name="msg_icon" value=""'.(!$msg_icon ? ' checked' : '' ) .'>No Icon</td></tr>
'.$data.'
</table>
</td></tr>';
}

function draw_post_attachments($al, $max_as, $max_a, $attach_control_error, $private=0, $msg_id)
{
	$attached_files = '';
	$i = 0;

	if (!empty($al)) {
		$enc = base64_encode(serialize($al));

		ses_putvar((int)$GLOBALS['usr']->sid, md5($enc));

		$c = uq('SELECT a.id,a.fsize,a.original_name,m.mime_hdr
		FROM fud26_attach a
		LEFT JOIN fud26_mime m ON a.mime_type=m.id
		WHERE a.id IN('.implode(',', $al).') AND message_id IN(0, '.$msg_id.') AND attach_opt='.($private ? 1 : 0));
		while ($r = db_rowarr($c)) {
			$sz = ( $r[1] < 100000 ) ? number_format($r[1]/1024,2).'KB' : number_format($r[1]/1048576,2).'MB';
			$insert_uploaded_image = strncasecmp('image/', $r[3], 6) ? '' : '&nbsp;|&nbsp;<a href="javascript: insertTag(document.post_form.msg_body, \'[img]index.php?t=getfile&id='.$r[0].'&private='.$private.'\', \'[/img]\');">Insert image into message body</a>';
			$attached_files .= '<tr>
	<td class="RowStyleB">'.$r[2].'</td>
	<td class="RowStyleB">'.$sz.'</td>
	<td class="RowStyleB"><a href="javascript: document.post_form.file_del_opt.value=\''.$r[0].'\'; document.post_form.submit();">Delete</a>'.$insert_uploaded_image.'</td>
</tr>';
			$i++;
		}
	}

	if (!$private && $GLOBALS['MOD'] && $GLOBALS['frm']->forum_opt & 32) {
		$allowed_extensions = '(unrestricted)';
	} else {
		include $GLOBALS['FORUM_SETTINGS_PATH'] . 'file_filter_regexp';
		if (empty($GLOBALS['__FUD_EXT_FILER__'])) {
			$allowed_extensions = '(unrestricted)';
		} else {
			$allowed_extensions = implode(' ', $GLOBALS['__FUD_EXT_FILER__']);
		}
	}
	return '<tr class="RowStyleB"><td class="GenText vt nw">File Attachments:</td><td>
'.($i ? '
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr>
	<th>Name</th>
	<th>Size</th>
	<th>Action</th>
</tr>
'.$attached_files.'
</table>
<input type="hidden" name="file_del_opt" value="">
' : '' )  .'
'.(isset($enc) ? '<input type="hidden" name="file_array" value="'.$enc.'">' : '' ) .'
'.$attach_control_error.'
<span class="SmallText"><b>Allowed File Extensions:</b> '.$allowed_extensions.'<br /><b>Maximum File Size:</b> '.$max_as.'Kb<br /><b>Maximum Files Per Message:</b> '.$max_a.'
'.($i ? '<span class="SmallText"> currently attached '.$i.' file(s)' : '' )  .'
</span><p>
'.((($i + 1) <= $max_a) ? '<input type="file" name="attach_control"> <input type="submit" class="button" name="attach_control_add" value="Upload File">
<input type="hidden" name="tmp_f_val" value="1">' : '' ) .'
</td></tr>';
}
?>