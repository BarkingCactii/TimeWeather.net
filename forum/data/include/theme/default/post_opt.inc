<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: post_opt.inc.t,v 1.11 2004/11/24 19:53:36 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

function tmpl_post_options($arg, $perms=0)
{
	$post_opt_html		= '<b>HTML</b> code is <b>OFF</b>';
	$post_opt_fud		= '<b>FUDcode</b> is <b>OFF</b>';
	$post_opt_images 	= '<b>Images</b> are <b>OFF</b>';
	$post_opt_smilies	= '<b>Smilies</b> are <b>OFF</b>';
	$edit_time_limit	= '';

	if (is_int($arg)) {
		if ($arg & 16) {
			$post_opt_fud = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#style" target="_blank"><b>FUDcode</b> is <b>ON</b></a>';
		} else if (!($arg & 8)) {
			$post_opt_html = '<b>HTML</b> is <b>ON</b>';
		}
		if ($perms & 16384) {
			$post_opt_smilies = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#sml" target="_blank"><b>Smilies</b> are <b>ON</b></a>';
		}
		if ($perms & 32768) {
			$post_opt_images = '<b>Images</b> are <b>ON</b>';
		}
		$edit_time_limit = $GLOBALS['EDIT_TIME_LIMIT'] ? '<br><b>Editing Time Limit</b>: <b>'.$GLOBALS['EDIT_TIME_LIMIT'].'</b> minutes' : '<br><b>Editing Time Limit</b>: <b>Unlimited</b>';
	} else if ($arg == 'private') {
		$o =& $GLOBALS['FUD_OPT_1'];

		if ($o & 4096) {
			$post_opt_fud = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#style" target="_blank"><b>FUDcode</b> is <b>ON</b></a>';
		} else if (!($o & 2048)) {
			$post_opt_html = '<b>HTML</b> is <b>ON</b>';
		}
		if ($o & 16384) {
			$post_opt_images = '<b>Images</b> are <b>ON</b>';
		}
		if ($o & 8192) {
			$post_opt_smilies = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#sml" target="_blank"><b>Smilies</b> are <b>ON</b></a>';
		}
	} else if ($arg == 'sig') {
		$o =& $GLOBALS['FUD_OPT_1'];

		if ($o & 131072) {
			$post_opt_fud = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#style" target="_blank"><b>FUDcode</b> is <b>ON</b></a>';
		} else if (!($o & 65536)) {
			$post_opt_html = '<b>HTML</b> is <b>ON</b>';
		}
		if ($o & 524288) {
			$post_opt_images = '<b>Images</b> are <b>ON</b>';
		}
		if ($o & 262144) {
			$post_opt_smilies = '<a href="index.php?section=readingposting&amp;t=help_index&amp;'._rsid.'#sml" target="_blank"><b>Smilies</b> are <b>ON</b></a>';
		}
	}

	return '<span class="SmallText"><b>Forum Options</b><br />
'.$post_opt_html.'<br />
'.$post_opt_fud.'<br />
'.$post_opt_images.'<br />
'.$post_opt_smilies.$edit_time_limit.'</span>';
}
?>