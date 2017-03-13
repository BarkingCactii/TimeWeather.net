<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: adm_acc.inc.t,v 1.5 2004/11/24 19:53:34 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

	$GLOBALS['account_accepted'] = 'Your account \''.$GLOBALS['r']->login.'\' was approved by the administrator and is now active. You may now access the '.$GLOBALS['FORUM_TITLE'].'\' forum as a confirmed, registered user.';
	$GLOBALS['account_rejected'] = 'Your account \''.$GLOBALS['r']->login.'\' was declined by the administrator. If you would like to know the reason behind this decision, feel free to contact the administrator at \''.$GLOBALS['ADMIN_EMAIL'].'\'.';

	$GLOBALS['account_accepted_s'] = 'Your account request for \''.$GLOBALS['FORUM_TITLE'].'\' was accepted.';
	$GLOBALS['account_rejected_s'] = 'Your account request for \''.$GLOBALS['FORUM_TITLE'].'\' was declined.';
?>
