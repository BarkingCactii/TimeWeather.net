<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: get_cur_ppage.inc.t,v 1.9 2004/11/24 19:53:35 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

$folders = array(1=>'Inbox', 2=>'Saved', 4=>'Draft', 3=>'Sent', 5=>'Trash');

function tmpl_cur_ppage($folder_id, $folders, $msg_subject='')
{
	if (!$folder_id || (!$msg_subject && $_GET['t'] == 'ppost')) {
		$user_action = 'Writing a Private Message';
	} else {
		$user_action = $msg_subject ? '<a href="index.php?t=pmsg&amp;folder_id='.$folder_id.'&amp;'._rsid.'">'.$folders[$folder_id].'</a> &raquo; '.$msg_subject.'' : 'Browsing <b>'.$folders[$folder_id].'</b> folder';
	}

	return '<span class="SmallText"><a href="index.php?t=pmsg&amp;'._rsid.'">Private Messaging</a>&nbsp;&raquo;&nbsp;'.$user_action.'</span><br /><img src="blank.gif" alt="" height=4 width=1 /><br />';
}
?>