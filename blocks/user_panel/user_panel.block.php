<?php

// KioCMS - Kiofol Content Managment System
// blocks/user_panel/index.php

class User_Panel extends Block
{

	public function getContent()
	{
		// User is logged in
		if (LOGGED)
		{
			$this->subcodename = 'logged';
			$tpl = new PHPTAL('blocks/user_panel/logged.html');
			$tpl->user = User::format(User::$id, User::$nickname, User::$groupId);

			$pm_item = User::$pmNew ? array(t('Messages <strong>(New: %new)</strong>', array('%new' => $user->pm_new)), 'pm/inbox') : array(t('Messages'), 'pm');

			$tpl->items = items(array(
					// Item name, url, comparison
					$pm_item[0] => HREF.$pm_item[1],
					t('Administration') => HREF.'admin',
					t('Edit profile') => HREF.'edit_profile',
					t('Log out') => HREF.'logout'));

			return $tpl->execute();
		}
		// User is not logged in
		else
		{
			$err = new Error();
			$note = new Notifier('note-user_panel');
			$this->subcodename = 'not_logged';
			$form = array('logname' => null, 'password' => null);

			if ($_POST['login'] && $_POST['user_panel'])
			{
				$form['logname'] = $_POST['logname-session'] ? filter($_POST['logname-session'], 100) : '';
				$form['password'] = $_POST['password-session'] ? $_POST['password-session'] : '';

				$err->setError('logname_empty', t('Logname field is required.'))
					->condition(!$form['logname']);
				$err->setError('logname_not_exists', t('Entered logname is not registered.'))
					->condition(!User::loginNameRegistered($form['logname']));
				$err->setError('password_empty', t('Password field is required.'))
					->condition(!$form['password']);
				$err->setError('password_incorrect', t('ERROR_PASS_INCORRECT'))
					->condition($form['password'] && !User::loginPasswordCorrect($form['password']));

				if ($err->noErrors())
				{
					redirect('./');
				}
				else
				{
					$note->error($err->toArray());
				}
			}

			$tpl = new PHPTAL('blocks/user_panel/not_logged.html');
			$tpl->note = $note;
			$tpl->form = $form;
			$tpl->err = $err->toArray();
			return $tpl->execute();
		}
	}
}