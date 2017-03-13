<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: postcheck.inc.t,v 1.25 2005/03/05 18:46:59 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

$GLOBALS['__error__'] = 0;
$GLOBALS['__err_msg__'] = array();

function set_err($err, $msg)
{
	$GLOBALS['__err_msg__'][$err] = $msg;
	$GLOBALS['__error__'] = 1;
}

function is_post_error()
{
	return $GLOBALS['__error__'];
}

function get_err($err, $br=0)
{
	if (isset($err, $GLOBALS['__err_msg__'][$err])) {
		return ($br ? '<span class="ErrorText">'.$GLOBALS['__err_msg__'][$err].'</span><br />' : '<br /><span class="ErrorText">'.$GLOBALS['__err_msg__'][$err].'</span>');
	}
}

function post_check_images()
{
	if ($GLOBALS['MAX_IMAGE_COUNT'] && $GLOBALS['MAX_IMAGE_COUNT'] < count_images($_POST['msg_body'])) {
		return -1;
	}

	return 0;
}

function check_post_form()
{
	/* make sure we got a valid subject */
	if (!strlen(trim($_POST['msg_subject']))) {
		set_err('msg_subject', 'Subject required');
	}

	/* make sure the number of images [img] inside the body do not exceed the allowed limit */
	if (post_check_images()) {
		set_err('msg_body', 'No more than '.$GLOBALS['MAX_IMAGE_COUNT'].' images are allowed per message. Please reduce the number of images.');
	}

	if (defined('fud_bad_sq')) {
		unset($_POST['submitted']);
		set_err('msg_session', '<h4 align="center" class="ErrorText">Your session has expired. Please re-submit the form. Sorry for the inconvenience.</h4>');
	}

	return $GLOBALS['__error__'];
}

function check_ppost_form($msg_subject)
{
	if (!strlen(trim($msg_subject))) {
		set_err('msg_subject', 'Subject required');
	}

	if (post_check_images()) {
		set_err('msg_body', 'No more than '.$GLOBALS['MAX_IMAGE_COUNT'].' images are allowed per message. Please reduce the number of images.');
	}
	$GLOBALS['recv_user_id'] = array();
	/* hack for login names containing HTML entities ex. &#123; */
	if (($hack = strpos($_POST['msg_to_list'], '&#')) !== false) {
		$hack_str = preg_replace('!&#([0-9]+);!', '&#\1#', $_POST['msg_to_list']);
	} else {
		$hack_str = $_POST['msg_to_list'];
	}
	foreach(explode(';', $hack_str) as $v) {
		$v = trim($v);
		if (strlen($v)) {
			if ($hack !== false) {
				$v = preg_replace('!&#([0-9]+)#!', '&#\1;', $v);
			}
			if (!($obj = db_sab('SELECT u.users_opt, u.id, ui.ignore_id FROM fud26_users u LEFT JOIN fud26_user_ignore ui ON ui.user_id=u.id AND ui.ignore_id='._uid.' WHERE u.alias='.strnull(addslashes(char_fix(htmlspecialchars($v))))))) {
				set_err('msg_to_list', 'There is no user named "'.char_fix(htmlspecialchars($v)).'" in this forum.');
				break;
			}
			if (!empty($obj->ignore_id)) {
				set_err('msg_to_list', 'You cannot send a private message to "'.char_fix(htmlspecialchars($v)).'", because this person is ignoring you.');
				break;
			} else if (!($obj->users_opt & 32) && !$GLOBALS['is_a']) {
				set_err('msg_to_list', 'You cannot send a private message to "'.htmlspecialchars($v).'", because this person is not accepting private messages.');
				break;
			} else {
				$GLOBALS['recv_user_id'][] = $obj->id;
			}
		}
	}

	if (empty($_POST['msg_to_list'])) {
		set_err('msg_to_list', 'Cannot send a message, missing recipient');
	}

	if (defined('fud_bad_sq')) {
		unset($_POST['btn_action']);
		set_err('msg_session', '<h4 align="center" class="ErrorText">Your session has expired. Please re-submit the form. Sorry for the inconvenience.</h4>');
	}

	return $GLOBALS['__error__'];
}

function check_femail_form()
{
	if (empty($_POST['femail']) || validate_email($_POST['femail'])) {
		set_err('femail', 'Please enter a valid e-mail address for your friend.');
	}
	if (empty($_POST['subj'])) {
		set_err('subj', 'You cannot send an e-mail without a subject.');
	}
	if (empty($_POST['body'])) {
		set_err('body', 'You cannot send an e-mail without the message body.');
	}
	if (defined('fud_bad_sq')) {
		unset($_POST['posted']);
		set_err('msg_session', '<h4 align="center" class="ErrorText">Your session has expired. Please re-submit the form. Sorry for the inconvenience.</h4>');
	}

	return $GLOBALS['__error__'];
}

function count_images($text)
{
	$text = strtolower($text);
	$a = substr_count($text, '[img]');
	$b = substr_count($text, '[/img]');

	return (($a > $b) ? $b : $a);
}
?>