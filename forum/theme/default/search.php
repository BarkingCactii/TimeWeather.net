<?php
/**
* copyright            : (C) 2001-2004 Advanced Internet Designs Inc.
* email                : forum@prohost.org
* $Id: search.php.t,v 1.61 2005/03/18 01:58:51 hackie Exp $
*
* This program is free software; you can redistribute it and/or modify it
* under the terms of the GNU General Public License as published by the
* Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
**/

if (_uid === '_uid') {
		exit('sorry, you can not access this page');
	}$GLOBALS['__revfs'] = array('&quot;', '&lt;', '&gt;', '&amp;');
$GLOBALS['__revfd'] = array('"', '<', '>', '&');

function reverse_fmt($data)
{
	$s = $d = array();
	foreach ($GLOBALS['__revfs'] as $k => $v) {
		if (strpos($data, $v) !== false) {
			$s[] = $v;
			$d[] = $GLOBALS['__revfd'][$k];
		}
	}

	return $s ? str_replace($s, $d, $data) : $data;
}function pager_replace(&$str, $s, $c)
{
	$str = str_replace(array('%s', '%c'), array($s, $c), $str);
}

function tmpl_create_pager($start, $count, $total, $arg, $suf='', $append=1, $js_pager=0)
{
	if (!$count) {
		$count =& $GLOBALS['POSTS_PER_PAGE'];
	}
	if ($total <= $count) {
		return;
	}

	if ($GLOBALS['FUD_OPT_2'] & 32768 && (!empty($_SERVER['PATH_INFO']) || strpos($arg, '?') === false)) {
		if (!$suf) {
			$suf = '/';
		}
		$upfx = '';
	} else {
		$upfx = '&amp;start=';
	}

	$cur_pg = ceil($start / $count);
	$ttl_pg = ceil($total / $count);

	$page_pager_data = '';

	if (($page_start = $start - $count) > -1) {
		if ($append) {
			$page_first_url = $arg . $upfx . $suf;
			$page_prev_url = $arg . $upfx . $page_start . $suf;
		} else {
			$page_first_url = $page_prev_url = $arg;
			pager_replace($page_first_url, 0, $count);
			pager_replace($page_prev_url, $page_start, $count);
		}

		$page_pager_data .= !$js_pager ? '&nbsp;<a href="'.$page_first_url.'" class="PagerLink">&laquo;</a>&nbsp;&nbsp;<a href="'.$page_prev_url.'" accesskey="p" class="PagerLink">&lt;</a>&nbsp;&nbsp;' : '&nbsp;<a href="javascript://" onClick="'.$page_first_url.'" class="PagerLink">&laquo;</a>&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_prev_url.'" class="PagerLink">&lt;</a>&nbsp;&nbsp;';
	}

	$mid = ceil($GLOBALS['GENERAL_PAGER_COUNT'] / 2);

	if ($ttl_pg > $GLOBALS['GENERAL_PAGER_COUNT']) {
		if (($mid + $cur_pg) >= $ttl_pg) {
			$end = $ttl_pg;
			$mid += $mid + $cur_pg - $ttl_pg;
			$st = $cur_pg - $mid;
		} else if (($cur_pg - $mid) <= 0) {
			$st = 0;
			$mid += $mid - $cur_pg;
			$end = $mid + $cur_pg;
		} else {
			$st = $cur_pg - $mid;
			$end = $mid + $cur_pg;
		}

		if ($st < 0) {
			$start = 0;
		}
		if ($end > $ttl_pg) {
			$end = $ttl_pg;
		}
		if ($end - $start > $GLOBALS['GENERAL_PAGER_COUNT']) {
			$end = $start + $GLOBALS['GENERAL_PAGER_COUNT'];
		}
	} else {
		$end = $ttl_pg;
		$st = 0;
	}

	while ($st < $end) {
		if ($st != $cur_pg) {
			$page_start = $st * $count;
			if ($append) {
				$page_page_url = $arg . $upfx . $page_start . $suf;
			} else {
				$page_page_url = $arg;
				pager_replace($page_page_url, $page_start, $count);
			}
			$st++;
			$page_pager_data .= !$js_pager ? '<a href="'.$page_page_url.'" class="PagerLink">'.$st.'</a>&nbsp;&nbsp;' : '<a href="javascript://" onClick="'.$page_page_url.'" class="PagerLink">'.$st.'</a>&nbsp;&nbsp;';
		} else {
			$st++;
			$page_pager_data .= !$js_pager ? $st.'&nbsp;&nbsp;' : $st.'&nbsp;&nbsp;';
		}
	}

	$page_pager_data = substr($page_pager_data, 0 , strlen((!$js_pager ? '&nbsp;&nbsp;' : '&nbsp;&nbsp;')) * -1);

	if (($page_start = $start + $count) < $total) {
		$page_start_2 = ($st - 1) * $count;
		if ($append) {
			$page_next_url = $arg . $upfx . $page_start . $suf;
			$page_last_url = $arg . $upfx . $page_start_2 . $suf;
		} else {
			$page_next_url = $page_last_url = $arg;
			pager_replace($page_next_url, $upfx . $page_start, $count);
			pager_replace($page_last_url, $upfx . $page_start_2, $count);
		}
		$page_pager_data .= !$js_pager ? '&nbsp;&nbsp;<a href="'.$page_next_url.'" accesskey="n" class="PagerLink">&gt;</a>&nbsp;&nbsp;<a href="'.$page_last_url.'" class="PagerLink">&raquo;</a>' : '&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_next_url.'" class="PagerLink">&gt;</a>&nbsp;&nbsp;<a href="javascript://" onClick="'.$page_last_url.'" class="PagerLink">&raquo;</a>';
	}

	return !$js_pager ? '<span class="SmallText fb">Pages ('.$ttl_pg.'): ['.$page_pager_data.']</span>' : '<span class="SmallText fb">Pages ('.$ttl_pg.'): ['.$page_pager_data.']</span>';
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
}function tmpl_draw_radio_opt($name, $values, $names, $selected, $sep)
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

	return $checkboxes;
}function tmpl_draw_select_opt($values, $names, $selected)
{
	$vls = explode("\n", $values);
	$nms = explode("\n", $names);

	if (count($vls) != count($nms)) {
		exit("FATAL ERROR: inconsistent number of values inside a select<br>\n");
	}

	$options = '';
	foreach ($vls as $k => $v) {
		$options .= '<option value="'.$v.'"'.($v == $selected ? ' selected' : '' )  .'>'.$nms[$k].'</option>';
	}

	return $options;
}function register_fp($id)
{
	if (!isset($GLOBALS['__MSG_FP__'][$id])) {
		$GLOBALS['__MSG_FP__'][$id] = fopen($GLOBALS['MSG_STORE_DIR'].'msg_'.$id, 'rb');
	}

	return $GLOBALS['__MSG_FP__'][$id];
}

