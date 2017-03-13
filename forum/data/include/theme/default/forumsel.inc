<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: forumsel.inc.t,v 1.30 2004/12/21 16:15:31 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

function tmpl_create_forum_select($frm_id, $mod)
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
}
?>