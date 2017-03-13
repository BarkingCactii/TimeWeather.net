<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: qbud.php.t,v 1.20 2004/11/24 19:53:36 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

	define('plain_form', 1);

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
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
}

	if (!_uid) {
		std_error('login');
	}

	$all = !empty($_GET['all']);

	if (!$all && isset($_POST['names']) && is_array($_POST['names'])) {
		$names = addcslashes(implode(';', $_POST['names']), '"\\');
?>
<html><body><script language="Javascript">
<!--
if (window.opener.document.post_form.msg_to_list.value.length > 0) {
	window.opener.document.post_form.msg_to_list.value = window.opener.document.post_form.msg_to_list.value+';'+"<?php echo $names; ?>";
} else {
	window.opener.document.post_form.msg_to_list.value = window.opener.document.post_form.msg_to_list.value+"<?php echo $names; ?>";
}
window.close();
//-->
</script></body></html>
<?php
		exit;
	}



	$buddies = '';
	$c = uq('SELECT u.alias FROM fud26_buddy b INNER JOIN fud26_users u ON b.bud_id=u.id WHERE b.user_id='._uid.' AND b.user_id>1');
	while ($r = db_rowarr($c)) {
		$buddies .= '<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td class="GenText">'.$r[0].'</td><td class="ac"><input type="checkbox" name="names[]" value="'.$r[0].'"'.($all ? ' checked' : '' ) .'></td></tr>';
	}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<title><?php echo $GLOBALS['FORUM_TITLE'].$TITLE_EXTRA; ?></title>
<BASE HREF="http://timeweather.net/forum/">
<script language="JavaScript" src="lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="theme/default/forum.css" type="text/css">
</head>
<body>
<table class="wa" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">
<form action="index.php?t=qbud" name="qbud" method="post"><?php echo _hs; ?>
<table cellspacing="1" cellpadding="2" class="ContentTable">
<?php echo ($buddies ? '<tr><th class="wa">Nick Name</th><th class="nw">Selected [<a class="thLnk" href="index.php?t=qbud&amp;'._rsid.'&amp;all='.($all ? '' : '1' )  .'">'.($all ? 'none' : 'all' )  .'</a>]</th></tr>
'.$buddies.'
<tr class="'.alt_var('search_alt','RowStyleA','RowStyleB').'"><td colspan=2 class="GenText ar"><input type="submit" class="button" name="submit" value="Add Selected"></td></tr>' : '<tr class="RowStyleA"><td class="GenText ac">No buddies to choose from</td></tr>'); ?>
</table>
</form>
</td></tr></table></body></html>