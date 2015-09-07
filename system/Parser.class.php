<?php
 // KioCMS - Kiofol Content Managment System
// includes/parser/index.php

defined('KioCMS') || exit;

include_once ROOT.'includes/parser/bbcode/'.($cfg->system['bbcode_parser'] ? $cfg->system['bbcode_parser'].'.php' : 'index.php');
include_once ROOT.'includes/parser/emoticons/'.($cfg->system['emoticons_parser'] ? $cfg->system['emoticons_parser'].'.php' : 'index.php');
include_once ROOT.'includes/parser/censure/'.($cfg->system['censure_parser'] ? $cfg->system['censure_parser'].'.php' : 'index.php');
//eval('$lol = '.var_export($system, true));

// bbcode/transform
function parse($str, $parsers = 12345)
{
	global $system;

	// 1 - BBCode
	if (strpos($str, '[') !== false && substr_count($cfg->system['parsers'].$parsers, 1) == 2)
		$str = preg_replace(array_keys($cfg->system['bbcode']), $cfg->system['bbcode'], $str);

	// 2 - Automatic links generator
	if (substr_count($cfg->system['parsers'].$parsers, 2) == 2)
	{
		$autolinks = array(
			// email@domain
			'#(^|[\n\s\[ <>])(^[\w\d-]+?(?:\.[\w\d\+-]+)*)@([a-z\d-]+(?:\.[a-z\d-]+)*\.[a-z]{2,4})$#ise' => '\'\1<a href="\'.mailto(\'\2@\3\').\'">\'.hash_email(\'\2@\3\').\'</a>\'',
			// xxxx://yyyy
			'#(^|[\n\s\[ <>])([\w]+?://[\w\#$%&~/.\-;:=,?@+]+)#is' => '\1<a href="\2" class="external" rel="nofollow">\2</a>',
			// www|ftp.xxxx.yyyy[/zzzz]
			'#(^|[\n\s\]])((www|ftp)\.[\w\#$%&~/.\-;:=,?@+]+)#is' => '\1<a href="http://\2" class="external" rel="nofollow">\2</a>');
		$str = preg_replace(array_keys($autolinks), $autolinks, $str);

		//'#((www|ftp)\.[a-z0-9_/?\-&%\.]+)#si' => '<a href="http://$1" class="external" rel="nofollow">$1</a>');
		//'#[a-z]+://([-]*[.]?[a-z0-9_/-?&%])*#i' => '<a href="$0" class="external" rel="nofollow">$0</a>',
		//"'$1<a href=\"'.mailto('$2@$3').'\">'.hash_email('$2@$3').'</a>'",
		// [a-z0-9_\+-]+(\.[a-z0-9_\+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,4})
		// ([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is z WordPressa wzięte
		// Manual PHP - '#((?:https?|ftp)://\S+[[:alnum:]]/?)#i' => '<a href="$1" class="external" rel="nofollow">$1</a>',
		// #([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is
		//'#(^| )(www([-]*[.]?[a-z0-9_/-?&%])*)#i' => '<a href="http://$2" class="external" rel="nofollow">$2</a>');
		//'#((?<!//)(www\.\S+[[:alnum:]]/?))#si' => '<a href="http://$1" class="external" rel="nofollow">$1</a>');
	}

	// 3 - Emoticons
	if (substr_count($cfg->system['parsers'].$parsers, 3) == 2) $replace = $cfg->system['emoticons'];
	// 4 - Censure
	if (substr_count($cfg->system['parsers'].$parsers, 4) == 2) $replace += $cfg->system['censure'];
	if ($replace) $str = str_ireplace(array_keys($replace), $replace, $str);

	// 5 - Break line
	if (substr_count($cfg->system['parsers'].$parsers, 5) == 2)
		$str = nl2br($str);

	// Visible spaces
	$str = str_replace(
		array("\t", '  ', '  '),
		array('&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;'), $str);

	return $str;
}
