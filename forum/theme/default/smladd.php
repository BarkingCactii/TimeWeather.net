<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: smladd.php.t,v 1.14 2005/02/27 02:44:26 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}


	$col_count = '7' - 2;
	$col_pos = -1;

	include $FORUM_SETTINGS_PATH.'ps_cache';

	$sml_smiley_entry = $sml_smiley_row = '';
	foreach ($PS_SRC as $k => $v) {
		if ($col_pos++ > $col_count) {
			$sml_smiley_row .= '<tr class="vb"><td>'.$sml_smiley_entry.'</td></tr>';
			$sml_smiley_entry = '';
			$col_pos = 0;
		}
		$sml_smiley_entry .= '<a href="javascript: insertParentTag(\' '.$PS_DST[$k].' \',\'\');">'.$v.'</a>&nbsp;&nbsp;';
	}
	if ($col_pos > -1) {
		$sml_smiley_row .= '<tr class="vb"><td>'.$sml_smiley_entry.'</td></tr>';
	} else {
		$sml_smiley_row = 'No emoticons available.';
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
<table cellspacing=1 cellpadding=0 width="100%" class="dashed">
<?php echo $sml_smiley_row; ?>
<tr><td class="ac" colspan="<?php echo $col_count; ?>">[<a href="javascript://" onClick="javascript: window.close();">close window</a>]</td></tr>
</table>
</td></tr></table></body></html>
