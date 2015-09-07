<?php

// KioCMS - Kiofol Content Managment System
// modules/edit_profile/index.php

class Edit_Profile extends Module
{
	public $codename = 'edit_profile';

	public function getContent()
	{
		global $sql;

		if (!LOGGED)
		{
			return no_access('By mieć dostęp do edycji profilu musisz się zalogować.');
		}

		$note = new Notifier();
		$err = new Error();

		$edit = isset($_POST['edit']) ? true : false;
		$countries = include 'lang/countries.php';
		asort($countries);

		//Edit user by ID
		if (ctype_digit(u1))
		{
			$profile = $sql->query('
				SELECT u.*
				FROM '.DB_PREFIX.'users u
				WHERE u.id = '.u1)->fetch(PDO::FETCH_ASSOC);

			if ($profile)
			{
				Kio::addTitle(t('Users'));
				Kio::addBreadcrumb(t('Users'), 'users');

				Kio::addTitle($profile['nickname'].' - '.t('Edit profile'));

				Kio::addBreadcrumb($profile['nickname'], 'profile/'.u1);
				Kio::addBreadcrumb(t('Edit profile'), 'edit_profile/'.u1);

				$form = $profile;
			}
			else
			{
				return not_found(t('Selected user doesn&apos;t exists.'), array(
					t('This person was deleted from database.'),
					t('Entered URL is invalid.')));
			}
		}
		// Edit own profile
		else
		{
			$profile = User::toArray();

			Kio::addTitle(t('Edit profile'));
			Kio::addBreadcrumb(t('Edit profile'), 'edit_profile');
		}

		$form = $profile;
		$form['password'] = '';
		$form['password2'] = '';
		$form['birthdate'] = explode('-', $profile['birthdate']);
		$form['newsletter'] = $profile['newsletter'] ? 1 : 0;
		$form['pm_notify'] = $profile['pm_notify'] ? 1 : 0;
		$form['hide_email'] = $profile['hide_email'] ? 1 : 0;

		if (!u1 || $profile)
		{
			// Edit profile
			if (!empty($edit))
			{
				$form = array(
					'nickname' => Kio::getConfig('allow_change_nick', 'edit_profile') ? filter($_POST['nickname'], 100) : User::$nickname,
					'password' => filter($_POST['password'], 100),
					'password2' => filter($_POST['password2'], 100),
					'email' => strtolower(filter($_POST['email'], 100)),
					'forename' => $_POST['forename'],
					'surname' => $_POST['surname'],
					'gender' => $_POST['gender'],
					'locality' => $_POST['locality'],
					'country' => !empty($countries[$_POST['country']]) ? $_POST['country'] : '',
					'communicator' => $_POST['communicator'],
					'website' => $_POST['website'],
					'birthdate' => array_map('intval', (array)$_POST['birthdate']),
					'newsletter' => isset($_POST['newsletter']) ? 1 : 0,
					'pm_notify' => isset($_POST['pm_notify']) ? 1 : 0,
					'hide_email' => isset($_POST['hide_email']) ? 1 : 0,
					'avatar' => $_FILES['avatar']['error'] == 0 && !$_POST['delete_avatar'] ? $_FILES['avatar'] : array(),
					'delete_avatar' => isset($_POST['delete_avatar']) ? 1 : 0,
					'photo' => isset($_FILES['photo']) ? $_FILES['photo'] : null,
					'delete_photo' => isset($_POST['delete_photo']) ? 1 : 0,
					'title' => $_POST['title'],
					'interests' => $_POST['interests'],
					'signature' => $_POST['signature']);

				$allowed_types = array(
					'image/png' => 'png',
					'image/jpeg' => 'jpg',
					'image/gif' => 'gif');

				// Nickname
				$err->setError('nickname_empty', t('ERROR_NICKNAME_EMPTY'))
					->condition(!$form['nickname']);
				$err->setError('nickname_exists', t('ERROR_NICKNAME_EXISTS'))
					->condition(
						Kio::getConfig('allow_change_nick', 'edit_profile')
						&& $form['nickname']
						&& strtolower($form['nickname']) != strtolower($profile['nickname'])
						&& is_registered($form['nickname']));

				// Password
				$err->setError('password_differ', t('ERROR_PASSWORD_DIFFER'))
					->condition($form['password'] != $form['password2']);

				// E-mail
				$err->setError('email_empty', t('ERROR_EMAIL_EMPTY'))
					->condition(!$form['email']);
				if ($form['email'])
				{
					$err->setError('email_invalid', t('ERROR_EMAIL_INVALID'))
						->condition($form['email'] && !is_email($form['email']));
					$err->setError('email_exists', t('ERROR_EMAIL_EXISTS'))
						->condition(
							$form['email'] != $profile['email']
							&& is_email($form['email'])
							&& is_registered($form['email'], 'email'));
				}

				// Birthdate
				$err->setError('birthdate_invalid', t('ERROR_BIRTHDATE'))
					->condition(array_sum($form['birthdate']) > 0 && !is_date('Y-n-j', $form['birthdate'][0].'-'.$form['birthdate'][1].'-'.$form['birthdate'][2]));

				// Avatar
				if ($form['avatar'])
				{
					$err->avatar_invalid_type(t('ERROR_ava'))
						->condition(!in_array($form['avatar']['type'], array_keys($allowed_types)));
					$err->avatar_exceeded_max_size(t('ERROR_ava'))
						->condition(
							Kio::getConfig('avatar_size_max', 'edit_profile')
							&& !$err->isError('avatar_invalid_type')
							&& $form['avatar']['size'] > Kio::getConfig('avatar_size_max', 'edit_profile'));
				}

				// No errors
				if ($err->noErrors())
				{
					if ($form['delete_avatar'])
					{
						unlink(ROOT.'images/avatars/'.$profile['id'].'.'.User::$avatar);
					}

					if ($form['avatar'])
					{
						move_uploaded_file($_FILES['avatar']['tmp_name'], ROOT.'images/avatars/'.$profile['id'].'.'.$allowed_types[$form['avatar']['type']]);

						if ($allowed_types[$form['avatar']['type']] != User::$avatar)
						{
							unlink(ROOT.'images/avatars/'.$profile['id'].'.'.User::$avatar);
						}
					}

					$form['birthdate'] = array_sum($form['birthdate']) > 0
						? $form['birthdate'][0].'-'.$form['birthdate'][1].'-'.$form['birthdate'][2]
						: '';

					$sql->exec('
						UPDATE '.DB_PREFIX.'users
						SET nickname		= "'.(Kio::getConfig('allow_change_nick', 'edit_profile') ? $form['nickname'] : User::$nickname).'",
							'.($form['password'] ? 'pass = "'.md5($form['password']).'",' : '').'
							email			= "'.$form['email'].'",
							forename		= "'.$form['forename'].'",
							surname			= "'.$form['surname'].'",
							gender			= '.($form['gender'] == 1 || $form['gender'] == 2 ? (int)$form['gender'] : 0).',
							locality		= "'.$form['locality'].'",
							country			= "'.$form['country'].'",
							communicator	= "'.$form['communicator'].'",
							website			= "'.$form['website'].'",
							birthdate		= "'.$form['birthdate'].'",
							newsletter		= '.$form['newsletter'].',
							pm_notify		= '.$form['pm_notify'].',
							hide_email		= '.$form['hide_email'].',
							'.($form['avatar'] ? 'avatar = "'.$allowed_types[$form['avatar']['type']].'",' : ($form['delete_avatar'] ? 'avatar = "",' : '')).'
							title			= "'.$form['title'].'",
							interests		= "'.$form['interests'].'",
							signature		= "'.$form['signature'].'"
						WHERE id = '.$profile['id']);

					$note->success(t('Your profile was modified successfully.'));

					redirect(HREF.'edit_profile');
				}
				else
				{
					$note->error($err->toArray());
				}
			}

			try
			{
				$tpl = new PHPTAL('modules/edit_profile/edit_profile.tpl.html');
				$tpl->profile = $profile;
				$tpl->countries = $countries;
				$tpl->allow_change_nick = Kio::getConfig('allow_change_nick', 'edit_profile');
				$tpl->form = $form;
				$tpl->err = $err->toArray();
				$tpl->note = $note;
				return $tpl->execute();
			}
			catch (Exception $e)
			{
				return template_error($e);
			}
		}
	}
}