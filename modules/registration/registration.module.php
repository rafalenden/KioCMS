<?php

// KioCMS - Kiofol Content Managment System
// modules/registration/index.php

class Registration extends Module
{
	private $note;
	public $codename = 'registration';

	function __construct()
	{
		Kio::addTitle(t('Registration'));
		Kio::addBreadcrumb(t('Registration'), 'registration');

		if (LOGGED)
		{
			redirect(LOCAL);
		}
	}

	public function getContent()
	{
		global $sql;

		$this->note = new Notifier();
		$err = new Error();

		// Redirect logged users to front page
		// Activate account
		// registration/activate/234/sfs9fsefsef36dsdgesefe4td
		if (u1 == 'activate' && ctype_digit(u2))
		{
			return $this->accountActivation();
		}
		// Registration is disabled
		else if (Kio::getConfig('type', 'registration') == 0)
		{
			return $this->note->error('Rejestracja została <strong>wstrzymana</strong>.');
		}
		else
		{
//			Kio::addJsCode('$(\'#check_logname\').click(function(){alert();});');
			// Registering
			if (isset($_POST['register']))
			{
				// filter(string, limit)
				$form = array(
					'logname' => $_POST['logname'] ? filter($_POST['logname'], 100) : '',
					'nickname' => $_POST['nickname'] ? filter($_POST['nickname'], 100) : '',
					'pass' => $_POST['pass'] ? filter($_POST['pass'], 100) : '',
					'pass2' => $_POST['pass2'] ? filter($_POST['pass2'], 100) : '',
					'email' => strtolower(filter($_POST['email'], 100)),
					'rules' => $_POST['rules'] ? true : false,
					'newsletter' => $_POST['newsletter'] ? 1 : 0,
					'pm_notify' => $_POST['pm_notify'] ? 1 : 0,
					'hide_email' => $_POST['hide_email'] ? 1 : 0);

				// Errors
				$err->setError('logname_empty', t('Logname field is required.'))
					->condition(!$form['logname']);
				$err->setError('logname_exists', t('The logname you used is already registered.'))
					->condition(is_registered($form['logname'], 'logname'));
				$err->setError('nickname_empty', t('Nickname field is required.'))
					->condition(!$form['nickname']);
				$err->setError('nickname_exists', t('The nickname you used is already registered.'))
					->condition(is_registered($form['nickname'], 'nickname'));
				$err->setError('pass_empty', t('Password field is required.'))
					->condition(!$form['pass']);
				$err->setError('pass_not_match', t('Passwords do not match.'))
					->condition($form['pass'] != $form['pass2'] && $form['pass']);
				$err->setError('email_empty', t('E-mail field is required.'))
					->condition(!$form['email']);
				$err->setError('email_invalid', t('E-mail address you entered is invalid.'))
					->condition($form['email'] && !is_email($form['email']));
				$err->setError('email_exists', t('The e-mail you used is already registered.'))
					->condition(is_registered($form['email'], 'email'));
				$err->setError('rules_not_accepted', t('Accepting the rules is required.'))
					->condition(!$form['rules'] && Kio::getConfig('show_rules', 'registration'));

				// No errors
				if ($err->noErrors())
				{
					$blocked = 1;

					switch (Kio::getConfig('type', 'registration'))
					{
						case 1:
							$blocked = 'NULL';
							$message = ('Rejestracja przebiegła pomyślnie, możesz się teraz zalogować.');
							break;
						case 2:
							$message = 'Rejestracja przebiegła pomyślnie.<br />Wymagana jest aktywacja konta poprzez kliknięcie w odnośnik wysłany na Twoją skrzynkę e-mail.';
							break;
						default:
							$message = 'Rejestracja przebiegła pomyślnie.<br />Wymagana jest aktywacja konta przez administratora, wówczas zostaniesz powiadomiony e-mail&#39;em.';
					}
					// Detect country
					$form['country'] = end(explode('.', gethostbyaddr(IP)));
					$form['country'] = $lang_system['COUNTRIES'][$form['country']] ? $form['country'] : '';

					$stmt = $sql->prepare('
						INSERT INTO '.DB_PREFIX.'users
						SET
							logname = :logname,
							nickname = :nickname,
							email = :email,
							pass = :pass,
							registered = :registered,
							country = :country,
							newsletter = :newsletter,
							pm_notify = :pm_notify,
							hide_email = :hide_email,
							blocked = :blocked,
							time_zone = :time_zone,
							ip = :ip,
							auth_code = :auth_code,
							http_agent = :http_agent;
							
						UPDATE '.DB_PREFIX.'stats
						SET content = content + 1
						WHERE name = "registered_users"');

					$stmt->execute(array(
						'logname' => $form['logname'],
						'nickname' => $form['nickname'],
						'email' => $form['email'],
						'pass' => md5($form['pass']),
						'registered' => TIMESTAMP,
						'country' => $form['country'],
						'newsletter' => $form['newsletter'],
						'pm_notify' => $form['pm_notify'],
						'hide_email' => $form['hide_email'],
						'blocked' => 1,
						'time_zone' => Kio::getConfig('time_zone'),
						'ip' => IP,
						'auth_code' => auth_code($form['logname']),
						'http_agent' => filter($_SERVER['HTTP_USER_AGENT'], 250)));

					$this->note->success($message);
					redirect(HREF.'registration');
				}
				// Errors occurred
				else
				{
					$this->note->error($err->toArray());
				}
			}
//			// No action
//			else
//			{
//				$this->note->info(array(t('Register and enjoy additional services.')));
//			}

			try
			{
				$tpl = new PHPTAL('modules/registration/registration.tpl.html');
				$tpl->form = $form;
				$tpl->entries = $entries;
				$tpl->err = $err->toArray();
				$tpl->note = $this->note;
				return $tpl->execute();
			}
			catch (Exception $e)
			{
				return template_error($e);
			}
		}
	}

	private function accountActivation()
	{
		global $sql;

		Kio::addTitle(t('Account activation'));

		$guest = $sql->query('
			SELECT id, nickname, blocked, auth_code
			FROM '.DB_PREFIX.'users
			WHERE id = '.u2)->fetch(PDO::FETCH_ASSOC);

		if ($guest)
		{
			if ($guest['auth_code'] == u3 && $guest['blocked'] == 1)
			{
				return $this->note->success(array('Twoje konto zostało pomyślnie aktywowane.', 'Dziękujemy.'), false);
			}
			else if ($guest['blocked'] == 0)
			{
				return $this->note->error(sprintf('Konto użytkownika <strong>%s</strong> jest już aktywne.', $guest['nickname']));
			}
			else
			{
				return $this->note->error('Kod aktywacyjny jest <strong>nieprawidłowy</strong>.');
			}
		}
		else
		{
			return $this->note->error(sprintf('Konto numer <strong>%u</strong> nie istnieje.', u2));
		}
	}
}