function read_msg_body($off, $len, $file_id)
{
	if (!$len) {
		return;
	}

	$fp = register_fp($file_id);
	fseek($fp, $off);
	return fread($fp, $len);
}function delete_msg_index($msg_id)
{
	q('DELETE FROM fud26_index WHERE msg_id='.$msg_id);
	q('DELETE FROM fud26_title_index WHERE msg_id='.$msg_id);
}

function mb_word_split($str)
{
	$m = array();
	$lang = $GLOBALS['usr']->lang == 'chinese' ? 'EUC-CN' : 'BIG-5';

	if (extension_loaded('iconv')) {
		preg_match_all('!(\w)!u', @iconv($lang, 'UTF-8', $str), $m);
	} else if (extension_loaded('mbstring')) {
		preg_match_all('!(\w)!u', @mb_convert_encoding($str, 'UTF-8', $lang), $m);
	} else { /* poor man's alternative to proper multi-byte support */
		preg_match_all("!([\\1-\\255]{1,2})!", $str, $m);
	}

	if (!$m) {
		return array();
	}

	$m2 = array();
	foreach (array_unique($m[0]) as $v) {
		if (isset($v[1])) {
			$m2[] = "'".addslashes($v)."'";
		}
	}

	return $m2;
}

function text_to_worda($text)
{
	$a = array();

	/* if no good locale, default to splitting by spaces */
	if (!$GLOBALS['good_locale']) {
		$GLOBALS['usr']->lang = 'latvian';
	}

	$text = reverse_fmt($text);
	while (1) {
		switch ($GLOBALS['usr']->lang) {
			case 'chinese_big5':
			case 'chinese':
				return array_unique(mb_word_split($text));
		
			case 'japanese':
				preg_match_all('!(\w)!u', $text, $tmp);
				break;

			case 'latvian':
			case 'russian-1251':
				$t1 = array_unique(preg_split('![\x00-\x40]+!', $text, -1, PREG_SPLIT_NO_EMPTY));
				break;

			default:
				$t1 = array_unique(str_word_count(strip_tags(strtolower($text)), 1));
				if (!$t1) { /* fall through to split by special chars */
					$GLOBALS['usr']->lang = 'latvian';
					continue;		
				} 
				break;
		}

		/* this is mostly a hack for php verison < 4.3 because isset(string[bad offset]) returns a warning */
		error_reporting(0);
	
		foreach ($t1 as $v) {
			if (isset($v[51]) || !isset($v[2])) continue;
			$a[] = "'".addslashes($v)."'";
		}

		error_reporting(2047); /* restore error reporting */

		break;
	}

	return $a;
}

function index_text($subj, $body, $msg_id)
{
	/* Remove Stuff In Quotes */
	while (preg_match('!<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>(.*?)</b></td></tr><tr><td class="quote"><br />(.*?)<br /></td></tr></table>!is', $body)) {
		$body = preg_replace('!<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>(.*?)</b></td></tr><tr><td class="quote"><br />(.*?)<br /></td></tr></table>!is', '', $body);
	}

	$w1 = text_to_worda($subj);
	$w2 = $w1 ? array_merge($w1, text_to_worda($body)) : text_to_worda($body);

	if (!$w2) {
		return;
	}

	$w2 = array_unique($w2);
	if (__dbtype__ == 'mysql') {
		ins_m('fud26_search', 'word', $w2);
	} else {
		if (!defined('search_prep')) {
			define('search_prep', 'PREPARE fud26_srch_ins (text) AS INSERT INTO fud26_search (word) VALUES($1)');
			define('search_prep2', 'PREPARE fud26_srch_sel (text) AS SELECT id FROM fud26_search WHERE word= $1');
			pg_query(fud_sql_lnk, search_prep);
			pg_query(fud_sql_lnk, search_prep2);
		}
		foreach ($w2 as $w) {			
			if (pg_num_rows(pg_query(fud_sql_lnk, "EXECUTE fud26_srch_sel (".$w.")")) < 1) {
				pg_query(fud_sql_lnk, "EXECUTE fud26_srch_ins (".$w.")");
			}
		}
		/* if persistent connections are used de-allocte the prepared statement to prevent query failures */
		if ($GLOBALS['FUD_OPT_1'] & 256) {
			pg_query(fud_sql_lnk, 'DEALLOCATE fud26_srch_sel');
			pg_query(fud_sql_lnk, 'DEALLOCATE fud26_srch_ins');
		}
	}

	if ($w1) {
		db_li('INSERT INTO fud26_title_index (word_id, msg_id) SELECT id, '.$msg_id.' FROM fud26_search WHERE word IN('.implode(',', $w1).')', $ef);
	}
	db_li('INSERT INTO fud26_index (word_id, msg_id) SELECT id, '.$msg_id.' FROM fud26_search WHERE word IN('.implode(',', $w2).')', $ef);
}$GLOBALS['seps'] = array(' '=>' ', "\n"=>"\n", "\r"=>"\r", "'"=>"'", '"'=>'"', '['=>'[', ']'=>']', '('=>'(', ';'=>';', ')'=>')', "\t"=>"\t", '='=>'=', '>'=>'>', '<'=>'<');

function fud_substr_replace($str, $newstr, $pos, $len)
{
        return substr($str, 0, $pos).$newstr.substr($str, $pos+$len);
}

