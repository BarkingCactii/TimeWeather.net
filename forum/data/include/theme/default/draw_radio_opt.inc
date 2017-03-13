<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: draw_radio_opt.inc.t,v 1.9 2005/03/18 01:13:22 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

function tmpl_draw_radio_opt($name, $values, $names, $selected, $sep)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (count($vls) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values<br>\n");
	}

	$checkboxes = '';
	foreach ($vls as $k => $v) {
		$checkboxes .= '<input type="radio" name="'.$name.'" value="'.$v.'" '.($v == $selected ? 'checked ' : '' )  .'>'.$nms[$k].$sep;
	}

	return ''.$checkboxes;
}
?>