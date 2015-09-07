<?php

// KioCMS - Kiofol Content Managment System
// modules/login/index.php

class Login extends Module
{
	public $codename = 'login';

	public function __construct()
	{
		Kio::addTitle(t('Log in'));
		Kio::addBreadcrumb(t('Log in'), 'login');
	}

	public function getContent()
	{
		return self::getForm();
	}

	public static function getForm($errors = array())
	{
		global $cfg;

		if (LOGGED)
		{
			redirect(REFERER);
		}

		$note = new Notifier();
		$err = new Error();

		if ($errors)
		{
			$note->error($errors);
		}

		if ($_POST['login'] && $_POST['module'])
		{
			$form = array(
				'logname' => $_POST['logname-session'] ? filter($_POST['logname-session'], 100) : '',
				'password' => $_POST['password-session'] ? filter($_POST['password-session'], 100) : '');

			$err->setError('empty_logname', t('Logname field is required.'))
				->condition(!$form['logname']);
			$err->setError('logname_not_exists', t('The logname you used isn&apos;t registered.'))
				->condition($form['logname'] && !User::loginNameRegistered($form['logname']));
			$err->setError('password_empty', t('Password field is required.'))
				->condition(!$form['password']);
			$err->setError('password_invalid', t('Password is invalid.'))
				->condition($form['password'] && !User::loginPasswordCorrect($form['password']));

			$err->noErrors() ? redirect(REFERER) : $note->restore()->error($err->toArray());
		}

		$tpl = new PHPTAL('modules/login/form.html');
		$tpl->form = $form;
		$tpl->err = $err->toArray();
		$tpl->note = $note;
		echo $tpl->execute();
	}
}
