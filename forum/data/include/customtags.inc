<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: customtags.inc,v 1.10 2004/11/24 19:53:34 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

function ctag_rebuild_cache($id)
{
	$ct = '';
	$c = uq('SELECT name FROM '.$GLOBALS['DBHOST_TBL_PREFIX'].'custom_tags WHERE user_id='.$id.' ORDER BY name');
	while ($r = db_rowarr($c)) {
		$ct .= $r[0] . '<br />';
	}
	q("UPDATE ".$GLOBALS["DBHOST_TBL_PREFIX"]."users SET custom_status='".addslashes(substr($ct, 0, -6))."' WHERE id=".$id);
}
?>