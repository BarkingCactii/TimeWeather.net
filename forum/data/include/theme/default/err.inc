<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: err.inc.t,v 1.47 2005/03/18 00:13:54 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

function error_dialog($title, $msg, $level='WARN', $ses=null)
{
	if (!$ses) {
		$ses = (int) $GLOBALS['usr']->sid;
	}

	$error_msg = '[Error] '.$title.'<br />';
	$error_msg .= '[Message Sent to User] '.trim($msg).'<br />';
	$error_msg .= '[User IP] '.get_ip().'<br />';
	$error_msg .= '[Requested URL] http://';
	$error_msg .= isset($_SERVER['HTTP_HOST']) ? htmlspecialchars($_SERVER['HTTP_HOST']) : '';
	$error_msg .= isset($_SERVER['REQUEST_URI']) ? htmlspecialchars($_SERVER['REQUEST_URI']) : '';
	$error_msg .= !empty($_POST) ? '<br />[Post-Data] '.base64_encode(htmlspecialchars(serialize($_POST))) : '';
	$error_msg .= '<br />';

	if (isset($_SERVER['HTTP_REFERER'])) {
		$error_msg .= '[Referring Page] '.htmlspecialchars($_SERVER['HTTP_REFERER']).'<br />';
	}
	error_log('['.gmdate('D M j G:i:s T Y', __request_timestamp__).'] '.base64_encode($error_msg)."\n", 3, $GLOBALS['ERROR_PATH'].'fud_errors');

	/* no need to redirect, we just want to log the error */
	if ($level == 'ATCH') {
		return;
	}

	ses_putvar($ses, array('er_msg' => $msg, 'err_t' => $title));

	if (is_int($ses)) {
		if ($GLOBALS['FUD_OPT_2'] & 32768) {
			header('Location: http://timeweather.net/forum/index.php/e/'._rsidl);
		} else {
			header('Location: http://timeweather.net/forum/index.php?t=error&'._rsidl);
		}
	} else {
		if ($GLOBALS['FUD_OPT_2'] & 32768) {
			header('Location: http://timeweather.net/forum/index.php/e/0/'.$ses);
		} else {
			header('Location: http://timeweather.net/forum/index.php?t=error&S='.$ses);
		}
	}
	exit;
}

function std_error($type)
{
	if (!isset($_SERVER['HTTP_REFERER'])) {
		$_SERVER['HTTP_REFERER'] = 'unknown';
	}

	$ses_id = s;
	$usr_d->email = $GLOBALS['usr']->email;

	if ($type == 'login') {
		if (__fud_real_user__) {
			$type = 'perms';
		} else {
			ses_anonuser_auth($GLOBALS['usr']->sid, '<fieldset><legend>ERROR: You are not logged-in</legend>To access this page you must be a member of this forum.</fieldset><br />');
		}
	}

	$err_array = array(
'ERR_disabled'=>array('Disabled feature', 'This feature has been disabled by the system administrator'),
'ERR_access'=>array('ERROR: Insufficient Permissions', 'You do not have the required permissions to perform this action'),
'ERR_registration_disabled'=>array('Registration Disabled', 'The administrator has disabled registration. Please try again later.'),
'ERR_user'=>array('ERROR: No such user', 'There is no such user'),
'ERR_perms'=>array('Permission Denied!', 'Your user permissions do not allow you to perform the action you are attempting.'),
'ERR_systemerr'=>array('ERROR: System Error', 'A system error has occurred. Please report it to the <a href="mailto:'.$GLOBALS['ADMIN_EMAIL'].'">webmaster</a>.'),
'ERR_emailconf'=>array('ERROR: Your account is not yet confirmed', 'You have not confirmed your account via e-mail yet<br><table border=0><tr><td><ol><li>If you have not received a confirmation e-mail, <a href="index.php?t=reset&amp;email='.$usr_d->email.'&amp;S='.$ses_id.'">click here</a><li>If '.$usr_d->email.' is not your correct e-mail address, <a href="index.php?t=register&amp;S='.$ses_id.'">click here</a></ol></td></tr></table>')
);

	if (isset($err_array['ERR_'.$type])) {
		$err = $err_array['ERR_'.$type];
		error_dialog($err[0], $err[1]);
	}
	error_dialog('CRITICAL ERROR', 'An error has occurred. Unfortunately, there is also a problem with the error reporting system.');
}

function invl_inp_err()
{
	error_dialog('Invalid Input', 'You have followed an invalid link. Please notify the <a href="mailto:'.$GLOBALS['ADMIN_EMAIL'].'">webmaster</a> how you got here.');
}
?>