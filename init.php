<?php
/// KioCMS 1.0.0 - Kiofol Content Managment System
/// Copyright © 2008 by Kiofol Software
/// License: kiofol.com/license
/// Author: Rafał "Endzio" Enden
/// system.php



defined('KioCMS') || exit;

///////////////// TODO:
///////////////// $form->nickname->value
///////////////// $form->nickname->error
///////////////// Form extends Error
// Set important variables
$m = $stylesheets = $module = $system = $errors = $form = $path = $stats = $permit = $groups = array();


/*
  Zarezerwowane zmienne:
  $module, $system, $errors, $form, $path, $permit, $groups, $stats, $keywords,
  $description, $head, $stylesheets, $lang, $lang_system, $m, $left, $right
 */

// Root directory
define('ROOT', dirname(__FILE__).'/');

define('TIMESTAMP', time());
define('DATE_FORMAT', 'Y-m-d');
define('TODAY', date('Y-m-d'));
define('YESTERDAY', date('Y-m-d', TIMESTAMP - 86400));
define('TOMMOROW', date('Y-m-d', TIMESTAMP + 86400));

require_once ROOT.'system/functions.php';

Kio::startTimer();

require_once ROOT.'config.php';
error_reporting(ERRORS);

if (!INSTALLED)
{
	redirect(ROOT.'install.php');
}

require_once ROOT.'system/'.DB_TYPE.'.php';

Kio::loadConfig();
Kio::loadStats();
Kio::loadGroups();

Kio::addTitle(Kio::getConfig('title'));
Kio::setDescription(Kio::getConfig('description'));
Kio::setKeywords(Kio::getConfig('keywords'));
Kio::addHead(Kio::getConfig('header'));

$kio->show_blocks = true;
$kio->blocks = Kio::getConfig('blocks');
$kio->columns = Kio::getConfig('columns');
$kio->functions = array(
	'set_magic_quotes_runtime', 'ini_set', 'date_default_timezone_set', 'mb_strlen');
$kio->functions = array_map('function_exists', array_combine($kio->functions, $kio->functions));

// Za spacje należy użyć \s, np. tanie\skomputery
$kio->spam_words = str_replace(
		// Input
		array(',', ' ', '#'),
		// Output
		array('|', '', ''), Kio::getConfig('spam_words'));

// Characters to replace
$kio->chars = unserialize(Kio::getConfig('chars')) + array(
	// Special chars (Input => Output)
	' ' => '_', '&#92;' => '', '&#34;' => '', '&#039;' => '', '&#96;' => '',
	'&quot;' => '', '&gt;' => '', '&lt;' => '', '&amp;' => '');

// Check timezone_identifiers_list()

$kio->bbcode = include ROOT.'system/parser/bbcode/'
	.(Kio::getConfig('bbcode_parser') ? Kio::getConfig('bbcode_parser').'.php' : 'index.php');

$kio->emoticons = include ROOT.'system/parser/emoticons/'
	.(Kio::getConfig('emoticons_parser') ? Kio::getConfig('emoticons_parser').'.php' : 'index.php');

$kio->censure = include ROOT.'system/parser/censure/'
	.(Kio::getConfig('censure_parser') ? Kio::getConfig('censure_parser').'.php' : 'index.php');

session_start();
//setlocale(LC_ALL, LC); ////
mb_internal_encoding('UTF-8');
set_magic_quotes_runtime(false); ////
ini_set('magic_quotes_gpc', 'Off'); ////
define('IP', User::getIP()); // Get user IP
define('TRANSLATE_DATE', Kio::getConfig('translate_date'));

// Breadcrumb/Path
define('ONLY_IN_TITLE', false);
define('NO_URL', null);



//////////////////
User::detectLang(Kio::getConfig('detect_lang'));
/////////////////
// Check if some functions exists
if (in_array(false, $kio->functions))
{
	require_once ROOT.'system/functions2.php';
}

switch (Kio::getConfig('url_type'))
{
	// www.site.com/?example
	case 1:
		Kio::$url = array_map('clean_url', explode('/', $_SERVER['QUERY_STRING']));
		Kio::$urlPrefix = '?';
		break;
	// www.site.com/example
	case 2:
		Kio::$url = array_map('clean_url', explode('/', substr($_SERVER['REQUEST_URI'], strlen(LOCAL))));
		Kio::$urlPrefix = '';
		break;
	// www.site.com/index.php/example
	default:
		Kio::$url = array_map('clean_url', explode('/', substr($_SERVER['PATH_INFO'], 1)));
		Kio::$urlPrefix = 'index.php/';
}

