<?php

// KioCMS - Kiofol Content Managment System
// modules/profile/index.php

class Profile extends Module
{
	public $codename = 'profile';

	public function __construct()
	{
		Kio::addCssFile('modules/profile/profile.css');

		$this->blocks = array(
			'left' => array('user_panel', 'partners', 'news_categories', 'shoutbox'));
	}

	public function getContent()
	{
		global $sql;

		// $kio->disableRegion('left');

		if (u1 || LOGGED)
		{
			// TODO: Zamiast zapytania dla własnego konta dać User::toArray()
			$profile = $sql->query('
				SELECT u.*
				FROM '.DB_PREFIX.'users u
				WHERE u.id = '.(ctype_digit(u1) ? u1 : UID))->fetch();
		}

		if ($profile)
		{
			Kio::addTitle(t('Users'));
			Kio::addBreadcrumb(t('Users'), 'users');

			Kio::addTitle($profile['nickname']);
			Kio::addBreadcrumb($profile['nickname'], 'profile/'.u1.'/'.clean_url($profile['nickname']));

			Kio::setDescription(t('%nickname&apos;s profile', array('%nickname' => $profile['nickname']))
					.($profile['title'] ? ' - '.$profile['title'] : ''));

			Kio::addTabs(array(t('Edit profile') => 'edit_profile/'.u1));

			if ($profile['birthdate'])
			{
				$profile['bd'] = $profile['birthdate'] ? explode('-', $profile['birthdate']) : '';

				// DD Month YYYY (Remaining days to next birthday)
				$profile['birthdate'] = $profile['bd'][2].' '.Kio::$months[$profile['bd'][1]].' '.$profile['bd'][0]
					.' ('.day_diff(mktime(0, 0, 0, $profile['bd'][1], $profile['bd'][2] + 1, date('y')), t('%d days remaining')).')';

				$profile['age'] = get_age($profile['bd'][2], $profile['bd'][1], $profile['bd'][0]);

				if (Plugin::exists('zodiac'))
				{
					require_once ROOT.'plugins/zodiac/zodiac.plugin.php';
					$profile['zodiac'] = Zodiac::get($profile['bd'][2], $profile['bd'][1]);
				}
			}

			if ($profile['http_agent'] && Plugin::exists('user_agent'))
			{
				require_once ROOT.'plugins/user_agent/user_agent.plugin.php';
				$profile['os'] = User_Agent::getOS($profile['http_agent']);
				$profile['browser'] = User_Agent::getBrowser($profile['http_agent']);
			}

			$group = Kio::getGroup($profile['group_id']);
			$profile['group'] = $group['name'] ? ($group['inline'] ? sprintf($group['inline'], $group['name']) : $group['name']) : '';

			if ($profile['gender'])
			{
				$profile['gender'] = $profile['gender'] == 1 ? t('Male') : t('Female');
			}

			try
			{
				// TODO: Zrobić modyfikator dla funkcji o wielu parametrach (teraz jest tylko jeden możliwy)
				$tpl = new PHPTAL('modules/profile/profile.tpl.html');
				$tpl->profile = $profile;
				return $tpl->execute();
			}
			catch (Exception $e)
			{
				return template_error($e);
			}
		}
		else
		{
			return not_found(t('Selected user doesn&apos;t exists.'), array(
				t('This person was deleted from database.'),
				t('Entered URL is invalid.')));
		}
	}
}