function tags_to_html($str, $allow_img=1, $no_char=0)
{
	if (!$no_char) {
		$str = htmlspecialchars($str);
	}

	$str = nl2br($str);

	$ostr = '';
	$pos = $old_pos = 0;

	while (($pos = strpos($str, '[', $pos)) !== false) {
		if (isset($GLOBALS['seps'][$str[$pos + 1]])) {
			++$pos;
			continue;
		}

		if (($epos = strpos($str, ']', $pos)) === false) {
			break;
		}
		if (!($epos-$pos-1)) {
			$pos = $epos + 1;
			continue;
		}
		$tag = substr($str, $pos+1, $epos-$pos-1);
		if (($pparms = strpos($tag, '=')) !== false) {
			$parms = substr($tag, $pparms+1);
			if (!$pparms) { /*[= exception */
				$pos = $epos+1;
				continue;
			}
			$tag = substr($tag, 0, $pparms);
		} else {
			$parms = '';
		}

		$tag = strtolower($tag);

		switch ($tag) {
			case 'quote title':
				$tag = 'quote';
				break;
			case 'list type':
				$tag = 'list';
				break;
		}

		if ($tag[0] == '/') {
			if (isset($end_tag[$pos])) {
				if( ($pos-$old_pos) ) $ostr .= substr($str, $old_pos, $pos-$old_pos);
				$ostr .= $end_tag[$pos];
				$pos = $old_pos = $epos+1;
			} else {
				$pos = $epos+1;
			}

			continue;
		}

		$cpos = $epos;
		$ctag = '[/'.$tag.']';
		$ctag_l = strlen($ctag);
		$otag = '['.$tag;
		$otag_l = strlen($otag);
		$rf = 1;
		$nt_tag = 0;
		while (($cpos = strpos($str, '[', $cpos)) !== false) {
			if (isset($end_tag[$cpos]) || isset($GLOBALS['seps'][$str[$cpos + 1]])) {
				++$cpos;
				continue;
			}

			if (($cepos = strpos($str, ']', $cpos)) === false) {
				if (!$nt_tag) {
					break 2;
				} else {
					break;
				}
			}

			if (strcasecmp(substr($str, $cpos, $ctag_l), $ctag) == 0) {
				--$rf;
			} else if (strcasecmp(substr($str, $cpos, $otag_l), $otag) == 0) {
				++$rf;
			} else {
				$nt_tag++;
				++$cpos;
				continue;
			}

			if (!$rf) {
				break;
			}
			$cpos = $cepos;
		}

		if (!$cpos || ($rf && $str[$cpos] == '<')) { /* left over [ handler */
			++$pos;
			continue;
		}

		if ($cpos !== false) {
			if (($pos-$old_pos)) {
				$ostr .= substr($str, $old_pos, $pos-$old_pos);
			}
			switch ($tag) {
				case 'notag':
					$ostr .= '<span name="notag">'.substr($str, $epos+1, $cpos-1-$epos).'</span>';
					$epos = $cepos;
					break;
				case 'url':
					if (!$parms) {
						$url = substr($str, $epos+1, ($cpos-$epos)-1);
					} else {
						$url = $parms;
					}

					if (!strncasecmp($url, 'www.', 4)) {
						$url = 'http&#58;&#47;&#47;'. $url;
					} else if (strpos(strtolower($url), 'javascript:') !== false) {
						$ostr .= substr($str, $pos, $cepos - $pos + 1);
						$epos = $cepos;
						$str[$cpos] = '<';
						break;
					} else {
						$url = str_replace('://', '&#58;&#47;&#47;', $url);
					}

					$end_tag[$cpos] = '</a>';
					$ostr .= '<a href="'.$url.'" target="_blank">';
					break;
				case 'i':
				case 'u':
				case 'b':
				case 's':
				case 'sub':
				case 'sup':
				case 'del':
					$end_tag[$cpos] = '</'.$tag.'>';
					$ostr .= '<'.$tag.'>';
					break;
				case 'email':
					if (!$parms) {
						$parms = str_replace('@', '&#64;', substr($str, $epos+1, ($cpos-$epos)-1));
						$ostr .= '<a href="mailto:'.$parms.'" target="_blank">'.$parms.'</a>';
						$epos = $cepos;
						$str[$cpos] = '<';
					} else {
						$end_tag[$cpos] = '</a>';
						$ostr .= '<a href="mailto:'.str_replace('@', '&#64;', $parms).'" target="_blank">';
					}
					break;
				case 'color':
				case 'size':
				case 'font':
					if ($tag == 'font') {
						$tag = 'face';
					}
					$end_tag[$cpos] = '</font>';
					$ostr .= '<font '.$tag.'="'.$parms.'">';
					break;
				case 'code':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);

					$ostr .= '<div class="pre"><pre>'.reverse_nl2br($param).'</pre></div>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'pre':
					$param = substr($str, $epos+1, ($cpos-$epos)-1);

					$ostr .= '<pre>'.reverse_nl2br($param).'</pre>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'php':
					$param = trim(reverse_fmt(reverse_nl2br(substr($str, $epos+1, ($cpos-$epos)-1))));

					if (strncmp($param, '<?php', 5)) {
						if (strncmp($param, '<?', 2)) {
							$param = "<?php\n" . $param;
						} else {
							$param = "<?php\n" . substr($param, 3);
						}
					}
					if (substr($param, -2) != '?>') {
						$param .= "\n?>";
					}

					$ostr .= '<span name="php">'.trim(@highlight_string($param, true)).'</span>';
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'img':
				case 'imgl':
				case 'imgr':
					if (!$allow_img) {
						$ostr .= substr($str, $pos, ($cepos-$pos)+1);
					} else {
						$class = ($tag == 'img') ? '' : 'class="'.$tag{3}.'" ';

						if (!$parms) {
							$parms = substr($str, $epos+1, ($cpos-$epos)-1);
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img '.$class.'src="'.$parms.'" border=0 alt="'.$parms.'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						} else {
							if (strpos(strtolower($parms), 'javascript:') === false) {
								$ostr .= '<img '.$class.'src="'.$parms.'" border=0 alt="'.substr($str, $epos+1, ($cpos-$epos)-1).'">';
							} else {
								$ostr .= substr($str, $pos, ($cepos-$pos)+1);
							}
						}
					}
					$epos = $cepos;
					$str[$cpos] = '<';
					break;
				case 'quote':
					if (!$parms) {
						$parms = 'Quote:';
					}
					$ostr .= '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>'.$parms.'</b></td></tr><tr><td class="quote"><br />';
					$end_tag[$cpos] = '<br /></td></tr></table>';
					break;
				case 'align':
					$end_tag[$cpos] = '</div>';
					$ostr .= '<div align="'.$parms.'">';
					break;
				case 'list':
					$tmp = substr($str, $epos, ($cpos-$epos));
					$tmp_l = strlen($tmp);
					$tmp2 = str_replace(array('[*]', '<br />'), array('<li>', ''), $tmp);
					$tmp2_l = strlen($tmp2);
					$str = str_replace($tmp, $tmp2, $str);

					$diff = $tmp2_l - $tmp_l;
					$cpos += $diff;

					if (isset($end_tag)) {
						foreach($end_tag as $key => $val) {
							if ($key < $epos) {
								continue;
							}

							$end_tag[$key+$diff] = $val;
						}
					}

					switch (strtolower($parms)) {
						case '1':
						case 'a':
							$end_tag[$cpos] = '</ol>';
							$ostr .= '<ol type="'.$parms.'">';
							break;
						case 'square':
						case 'circle':
						case 'disc':
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul type="'.$parms.'">';
							break;
						default:
							$end_tag[$cpos] = '</ul>';
							$ostr .= '<ul>';
					}
					break;
				case 'spoiler':
					$rnd = rand();
					$end_tag[$cpos] = '</div></div>';
					$ostr .= '<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis(\''.$rnd.'\', 1);">'
						.($parms ? $parms : 'Toggle Spoiler').'</a><div align="left" id="'.$rnd.'" style="display: none;">';
					break;
				case 'acronym':
					$end_tag[$cpos] = '</acronym>';
					$ostr .= '<acronym title="'.($parms ? $parms : ' ').'">';
					break;
			}

			$str[$pos] = '<';
			$pos = $old_pos = $epos+1;
		} else {
			$pos = $epos+1;
		}
	}
	$ostr .= substr($str, $old_pos, strlen($str)-$old_pos);

	/* url paser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '://', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}
		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i > $ppos) {
			if ($ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if (!$pos || $ostr[$i] == '<') {
			$pos += 3;
			continue;
		}

		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		// check if it's inside the span tag
		if (($ts = strpos($ostr, '<span>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</span>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 3;
			continue;
		}

		$us = $pos;
		$l = strlen($ostr);
		while (1) {
			--$us;
			if ($ppos > $us || $us >= $l || isset($GLOBALS['seps'][$ostr[$us]])) {
				break;
			}
		}

		unset($GLOBALS['seps']['=']);
		$ue = $pos;
		while (1) {
			++$ue;
			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}

			if ($ostr[$ue] == '&') {
				if ($ostr[$ue+4] == ';') {
					$ue += 4;
					continue;
				}
				if ($ostr[$ue+3] == ';' || $ostr[$ue+5] == ';') {
					break;
				}
			}

			if ($ue >= $l || isset($GLOBALS['seps'][$ostr[$ue]])) {
				break;
			}
		}
		$GLOBALS['seps']['='] = '=';

		$url = substr($ostr, $us+1, $ue-$us-1);
		if (!strncasecmp($url, 'javascript', strlen('javascript')) || ($ue - $us - 1) < 9) {
			$pos = $ue;
			continue;
		}
		$html_url = '<a href="'.$url.'" target="_blank">'.$url.'</a>';
		$html_url_l = strlen($html_url);
		$ostr = fud_substr_replace($ostr, $html_url, $us+1, $ue-$us-1);
		$ppos = $pos;
		$pos = $us+$html_url_l;
	}

	/* email parser */
	$pos = 0;
	$ppos = 0;
	while (($pos = @strpos($ostr, '@', $pos)) !== false) {
		if ($pos < $ppos) {
			break;
		}

		// check if it's inside any tag;
		$i = $pos;
		while (--$i && $i>$ppos) {
			if ( $ostr[$i] == '>' || $ostr[$i] == '<') {
				break;
			}
		}
		if ($i < 0 || $ostr[$i]=='<') {
			++$pos;
			continue;
		}


		// check if it's inside the a tag
		if (($ts = strpos($ostr, '<a ', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</a>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		// check if it's inside the pre tag
		if (($ts = strpos($ostr, '<div class="pre"><pre>', $pos)) === false) {
			$ts = strlen($ostr);
		}
		if (($te = strpos($ostr, '</pre></div>', $pos)) == false) {
			$te = strlen($ostr);
		}
		if ($te < $ts) {
			$ppos = $pos += 1;
			continue;
		}

		for ($es = ($pos - 1); $es > ($ppos - 1); $es--) {
			if (
				( ord($ostr[$es]) >= ord('A') && ord($ostr[$es]) <= ord('z') ) ||
				( ord($ostr[$es]) >= ord(0) && ord($ostr[$es]) <= ord(9) ) ||
				( $ostr[$es] == '.' || $ostr[$es] == '-' || $ostr[$es] == '\'')
			) { continue; }
			++$es;
			break;
		}
		if ($es == $pos) {
			$ppos = $pos += 1;
			continue;
		}
		if ($es < 0) {
			$es = 0;
		}

		for ($ee = ($pos + 1); @isset($ostr[$ee]); $ee++) {
			if (
				( ord($ostr[$ee]) >= ord('A') && ord($ostr[$ee]) <= ord('z') ) ||
				( ord($ostr[$ee]) >= ord(0) && ord($ostr[$ee]) <= ord(9) ) ||
				( $ostr[$ee] == '.' || $ostr[$ee] == '-' )
			) { continue; }
			break;
		}
		if ($ee == ($pos+1)) {
			$ppos = $pos += 1;
			continue;
		}

		$email = str_replace('@', '&#64;', substr($ostr, $es, $ee-$es));
		$email_url = '<a href="mailto:'.$email.'" target="_blank">'.$email.'</a>';
		$email_url_l = strlen($email_url);
		$ostr = fud_substr_replace($ostr, $email_url, $es, $ee-$es);
		$ppos =	$es+$email_url_l;
		$pos = $ppos;
	}

	return $ostr;
}

function html_to_tags($fudml)
{
	while (preg_match('!<span name="php">(.*?)</span>!is', $fudml, $res)) {
		$tmp = trim(html_entity_decode(strip_tags(str_replace('<br />', "\n", $res[1]))));
		$m = md5($tmp);
		$php[$m] = $tmp;
		$fudml = str_replace($res[0], "[php]\n".$m."\n[/php]", $fudml);
	}

	if (strpos($fudml, '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>')  !== false) {
		$fudml = str_replace(array('<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>','</b></td></tr><tr><td class="quote"><br />','<br /></td></tr></table>'), array('[quote title=', ']', '[/quote]'), $fudml);
		// old bad code
		$fudml = str_replace(array('<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>','</b></td></tr><tr><td class="quote"><br>','<br></td></tr></table>'), array('[quote title=', ']', '[/quote]'), $fudml);
	}

	/* old format */
	if (preg_match('!<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis\(\'.*?\', 1\);">.*?</a><div align="left" id="(.*?)" style="visibility: hidden;">!is', $fudml)) {
		$fudml = preg_replace('!\<div class\="dashed" style\="padding: 3px;" align\="center" width\="100%"\>\<a href\="javascript://" OnClick\="javascript: layerVis\(\'.*?\', 1\);">(.*?)\</a\>\<div align\="left" id\=".*?" style\="visibility: hidden;"\>!is', '[spoiler=\1]', $fudml);
		$fudml = str_replace('</div></div>', '[/spoiler]', $fudml);
	}

	/* new format */	
	if (preg_match('!<div class="dashed" style="padding: 3px;" align="center" width="100%"><a href="javascript://" OnClick="javascript: layerVis\(\'.*?\', 1\);">.*?</a><div align="left" id="(.*?)" style="display: none;">!is', $fudml)) {
		$fudml = preg_replace('!\<div class\="dashed" style\="padding: 3px;" align\="center" width\="100%"\>\<a href\="javascript://" OnClick\="javascript: layerVis\(\'.*?\', 1\);">(.*?)\</a\>\<div align\="left" id\=".*?" style\="display: none;"\>!is', '[spoiler=\1]', $fudml);
		$fudml = str_replace('</div></div>', '[/spoiler]', $fudml);
	}

	$fudml = str_replace('<font face=', '<font font=', $fudml);
	foreach (array('color', 'font', 'size') as $v) {
		while (preg_match('!<font '.$v.'=".+?">.*?</font>!is', $fudml, $m)) {
			$fudml = preg_replace('!<font '.$v.'="(.+?)">(.*?)</font>!is', '['.$v.'=\1]\2[/'.$v.']', $fudml);
		}
	}

	while (preg_match('!<acronym title=".+?">.*?</acronym>!is', $fudml)) {
		$fudml = preg_replace('!<acronym title="(.+?)">(.*?)</acronym>!is', '[acronym=\1]\2[/acronym]', $fudml);
	}
	while (preg_match('!<(o|u)l type=".+?">.*?</\\1l>!is', $fudml)) {
		$fudml = preg_replace('!<(o|u)l type="(.+?)">(.*?)</\\1l>!is', '[list type=\2]\3[/list]', $fudml);
	}

	$fudml = str_replace(
	array(
		'<b>', '</b>', '<i>', '</i>', '<u>', '</u>', '<s>', '</s>', '<sub>', '</sub>', '<sup>', '</sup>', '<del>', '</del>',
		'<div class="pre"><pre>', '</pre></div>', '<div align="center">', '<div align="left">', '<div align="right">', '</div>',
		'<ul>', '</ul>', '<span name="notag">', '</span>', '<li>', '&#64;', '&#58;&#47;&#47;', '<br />', '<pre>', '</pre>'
	),
	array(
		'[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[s]', '[/s]', '[sub]', '[/sub]', '[sup]', '[/sup]', '[del]', '[/del]', 
		'[code]', '[/code]', '[align=center]', '[align=left]', '[align=right]', '[/align]', '[list]', '[/list]',
		'[notag]', '[/notag]', '[*]', '@', '://', '', '[pre]', '[/pre]'
	),
	$fudml);

	while (preg_match('!<img src="(.*?)" border=0 alt="\\1">!is', $fudml)) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="\\1">!is', '[img]\1[/img]', $fudml);
	}
	while (preg_match('!<img class="(r|l)" src="(.*?)" border=0 alt="\\2">!is', $fudml)) {
		$fudml = preg_replace('!<img class="(r|l)" src="(.*?)" border=0 alt="\\2">!is', '[img\1]\2[/img\1]', $fudml);
	}
	while (preg_match('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">\\1</a>!is', '[email]\1[/email]', $fudml);
	}
	while (preg_match('!<a href="(.+?)" target="_blank">\\1</a>!is', $fudml)) {
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">\\1</a>!is', '[url]\1[/url]', $fudml);
	}

	if (strpos($fudml, '<img src="') !== false) {
		$fudml = preg_replace('!<img src="(.*?)" border=0 alt="(.*?)">!is', '[img=\1]\2[/img]', $fudml);
	}
	if (strpos($fudml, '<img class="') !== false) {
		$fudml = preg_replace('!<img class="(r|l)" src="(.*?)" border=0 alt="(.*?)">!is', '[img\1=\2]\3[/img\1]', $fudml);
	}
	if (strpos($fudml, '<a href="mailto:') !== false) {
		$fudml = preg_replace('!<a href="mailto:(.+?)" target="_blank">(.+?)</a>!is', '[email=\1]\2[/email]', $fudml);
	}
	if (strpos($fudml, '<a href="') !== false) {
		$fudml = preg_replace('!<a href="(.+?)" target="_blank">(.+?)</a>!is', '[url=\1]\2[/url]', $fudml);
	}

	if (isset($php)) {
		$fudml = str_replace(array_keys($php), array_values($php), $fudml);
	}

	/* unhtmlspecialchars */
	return reverse_fmt($fudml);
}


function filter_ext($file_name)
{
	include $GLOBALS['FORUM_SETTINGS_PATH'] . 'file_filter_regexp';
	if (empty($GLOBALS['__FUD_EXT_FILER__'])) {
		return;
	}
	if (($p = strrpos($file_name, '.')) === false) {
		return 1;
	}
	return !in_array(strtolower(substr($file_name, ($p + 1))), $GLOBALS['__FUD_EXT_FILER__']);
}

function safe_tmp_copy($source, $del_source=0, $prefx='')
{
	if (!$prefx) {
		 $prefx = getmypid();
	}

	$umask = umask(($GLOBALS['FUD_OPT_2'] & 8388608 ? 0177 : 0111));
	if (!move_uploaded_file($source, ($name = tempnam($GLOBALS['TMP'], $prefx.'_')))) {
		return;
	}
	umask($umask);
	if ($del_source) {
		@unlink($source);
	}
	umask($umask);

	return basename($name);
}

function reverse_nl2br(&$data)
{
	if (strpos($data, '<br />') !== false) {
		return str_replace('<br />', '', $data);
	}
	return $data;
}

	if (!($FUD_OPT_1 & 16777216)) {
		std_error('disabled');
	}
	if (!isset($_GET['start']) || !($start = (int)$_GET['start'])) {
		$start = 0;
	}

	$ppg = $usr->posts_ppg ? $usr->posts_ppg : $POSTS_PER_PAGE;
	$srch = isset($_GET['srch']) ? trim($_GET['srch']) : '';
	$forum_limiter = isset($_GET['forum_limiter']) ? $_GET['forum_limiter'] : '';
	$field = !isset($_GET['field']) ? 'all' : ($_GET['field'] == 'subject' ? 'subject' : 'all');
	$search_logic = (isset($_GET['search_logic']) && $_GET['search_logic'] == 'OR') ? 'OR' : 'AND';
	$sort_order = (isset($_GET['sort_order']) && $_GET['sort_order'] == 'ASC') ? 'ASC' : 'DESC';
	if (!empty($_GET['author'])) {
		$author = $_GET['author'];
		$author_id = q_singleval("SELECT id FROM fud26_users WHERE alias='".addslashes($_GET['author'])."'");
	} else {
		$author = $author_id = '';
	}

	require $GLOBALS['FORUM_SETTINGS_PATH'].'cat_cache.inc';

function fetch_search_cache($qry, $start, $count, $logic, $srch_type, $order, $forum_limiter, &$total)
{
	$wa = text_to_worda($qry);
	$lang =& $GLOBALS['usr']->lang;
	
	if ($lang != 'chinese_big5' && $lang != 'chinese' && $lang != 'japanese') {
		if (count($wa) > 10) {
			$wa = array_slice($wa, 0, 10);
		}
	}

	if (!$wa) {
		return;
	}

	$qr = implode(',', $wa);
	$i = count($wa);

	if ($srch_type == 'all') {
		$tbl = 'index';
		$qt = '0';
	} else {
		$tbl = 'title_index';
		$qt = '1';
	}

	$qry_lck = md5($qr);

	/* remove expired cache */
	q('DELETE FROM fud26_search_cache WHERE expiry<'.(__request_timestamp__ - $GLOBALS['SEARCH_CACHE_EXPIRY']));

	if (!($total = q_singleval("SELECT count(*) FROM fud26_search_cache WHERE query_type=".$qt." AND srch_query='".$qry_lck."'"))) {
		if (__dbtype__ == 'mysql') {
			q("INSERT IGNORE INTO fud26_search_cache (srch_query, query_type, expiry, msg_id, n_match) SELECT '".$qry_lck."', ".$qt.", ".__request_timestamp__.", msg_id, count(*) as word_count FROM fud26_search s INNER JOIN fud26_".$tbl." i ON i.word_id=s.id WHERE word IN(".$qr.") GROUP BY msg_id ORDER BY word_count DESC LIMIT 500");
			if (!($total = (int) db_affected())) {
				return;
			}
		} else {
			q("BEGIN; DELETE FROM fud26_search_cache; INSERT INTO fud26_search_cache (srch_query, query_type, expiry, msg_id, n_match) SELECT '".$qry_lck."', ".$qt.", ".__request_timestamp__.", msg_id, count(*) as word_count FROM fud26_search s INNER JOIN fud26_".$tbl." i ON i.word_id=s.id WHERE word IN(".$qr.") GROUP BY msg_id ORDER BY word_count DESC LIMIT 500; COMMIT;");
		}
	}

	if ($forum_limiter) {
		if ($forum_limiter{0} != 'c') {
			$qry_lmt = ' AND f.id=' . (int)$forum_limiter . ' ';
		} else {
			$cid = (int)substr($forum_limiter, 1);
			$cids = array();
			/* fetch all sub-categories if there are any */
			if (!empty($GLOBALS['cat_cache'][$cid][2])) {
				$cids = $GLOBALS['cat_cache'][$cid][2];
			}
			$cids[] = $cid;
			$qry_lmt = ' AND c.id IN(' . implode(',', $cids) . ') ';
		}
	} else {
		$qry_lmt = '';
	}
	if ($GLOBALS['author_id']) {
		$qry_lmt = ' AND m.poster_id='.$GLOBALS['author_id'].' ';
	}

	$qry_lck = "'" . $qry_lck . "'";

	$total = q_singleval('SELECT count(*)
		FROM fud26_search_cache sc
		INNER JOIN fud26_msg m ON m.id=sc.msg_id
		INNER JOIN fud26_thread t ON m.thread_id=t.id
		INNER JOIN fud26_forum f ON t.forum_id=f.id
		INNER JOIN fud26_cat c ON f.cat_id=c.id
		INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
		LEFT JOIN fud26_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
		LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
		WHERE
			sc.query_type='.$qt.' AND sc.srch_query='.$qry_lck.$qry_lmt.'
			'.($logic == 'AND' ? ' AND sc.n_match>='.$i : '').'
			'.($GLOBALS['is_a'] ? '' : ' AND (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 262146) >= 262146)'));
	if (!$total) {
		return;
	}

	return uq('SELECT u.alias, f.name AS forum_name, f.id AS forum_id,
			m.poster_id, m.id, m.thread_id, m.subject, m.poster_id, m.foff, m.length, m.post_stamp, m.file_id, m.icon, 
			mm.id AS md, (t.root_msg_id = m.id) AS is_rootm, (t.thread_opt & 1) AS is_lckd
		FROM fud26_search_cache sc
		INNER JOIN fud26_msg m ON m.id=sc.msg_id
		INNER JOIN fud26_thread t ON m.thread_id=t.id
		INNER JOIN fud26_forum f ON t.forum_id=f.id
		INNER JOIN fud26_cat c ON f.cat_id=c.id
		INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
		LEFT JOIN fud26_users u ON m.poster_id=u.id
		LEFT JOIN fud26_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
		LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
		WHERE
			sc.query_type='.$qt.' AND sc.srch_query='.$qry_lck.$qry_lmt.'
			'.($logic == 'AND' ? ' AND sc.n_match>='.$i : '').'
			'.($GLOBALS['is_a'] ? '' : ' AND (mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 262146) >= 262146)').'
		ORDER BY sc.n_match DESC, m.post_stamp '.$order.' LIMIT '.qry_limit($count, $start));
}

if (_uid) {
	$admin_cp = $accounts_pending_approval = $group_mgr = $reported_msgs = $custom_avatar_queue = $mod_que = $thr_exch = '';

	if ($usr->users_opt & 524288 || $is_a) {
		if ($is_a) {
			if ($FUD_OPT_1 & 32 && ($avatar_count = q_singleval("SELECT count(*) FROM fud26_users WHERE users_opt>=16777216 AND (users_opt & 16777216) > 0"))) {
				$custom_avatar_queue = '| <a href="adm/admapprove_avatar.php?S='.s.'&amp;SQ='.$GLOBALS['sq'].'">Custom Avatar Queue</a> <span class="GenTextRed">('.$avatar_count.')</span>';
			}
			if ($report_count = q_singleval('SELECT count(*) FROM fud26_msg_report')) {
				$reported_msgs = '| <a href="index.php?t=reported&amp;'._rsid.'">Reported Messages</a> <span class="GenTextRed">('.$report_count.')</span>';
			}

			if ($thr_exchc = q_singleval('SELECT count(*) FROM fud26_thr_exchange')) {
				$thr_exch = '| <a href="index.php?t=thr_exch&amp;'._rsid.'">Topic Exchange</a> <span class="GenTextRed">('.$thr_exchc.')</span>';
			}

			if ($FUD_OPT_2 & 1024 && ($accounts_pending_approval = q_singleval("SELECT count(*) FROM fud26_users WHERE users_opt>=2097152 AND (users_opt & 2097152) > 0"))) {
				$accounts_pending_approval = '| <a href="adm/admaccapr.php?S='.s.'&amp;SQ='.$GLOBALS['sq'].'">Accounts Pending Approval</a> <span class="GenTextRed">('.$accounts_pending_approval.')</span>';
			}

			$q_limit = '';
		} else {
			if ($report_count = q_singleval('SELECT count(*) FROM fud26_msg_report mr INNER JOIN fud26_msg m ON mr.msg_id=m.id INNER JOIN fud26_thread t ON m.thread_id=t.id INNER JOIN fud26_mod mm ON t.forum_id=mm.forum_id AND mm.user_id='._uid)) {
				$reported_msgs = '| <a href="index.php?t=reported&amp;'._rsid.'">Reported Messages</a> <span class="GenTextRed">('.$report_count.')</span>';
			}

			if ($thr_exchc = q_singleval('SELECT count(*) FROM fud26_thr_exchange te INNER JOIN fud26_mod m ON m.user_id='._uid.' AND te.frm=m.forum_id')) {
				$thr_exch = '| <a href="index.php?t=thr_exch&amp;'._rsid.'">Topic Exchange</a> <span class="GenTextRed">('.$thr_exchc.')</span>';
			}

			$q_limit = ' INNER JOIN fud26_mod mm ON f.id=mm.forum_id AND mm.user_id='._uid;
		}

		if ($approve_count = q_singleval("SELECT count(*) FROM fud26_msg m INNER JOIN fud26_thread t ON m.thread_id=t.id INNER JOIN fud26_forum f ON t.forum_id=f.id ".$q_limit." WHERE m.apr=0 AND (f.forum_opt>=2 AND (f.forum_opt & 2) > 0)")) {
			$mod_que = '<a href="index.php?t=modque&amp;'._rsid.'">Moderation Queue</a> <span class="GenTextRed">('.$approve_count.')</span>';
		}
	}
	if ($is_a || $usr->group_leader_list) {
		$group_mgr = '| <a href="index.php?t=groupmgr&amp;'._rsid.'">Group(s) Manager</a>';
	}

	if ($thr_exch || $accounts_pending_approval || $group_mgr || $reported_msgs || $custom_avatar_queue || $mod_que) {
		$admin_cp = '<br /><span class="GenText fb">Admin:</span> '.$mod_que.' '.$reported_msgs.' '.$thr_exch.' '.$custom_avatar_queue.' '.$group_mgr.' '.$accounts_pending_approval.'<br />';
	}
} else {
	$admin_cp = '';
}if (__fud_real_user__ && $FUD_OPT_1 & 1024) {
		$c = q_singleval('SELECT count(*) FROM fud26_pmsg WHERE duser_id='._uid.' AND fldr=1 AND read_stamp=0');
		$private_msg = $c ? '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> You have <span class="GenTextRed">('.$c.')</span> unread private message(s)</a>&nbsp;&nbsp;' : '<a href="index.php?t=pmsg&amp;'._rsid.'" class="UserControlPanel"><img src="theme/default/images/top_pm.png" alt="Private Messaging" /> Private Messaging</a>&nbsp;&nbsp;';
	} else {
		$private_msg = '';
	}if (!isset($th)) {
	$th = 0;
}
if (!isset($frm->id)) {
	$frm->id = 0;
}/* draw search engine selection boxes */
if ($is_a) {
	$c = uq('SELECT f.id, f.name, c.id FROM fud26_fc_view v INNER JOIN fud26_forum f ON f.id=v.f INNER JOIN fud26_cat c ON f.cat_id=c.id ORDER BY v.id');
} else {
	$c = uq('SELECT f.id, f.name, c.id
			FROM fud26_fc_view v
			INNER JOIN fud26_forum f ON f.id=v.f
			INNER JOIN fud26_cat c ON f.cat_id=c.id
			INNER JOIN fud26_group_cache g1 ON g1.user_id='.(_uid ? '2147483647' : '0').' AND g1.resource_id=f.id
			LEFT JOIN fud26_mod mm ON mm.forum_id=f.id AND mm.user_id='._uid.'
			LEFT JOIN fud26_group_cache g2 ON g2.user_id='._uid.' AND g2.resource_id=f.id
			WHERE mm.id IS NOT NULL OR ((CASE WHEN g2.id IS NOT NULL THEN g2.group_cache_opt ELSE g1.group_cache_opt END) & 1) > 0
			ORDER BY v.id');
}
$oldc = $forum_limit_data = ''; $g = $f = array();
if ($forum_limiter) {
	if ($forum_limiter{0} != 'c') {
		$f[$forum_limiter] = 1;
	} else {
		$g[(int)ltrim($forum_limiter, 'c')] = 1;
	}
}

while ($r = db_rowarr($c)) {
	if ($oldc != $r[2]) {
		while (list($k, $i) = each($cat_cache)) {
			$forum_limit_data .= '<option value="c'.$k.'"'.(isset($g[$k]) ? ' selected' : '').'>- '.($tabw = ($i[0] ? str_repeat('&nbsp;&nbsp;&nbsp;', $i[0]) : '')).$i[1].'</option>';
			if ($k == $r[2]) {
				break;
			}
		}
		$oldc = $r[2];
	}
	$forum_limit_data .= '<option value="'.$r[0].'"'.(isset($f[$r[0]]) ? ' selected' : '').'>'.$tabw.'&nbsp;&nbsp;&nbsp;'.$r[1].'</option>';
}

/* user has no permissions to any forum, so as far as they are concerned the search is disabled */
if (!$forum_limit_data) {
	std_error('disabled');
}

function trim_body($body)
{
	/* remove stuff in quotes */
	while (($p = strpos($body, '<table border="0" align="center" width="90%" cellpadding="3" cellspacing="1"><tr><td class="SmallText"><b>')) !== false) {
		if (($pos = strpos($body, '<br></td></tr></table>', $p)) === false) {
			$pos = strpos($body, '<br /></td></tr></table>', $p);
			if ($pos === false) {
				break;
			}
			$e = $pos + strlen('<br /></td></tr></table>');
		} else {
			$e = $pos + strlen('<br></td></tr></table>');
		}
		$body = substr($body, 0, $p) . substr($body, $e);
	}

	$body = strip_tags($body);
	if (strlen($body) > $GLOBALS['MNAV_MAX_LEN']) {
		$body = substr($body, 0, $GLOBALS['MNAV_MAX_LEN']) . '...';
	}
	return $body;
}

	$search_options = tmpl_draw_radio_opt('field', "all\nsubject", "Entire Message\nSubject Only", $field, '&nbsp;&nbsp;');
	$logic_options = tmpl_draw_select_opt("AND\nOR", "AND\nOR", $search_logic);
	$sort_options = tmpl_draw_select_opt("DESC\nASC", "Descending Order\nAscending Order", $sort_order);

	$TITLE_EXTRA = ': Search For '.htmlspecialchars($srch);

	ses_update_status($usr->sid, 'Searching messages');

	if ($srch) {
		if (!($c =& fetch_search_cache($srch, $start, $ppg, $search_logic, $field, $sort_order, $forum_limiter, $total))) {
			$search_data = '<br />
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th class="wa ac">No Results</th></tr>
</table>';
			$page_pager = '';
		} else {
			$i = 0;
			$search_data = '';
			while ($r = db_rowobj($c)) {
				$search_data .= '<tr>
	<td class="RowStyleC vt"><b>'.++$i.'</b></td>
	<td class="'.alt_var('search_alt','RowStyleA','RowStyleB').'">
		<b>Forum</b>: <a href="index.php?t='.t_thread_view.'&amp;frm_id='.$r->forum_id.'&amp;'._rsid.'">'.$r->forum_name.'</a> &laquo;&raquo;
		<b>Posted On</b>: <span class="DateText">'.strftime("%a, %d %B %Y %H:%M", $r->post_stamp).'</span> &laquo;&raquo;
		<b>By:</b> '.(!empty($r->poster_id) ? '<a href="index.php?t=usrinfo&amp;id='.$r->poster_id.'&amp;'._rsid.'">'.$r->alias.'</a>' : $GLOBALS['ANON_NICK'].'' ) .'<br />
		<a href="index.php?t='.d_thread_view.'&amp;goto='.$r->id.'&amp;'._rsid.'#msg_'.$r->id.'">'.$r->subject.'</a><br />
		'.trim_body(read_msg_body($r->foff, $r->length, $r->file_id)).'
		'.(($is_a || $r->md) ? '
<hr /><div class="ModOpt">Moderator Options: <a href="index.php?t=mmod&amp;'._rsid.'&amp;th='.$r->thread_id.'&amp;del='.$r->id.'">Delete</a>
'.($r->is_rootm ? '
 | <a href="javascript://" onClick="javascript: window_open(\'http://timeweather.net/forum/index.php?t=mvthread&amp;'._rsid.'&amp;th='.$r->thread_id.'\', \'th_move\', 300, 400);">Move</a> | <a href="index.php?t=mmod&amp;'._rsid.'&amp;th='.$r->thread_id.'&amp;'.($r->is_lckd ? 'unlock' : 'lock' )  .'=1&amp;SQ='.$GLOBALS['sq'].'">'.($r->is_lckd ? 'Unlock Topic' : 'Lock Topic' )  .'</a>
' : '' )  .'
</div>
		' : '' )  .'
	</td>
</tr>';
			}
			$search_data = '<br />
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr>
	<th> </th>
	<th>'.$total.' Search Results Found</th>
</tr>
'.$search_data.'
</table>';
			if ($FUD_OPT_2 & 32768) {
				$page_pager = tmpl_create_pager($start, $ppg, $total, 'index.php/s/'.urlencode($srch).'/'.$field.'/'.$search_logic.'/'.$sort_order.'/'.$forum_limiter.'/', '/'.urlencode($author).'/'._rsid);
			} else {
				$page_pager = tmpl_create_pager($start, $ppg, $total, 'index.php?t=search&amp;srch='.urlencode($srch).'&amp;field='.$field.'&amp;'._rsid.'&amp;search_logic='.$search_logic.'&amp;sort_order='.$sort_order.'&amp;forum_limiter='.$forum_limiter.'&amp;author='.urlencode($author));
			}
		}
	} else {
		$search_data = $page_pager = '';
	}

if ($FUD_OPT_2 & 2 || $is_a) {
	$page_gen_end = gettimeofday();
	$page_gen_time = sprintf('%.5f', ($page_gen_end['sec'] - $PAGE_TIME['sec'] + (($page_gen_end['usec'] - $PAGE_TIME['usec'])/1000000)));
	$page_stats = $FUD_OPT_2 & 2 ? '<br /><div class="SmallText al">Total time taken to generate the page: '.$page_gen_time.' seconds</div>' : '<br /><div class="SmallText al">Total time taken to generate the page: '.$page_gen_time.' seconds</div>';
} else {
	$page_stats = '';
}
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
<span class="GenText fb">Show:</span> <a href="index.php?t=selmsg&amp;date=today&amp;<?php echo _rsid; ?>&amp;frm_id=<?php echo $frm->id; ?>&amp;th=<?php echo $th; ?>" title="Show all messages that were posted today">Today's Messages</a>&nbsp;<?php echo (_uid ? '<b>::</b> <a href="index.php?t=selmsg&amp;unread=1&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all unread messages">Unread Messages</a>&nbsp;' : '' ) .(!$th ? '<b>::</b> <a href="index.php?t=selmsg&amp;reply_count=0&amp;'._rsid.'&amp;frm_id='.$frm->id.'" title="Show all messages, which have no replies">Unanswered Messages</a>' : ''); ?> <b>::</b> <a href="index.php?t=polllist&amp;<?php echo _rsid; ?>">Show Polls</a> <b>::</b> <a href="index.php?t=mnav&amp;<?php echo _rsid; ?>">Message Navigator</a><br /><img src="blank.gif" alt="" height=2 /><?php echo $admin_cp; ?>
<form name="search" method="get" action="index.php"><?php echo _hs; ?><input type="hidden" name="t" value="search">
<table cellspacing="1" cellpadding="2" class="ContentTable">
<tr><th>Forum Search</th><th class="wa">Search Options</th></tr>
<tr class="RowStyleA">
	<td class="vt nw"><input type="text" name="srch" tabindex="1" value="<?php echo htmlspecialchars($srch); ?>"> <input type="submit" tabindex="2" class="button" name="btn_submit" value="Search"><br /><span class="SmallText"><?php echo $search_options; ?></span></td>
	<td class="RowStyleB SmallText">
<div class="sr">Search in Forum:<br /><select class="SmallText" name="forum_limiter"><option value="">Search All Forums</option><?php echo $forum_limit_data; ?></select></div>
<div class="sr">Search Logic:<br /><select class="SmallText" name="search_logic"><?php echo $logic_options; ?></select></div>
<div class="sr">Sort Results by Date in:<br /><select class="SmallText" name="sort_order"><?php echo $sort_options; ?></select></div>
<div class="sr">Filter by user:<br /><input class="SmallText" type="text" value="<?php echo htmlspecialchars($author); ?>" name="author"></div>
</td>
</tr>
</table></form>
<script>
<!--
document.search.srch.focus();
//-->
</script>
<?php echo $search_data; ?>
<div align="left"><?php echo $page_pager; ?></div>
<br /><div class="ac"><span class="curtime"><b>Current Time:</b> <?php echo strftime("%a %b %e %H:%M:%S %Z %Y", __request_timestamp__); ?></span></div>
<?php echo $page_stats; ?>
</td></tr></table><div class="ForumBackground ac foot">
<b>.::</b> <a href="mailto:<?php echo $GLOBALS['ADMIN_EMAIL']; ?>">Contact</a> <b>::</b> <a href="index.php?t=index&amp;<?php echo _rsid; ?>">Home</a> <b>::.</b>
<p>
<span class="SmallText">Powered by: FUDforum <?php echo $GLOBALS['FORUM_VERSION']; ?>.<br />Copyright &copy;2001-2004 <a href="http://fudforum.org/">FUD Forum Bulletin Board Software</a></span>
</div></body></html>