if (Kio::getConfig('multilang'))
{
	// Language code is setted in URL
	if (is_lang(Kio::$url[0]))
	{
		define('LANG', Kio::$url[0]);
		setcookie(COOKIE.'-lang', Kio::$url[0], TIMESTAMP + 31536000, '/');
		array_shift(Kio::$url);
	}
	// Get preffered user language
	else
	{
		define('LANG', is_lang(User::$lang) ? User::$lang : Kio::getConfig('lang'));
	}
}
else
{
	define('LANG', Kio::getConfig('lang'));
}
//is_lang($kio->url[0]) && array_shift($m).redirect(local_dir.$cfg->system['url_prefix'].implode('/', $m));
//is_lang($kio->url[0]) && array_shift($m);
//$lang_url = '';

for ($i = 0; $i <= 20; $i++)
{
	define('u'.$i, isset(Kio::$url[$i]) ? Kio::$url[$i] : '');
}

Kio::loadLangPhrases();

// Name of system theme (default is Kiofol)
define('THEME', Kio::getConfig('template'));

// Requested system path
define('PATH', substr(implode('/', Kio::$url), 0, 255));

// URL prefix
define('HREF', LOCAL.Kio::$urlPrefix.(Kio::getConfig('multilang') ? LANG.'/' : ''));

// Local referer
define('REFERER', strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) !== false ? $_SERVER['HTTP_REFERER'] : LOCAL);

// Current location
define('CURRENT_URL', 'http://'.$_SERVER['SERVER_NAME'].HREF.PATH);

// Ustawienia takie same jak w E_ERROR
define('BBCODE', 1);
define('AUTOLINKS', 2);
define('EMOTICONS', 3);
define('CENSURE', 4);
define('PRE', 5);

define('TRIM', 1);
define('NO_HTML', 2);
define('ANTISPAM', 3);
define('ANTIFLOOD_IP', 4);
define('ANTIFLOOD_COOKIE', 5);

// Define constants (BBCode and parameters for User::get*)
define('BY_NICKNAME', 'nickname');
define('BY_LOGNAME', 'logname');
define('BY_ID', 'id');

User::init();

// Set timezones and assign day/month names
Kio::setTimeLocales();


define('AUTH', md5(PATH.IP.$_SERVER['HTTP_USER_AGENT'].User::$logname.User::$authCode.DB_HOST.DB_PORT.DB_NAME.DB_USER.CACHE_DIR.date('z')));
define('CORRECT_AUTH', isset($_POST['auth']) && $_POST['auth'] == AUTH ? true : false);
define('SORT_PATTERN', '(/[0-9]+|(/sort/[A-z0-9_]+/(desc|asc)(/[0-9]+)?))?');


//// Blocks
//if (!($kio->blocks = $sql->getCache('blocks')))
//{
//	$query = $sql->query('
//		SELECT * FROM '.DB_PREFIX.'blocks
//		WHERE id NOT IN($cfg[MODULE]['disabled_blocks'])
//		ORDER BY position');
//
//	$sql->putCacheContent('groups', $kio->groups);
//}
// lang_translations {translation_id, translation_phrase_id, translation_value}
// lang_phrases {phrase_id, phrase_value, phrase_holder}
// if (empty($kio->lang[$string])) isset($kio->lang[$string]) ? $string : $sql->exec("INSERT INTO {DB_PREFIX}")
//url(4);
# echo $l->sys['COUNTRIES']['pl'];
# $lang->set('contact');
// t('ERROR_MESSAGE_EMPTY')
// Limit of displayed entries in table
//$cfg->system['limit'] = unserialize(stripslashes($_COOKIE['KioCMS-'.COOKIE.'-limit']));
//$functions = get_defined_functions();
//!in_array(array('printr', 'mb_strlen'), $functions) && printf('hura');
//in_array(array('print', 'mb_strlen'), $functions['user']) && include_once ROOT.'includes/functions2.php';
//define('time_offset', ($user['time_zone'] + $cfg->system['time_offset']) * 3600 + (date('I') ? 3600 : 0));
//define('time_offset', ($user['time_offset'] + $cfg->system['time_offset']) * 3600);
// For printing current time use insted of REQUEST_TIME (ex. date('H:i', local_time))
//define('local_time', now + time_offset);
// eval(base64_decode('ZWNobyAnQ2h1aic7'));
// save_log($system); log_error report
// $arrayone + array(1 => "newvalue");
//negative(array('Czy <strong>nadpisać</strong> istniejącą wersję nową?', '<input type="submit" value="Tak" class="button" /> <input type="submit" value="Nie" class="button" />'));
//session_cache_limiter('must-revalidate');
//header('Cache-Control: public');
// Local directory
//define('LOCAL', $_SERVER['PHP_SELF'] == 'index.php' ? '/' : dirname($_SERVER['PHP_SELF']).'/');
//define('current_url', 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//error_reporting(E_ALL ^ E_NOTICE);
//ini_set('error_reporting', 0);
//if (LOGS) require_once ROOT.'includes/save_log.php';
// strpos($_SERVER['HTTP_REFERER'], $user['last_location']) -> sprawdzanie podczas akcji admina
//eval('$lol = '.var_export($system, true));
