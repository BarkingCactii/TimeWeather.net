<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: pre_reg.php.t,v 1.18 2005/02/25 01:32:53 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}

	if (isset($_POST['disagree'])) {
		if ($FUD_OPT_2 & 32768) {
			header('Location: http://timeweather.net/forum/index.php/i/'._rsidl);
		} else {
			header('Location: http://timeweather.net/forum/index.php?'._rsidl);
		}
		exit;
	} else if (isset($_POST['agree'])) {
		if ($FUD_OPT_2 & 32768) {
			header('Location: http://timeweather.net/forum/index.php/re/' . ($FUD_OPT_1 & 1048576 ?(int)$_POST['coppa'] : 0) .'/'._rsidl);
		} else {
			header('Location: http://timeweather.net/forum/index.php?t=register&'._rsidl.'&reg_coppa='.($FUD_OPT_1 & 1048576 ?(int)$_POST['coppa'] : 0));
		}
		exit;
	}

	$TITLE_EXTRA = ': Forum Terms';

if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
	}

	$_GET['coppa'] = isset($_GET['coppa']) ? (int) $_GET['coppa'] : 0;


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?php echo $GLOBALS['FORUM_TITLE'].$TITLE_EXTRA; ?></title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-15">
<BASE HREF="http://timeweather.net/forum/">
<script language="javascript" src="lib.js" type="text/javascript"></script>
<link rel="StyleSheet" href="theme/default/forum.css" type="text/css" media="screen" title="Default FUDforum Theme">
</head>
<body>

<table width="98%" cellpadding="0" cellspacing="0" class="tbright_cell">
<tr valign="top" bgcolor="#EEEEFF">
<img border="0" src="/images/banner.jpg" width="730" height="100">

<img border="0" src="/images/line14.gif" width="100%" height="25">
</tr>



<tr>

<h2><a href="http://timeweather.net">Jump to Website</a></h2>
<table class="wa" border="0" cellspacing="3" cellpadding="5"><tr><td class="ForumBackground">


</tr>
<div class="UserControlPanel"><?php echo $private_msg; ?> <?php echo (($FUD_OPT_1 & 8388608 || (_uid && $FUD_OPT_1 & 4194304) || $usr->users_opt & 1048576) ? '<a class="UserControlPanel" href="index.php?t=finduser&amp;btn_submit=Find&amp;'._rsid.'"><img src="theme/default/images/top_members.png" alt="Members" /> Members</a>&nbsp;&nbsp;' : ''); ?> <?php echo ($FUD_OPT_1 & 16777216 ? '<a class="UserControlPanel" href="index.php?t=search&amp;'._rsid.'"><img src="theme/default/images/top_search.png" alt="Search" /> Search</a>&nbsp;&nbsp;' : ''); ?> <a class="UserControlPanel" accesskey="h" href="index.php?t=help_index&amp;<?php echo _rsid; ?>"><img src="theme/default/images/top_help.png" alt="FAQ" /> FAQ</a> <?php echo (__fud_real_user__ ? '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=uc&amp;'._rsid.'"><img src="theme/default/images/top_profile.png" title="Click here to access user control panel" alt="User CP" /> User CP</a>' : '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=register&amp;'._rsid.'"><img src="theme/default/images/top_register.png" alt="Register" /> Register</a>'); ?> <?php echo (__fud_real_user__ ? '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=login&amp;'._rsid.'&amp;logout=1&amp;SQ='.$GLOBALS['sq'].'"><img src="theme/default/images/top_logout.png" alt="Logout" /> Logout [ '.$usr->alias.' ]</a>' : '&nbsp;&nbsp;<a class="UserControlPanel" href="index.php?t=login&amp;'._rsid.'"><img src="theme/default/images/top_login.png" alt="Login" /> Login</a>'); ?>&nbsp;&nbsp; <a class="UserControlPanel" href="index.php?t=index&amp;<?php echo _rsid; ?>"><img src="theme/default/images/top_home.png" alt="Home" /> Home</a> <?php echo ($is_a ? '&nbsp;&nbsp;<a class="UserControlPanel" href="adm/admglobal.php?S='.s.'&amp;SQ='.$GLOBALS['sq'].'"><img src="theme/default/images/top_admin.png" alt="Admin Control Panel" /> Admin Control Panel</a>' : ''); ?></div>
<form method="post" action="index.php?t=pre_reg" name="sub"><?php echo _hs; ?>
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th>Information</th></tr>
<tr class="RowStyleA ac GenText"><td>
<?php echo ($_GET['coppa'] ? 'All users under the age of 13 must seek permission from their parent or guardian in order to gain membership to the forums. While we welcome participation from members under 13, we require that a parent or guardian fax or mail back a signed permission form before we grant membership.<p>If you would like to start the registration process prior to us receiving a permission form, press the Agree button below. If you would like to cancel the registration, press Cancel to return to the forums index. <p>A copy of the permission form can be downloaded here. For more information about the registration process, please send e-mail to <a href="mailto:'.$GLOBALS['ADMIN_EMAIL'].'">'.$GLOBALS['ADMIN_EMAIL'].'</a>.<p>' : 'Registration to this forum is free! We do insist that you abide by the rules and policies detailed below. If you agree to the terms, please press the Agree button at the end of the page. Note: By pressing the button you declare that you are over the age of 13. If you are 13 or under, please use this registration form.<p>Although the administrators and moderators of this forum will attempt to keep all objectionable messages off this forum, it is impossible for us to review all messages. All messages express the views of the author, and neither the owners of forum or the developers will be held responsible for the content of any message.<p>By clicking the Agree button, you warrant that you will not post any messages that are obscene, vulgar, sexually-oriented, hateful, threatening, or otherwise violate any laws. <p>The owners of this forum  have the right to remove, edit, move or close any topic for any reason.<p>'); ?>
<br /><input type="hidden" name="coppa" value="<?php echo $_GET['coppa']; ?>"><input type="submit" class="button" name="agree" value="Agree"> <input type="submit" class="button" name="disagree" value="Disagree"></td></tr>
</table></form>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>