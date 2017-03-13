<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: markread.php.t,v 1.13 2004/11/24 19:53:35 hackie Exp $
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
}


	if (_uid && sq_check(0, $usr->sq)) {
		if (!empty($_GET['id'])) {
			user_mark_forum_read(_uid, (int)$_GET['id'], $usr->last_read);
		} else if (!empty($_GET['cat'])) {
			/* mark all forums inside a category and it's child categories */
			require $GLOBALS['FORUM_SETTINGS_PATH'].'cat_cache.inc';

			if (!empty($cat_cache[$_GET['cat']])) {
				$c = $cat_cache[$_GET['cat']];

				$cids = array();
				/* fetch all sub-categories if there are any */
				if (!empty($c[2])) {
					$cids = $c[2];
				}
				$cids[] = (int)$_GET['cat'];

				$c = q("SELECT id FROM fud26_forum WHERE cat_id IN(".implode(',', $cids).")");
				while ($r = db_rowarr($c)) {
					user_mark_forum_read(_uid, $r[0], $usr->last_read);
				}
			}
		} else {
			user_mark_all_read(_uid);
		}
	}

	check_return($usr->returnto);
	exit;
?>