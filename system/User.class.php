<?php
// KioCMS - Kiofol Content Managment System
// includes/session.php

defined('KioCMS') || exit;

class User
{
	protected static $get;
	public static $id = 0;
	public static $nickname, $logname, $forename, $surname, $email, $password,
	$registered, $groupId, $gender, $locality, $country, $communicator,
	$website, $birthdate, $newsletter, $pmNotify, $hideEmail, $avatar, $photo,
	$title, $interests, $signature, $hidden, $blocked, $points, $warnings,
	$pmUnread, $pmNew, $pmInbox, $pmOutbox, $visits, $timeZone, $lastVisit,
	$lastLocation, $ip, $authCode, $httpAgent, $permits, $params, $lang;
	private static $temp;
	private static $array = array();

	const GET_NICKNAME = 1;
	const GET_LOGNAME = 2;
	const GET_ID = 3;
	const BY_NICKNAME = 4;
	const BY_LOGNAME = 5;
	const BY_ID = 6;

	// TODO: $id = User::get(BY_NICKNAME, form['author'])->column('user_id');

	function __construct()
	{
		global $cfg;
		$this->lang = Kio::getConfig('detect_lang') && empty($_COOKIE['KioCMS-'.COOKIE.'-lang']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : $_COOKIE['KioCMS-'.COOKIE.'-lang'];
	}

	public static function detectLang($auto_detect = false)
	{
		self::$lang = $auto_detect && !$_COOKIE[COOKIE.'-lang'] ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : $_COOKIE[COOKIE.'-lang'];
	}

	public static function init()
	{
		global $sql, $kio;
		// Check session and set user variables
		// TODO: Logowanie za pomocą pliku, jak w truecrypt
		$cookie = $_COOKIE[COOKIE.'-login'];
		if ($cookie)
		{
			$user = $sql->query('SELECT * FROM '.DB_PREFIX.'users WHERE id = '.(int)$cookie)->fetch(PDO::FETCH_ASSOC);

			// Correct
			if ($cookie == $user['id'].'.'.sha1($user['auth_code']) && IP == $user['ip'] /* && TIMESTAMP - $user['last_visit'] < $cfg->system['session_time'] */)
			{
				self::$array = & $user;

				self::$id = $user['id'];
				self::$nickname = $user['nickname'];
				self::$logname = $user['logname'];
				self::$email = $user['email'];
				self::$groupId = $user['group_id'];
				self::$pmInbox = $user['pm_inbox'];
				self::$pmOutbox = $user['pm_outbox'];

				self::$lastVisit = $user['last_visit'];


//				foreach ($user as $key => $value)
//					$this->{$key} = $value;

				define('LOGGED', true);
				define('NICKNAME', self::$nickname);
				define('UID', self::$id);
				define('GID', self::$groupId);

				// update online_time
				if (!defined('AJAX'))
					$sql->exec('
						UPDATE '.DB_PREFIX.'users
						SET
							last_visit = '.TIMESTAMP.',
							last_path = "'.PATH.'"
							-- online_time += '.TIMESTAMP.' - last_visit
						WHERE id = '.UID);

				// TODO: Zrobić po zalogowaniu okienki - Wróć do strony: Dziećmarów
				// TODO: zmienić jako prywatne
				// TODO: nakładanie się na siebie pozwoleń
				self::$permits = $user['permits'] ? unserialize($user['permits']) : (Kio::getGroup(GID, 'permits') ? unserialize(Kio::getGroup(GID, 'permits')) : array());
				self::$permits += Kio::getGroup(GID, 'permits') ? unserialize(Kio::getGroup(GID, 'permits')) : array();
				self::$permits = array_unique(self::$permits);
			}
			// Incorrect
			else
			{
				define('LOGGED', false);
				setcookie(COOKIE.'-session', '', 0, '/');
				define('UID', self::$id);
			}
		}
		else
		{
			define('LOGGED', false);
			define('NICKNAME', t('Guest'));
			define('UID', self::$id);
		}

		// Save session
		if (isset($_POST['login']) && !LOGGED)
		{
			self::login();
		}
	}

	public static function toArray()
	{
		return self::$array;

		return array(
			'id' => self::$id,
			'nickname' => self::$nickname,
			'logname' => self::$logname,
			'forename' => self::$forename,
			'surname' => self::$surname,
			'email' => self::$email,
			//'pass' => self::$pass,
			'registered' => self::$registered,
			'group_id' => self::$groupId,
			'gender' => self::$gender,
			'locality' => self::$locality,
			'country' => self::$country,
			'communicator' => self::$communicator,
			'website' => self::$website,
			'birthdate' => self::$birthdate,
			'newsletter' => self::$newsletter,
			//'pm_notify' => self::$pm_notify,
			'hide_email' => self::$hideEmail,
			'avatar' => self::$avatar,
			'photo' => self::$photo,
			'title' => self::$title,
			'interests' => self::$interests,
			'signature' => self::$signature,
			'hidden' => self::$hidden,
			'blocked' => self::$blocked,
			//
			//
			'pm_new' => self::$pmNew,
			'pm_inbox' => self::$pmInbox,
			'pm_outbox' => self::$pmOutbox,
			'visits' => self::$visits,
			'time_zone' => self::$timeZone,
			'last_visit' => self::$lastVisit,
			//'last_path' => self::$lastPath,
			'ip' => self::$ip,
			'auth_code' => self::$authCode,
			'http_agent' => self::$httpAgent,
			'permits' => self::$permits
		//
		);
	}

	public static function loginNameRegistered($logname)
	{
		return strtolower($logname) == strtolower(self::$temp['logname']);
	}

	public static function loginPasswordCorrect($password)
	{
		return md5($password) == self::$temp['pass'];
	}

	public static function login()
	{
		global $sql;

		if ($_POST['logname-session'])
		{
			self::$temp = $sql->query('
				SELECT id, logname, pass, email, auth_code
				FROM '.DB_PREFIX.'users
				WHERE logname = "'.$_POST['logname-session'].'"')->fetch();
		}

		if (self::loginNameRegistered($_POST['password-session'])
			&& self::loginPasswordCorrect($_POST['password-session']))
		{
			$new_session = md5(uniqid());
			$sql->exec('
				UPDATE '.DB_PREFIX.'users
				SET
					visits = visits + 1,
					last_visit = '.TIMESTAMP.',
					last_path = "'.PATH.'",
					auth_code = "'.$new_session.'",
					ip = "'.IP.'",
					http_agent = "'.filter($_SERVER['HTTP_USER_AGENT'], 255).'"
				WHERE id = '.self::$temp['id']);
			setcookie(COOKIE.'-login', self::$temp['id'].'.'.sha1($new_session), TIMESTAMP + (Kio::getConfig('session_time') ? Kio::getConfig('session_time') * 60 : 31536000), '/', '', false, true);
			//redirect(REFERER);
		}
	}

	public static function logout()
	{
		global $sql;

		if (LOGGED)
		{
			$sql->exec('
				UPDATE '.DB_PREFIX.'users
				SET auth_code = "'.self::hash(self::$logname).'"
				WHERE id = '.UID);
		}

		setcookie(COOKIE.'-login', '', 0, '/');
		redirect(REFERER);
	}

	public static function format($id = false, $nickname = false, $group_id = false)
	{
		$group = Kio::getGroup($group_id);

		// $u_color - do czata / mozliwosc uzycia font-size: 40px
		return $nickname ? '<a href="'.HREF.'profile/'.$id.'/'.clean_url($nickname).'" class="nickname"'.($group_id ? ' title="'.$group['name'].'"' : '').'>'.($group['inline'] ? sprintf($group['inline'], $nickname) : $nickname).'</a>' : t('???');
	}

	public static function hash($logname)
	{
		return md5(microtime().$logname.$_SERVER['HTTP_USER_AGENT'].session_id().uniqid(rand(), true));
	}

	public static function getIP()
	{
		if (isset($_SERVER['HTTP_X_REAL_IP']))
			return $_SERVER['HTTP_X_REAL_IP'];
		elseif (isset($_SERVER['HTTP_CLIENT_IP']))
			return $_SERVER['HTTP_CLIENT_IP'];
		elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		elseif (isset($_SERVER['HTTP_X_FORWARDED']))
			return $_SERVER['HTTP_X_FORWARDED'];
		elseif (isset($_SERVER['HTTP_FORWARDED_FOR']))
			return $_SERVER['HTTP_FORWARDED_FOR'];
		elseif (isset($_SERVER['HTTP_FORWARDED']))
			return $_SERVER['HTTP_FORWARDED'];
		else
			return $_SERVER['REMOTE_ADDR'];
	}

	public static function get($get_by, $input)
	{
		global $sql;

		if ($input)
			return $sql->query('SELECT * FROM '.DB_PREFIX.'users WHERE '.$get_by.' = "'.$input.'"')->fetchColumn();
	}

	public static function getNickname($get_by, $input)
	{
		global $sql;

		if ($input)
			return $sql->query('SELECT nickname FROM '.DB_PREFIX.'users WHERE '.$get_by.' = "'.$input.'"')->fetchColumn();
	}

	/**
	 * Gets user logname from database by specified field (ex. id, nickname)
	 * @global object $sql SQL Database instance
	 * @param string $by Inserted to WHERE condition (ex. BY_ID, BY_NICKNAME)
	 * @param mixed $input Holds condition value, int for user id or string for nickname
	 * @return string Returns user logname
	 */
	public static function getLogname($by, $input)
	{
		global $sql;

		if ($input)
			return $sql->query('SELECT logname FROM '.DB_PREFIX.'users WHERE '.$by.' = "'.$input.'"')->fetchColumn();
	}

	public static function getId($by, $input)
	{
		global $sql;

		if ($input)
			return $sql->query('SELECT id FROM '.DB_PREFIX.'users WHERE '.$by.' = "'.$input.'"')->fetchColumn();
	}

	public static function getEmail($by, $input)
	{
		global $sql;

		if ($input)
			return $sql->query('SELECT email FROM '.DB_PREFIX.'users WHERE '.$by.' = "'.$input.'"')->fetchColumn();
	}

	public static function find($get, $by, $input)
	{
		global $sql;

		if ($input)
			return $sql->query('SELECT '.$get.' FROM '.DB_PREFIX.'users WHERE '.$by.' = "'.$input.'"')->fetchColumn();
	}

	public static function hasPermit($permit)
	{
		return in_array($permit, self::$permits) ? true : false;
//		$permit = explode('/', $permit);
//		if ($permit[1])
//			return in_array($permit[1], (array)$this->permit[$permit[0]]) ? true : false;
//		else
//			return in_array($permit[0], (array)$this->permit) ? true : false;
	}

	public static function permitsToArray()
	{
		$permit = explode('/', $permit);

		if ($permit[1])
		{
			return in_array($permit[1], (array)$this->permit[$permit[0]]) ? true : false;
		}
		else
		{
			return in_array($permit[0], (array)$this->permit) ? true : false;
		}
	}
}