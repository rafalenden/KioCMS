<?php

// KioCMS - Kiofol Content Managment System
// modules/pm/index.php

class PM extends Module
{
	public $codename = 'pm';

	public function __construct()
	{
		Kio::addTitle(t('Private messages'));
		Kio::addBreadcrumb(t('Private messages'), 'pm');

		$this->blocks = array(
			'left' => array('user_panel', 'partners', 'news_categories', 'shoutbox'));
	}

	public function getContent()
	{
		global $sql;

		if (LOGGED)
		{
			$note = new Notifier();

			Kio::addCssFile('modules/pm/pm.css');

			// Tabs
			Kio::addTabs(array(
					// Name => URL
					// 'Podsumowanie' => array('^pm$', 'pm'),
					t('Received (%messages)', array('%messages' => User::$pmInbox)) => 'pm/inbox',
					t('Sent (%messages)', array('%messages' => User::$pmOutbox)) => 'pm/outbox',
					t('Write') => 'pm/write'));

			switch (u1)
			{
				case 'write':
					return $this->getComposeForm();
					break;
				case 'inbox':
				case 'outbox':
					if (u2 == 'read' && ctype_digit(u3))
					{
						return $this->getMessage();
					}
					else
					{
						return $this->getFolder(u1 == 'inbox' ? 0 : 1);
					}
					break;
				default:
					redirect(HREF.'pm/inbox');
//				//$info->negative('Pojemność skrzynki jest na wyczeraniu.');
//				$tpl = new PHPTAL('modules/pm/default.html');
//				$tpl->cfg = $cfg;
//				$tpl->form = $form;
//				$tpl->user = $user;
//				$tpl->note = $note;
//				echo $tpl->execute();
			}
		}
		else
		{
			return no_access(array(
				'Dostęp do prywatnych wiadomości jest możliwy tylko po zalogowaniu się.',
				t('REGISTER')));
		}
	}

	private function getComposeForm()
	{
		global $sql;

		Kio::addTitle(t('Compose message'));
		Kio::addBreadcrumb(t('Compose message'), 'pm/write');

		$err = new Error();
		$note = new Notifier();

		if ((u2 == 'resend' || u2 == 'reply') && ctype_digit(u3))
		{
			if (u2 == 'reply')
			{
				$message = $sql->query('
					SELECT connector_id, subject
					FROM '.DB_PREFIX.'pm
					WHERE id = '.u3.' AND folder != 1')->fetch(PDO::FETCH_ASSOC);

				$form['subject'] = 'Re: '.$message['subject'];
			}
			else
			{
				$message = $sql->query('
					SELECT connector_id, subject, message
					FROM '.DB_PREFIX.'pm
					WHERE id = '.(int)u3.' AND folder = 1')->fetch(PDO::FETCH_ASSOC);

				$form['subject'] = $message['subject'];
				$form['message'] = $message['message'];
			}

			$form['receiver'] = User::getNickname(BY_ID, $message['connector_id']);
		}
		else if (ctype_digit(u2))
		{
			$form['receiver'] = User::getNickname(BY_ID, u2);
		}

		if (isset($_POST['send']))
		{
			// Form values
			$form = array(
				'receiver' => filter($_POST['receiver'], 100),
				'subject' => filter($_POST['subject'], 100),
				'save' => $_POST['save'],
				'bbcode' => $_POST['bbcode'] ? BBCODE : 0,
				'emoticons' => $_POST['emoticons'] ? EMOTICONS : 0,
				'autolinks' => $_POST['autolinks'] ? AUTOLINKS : 0,
				'message' => filter($_POST['message'], 250));

			$err->setError('receiver_empty', t('ERROR_RECEIVER_EMPTY'))
				->condition(!$form['receiver']);
			$err->setError('receiver_not_exists', t('ERROR_RECEIVER_NOT_EXISTS'))
				->condition($form['receiver'] && !User::getId(BY_NICKNAME, $form['receiver']));
			$err->setError('subject_empty', t('ERROR_SUBJECT_EMPTY'))
				->condition(!$form['subject']);
			$err->setError('message_empty', t('ERROR_MESSAGE_EMPTY'))
				->condition(!$form['message']);

			// No errors
			if ($err->noErrors())
			{
				$form['receiver'] = User::getId(BY_NICKNAME, $form['receiver']);
				$form['message'] = cut($form['message'], Kio::getConfig('message_max', 'pm'));
				$form['parsers'] = $form['bbcode'].$form['autolinks'].$form['emoticons'].CENSURE.PRE;

				$stmt = $sql->prepare('
					INSERT INTO '.DB_PREFIX.'pm
						(sent, owner_id, connector_id, subject, message, folder, is_read, parsers)
					VALUES
						(:sent, :owner_id, :connector_id, :subject, :message, :folder, :is_read, :parsers)'.
						($form['save'] ? ', (:sent, :owner_id, :connector_id, :subject, :message, :folder, :is_read, :parsers)' : ''));

				$stmt->execute(array(
					'sent' => TIMESTAMP,
					'owner_id' => $form['receiver'],
					'connector_id' => UID,
					'subject' => $form['subject'],
					'message' => $form['message'],
					'folder' => 0,
					'is_read' => 0,
					'parsers' => $form['parsers']));

				setcookie(COOKIE.'-pm', 'true', TIMESTAMP + Kio::getConfig('flood_interval', 'pm') + 1, '/');
				$note->success('Wiadomość została wysłana.');
				redirect(HREF.'pm/inbox');
			}
			// Show errors
			else
			{
				$note->error($err->toArray());
			}
		}
		else
		{
			$note->info(array(t('WELCOME_MESSAGE'), t('REQUIRED')));
		}

		try
		{
			$tpl = new PHPTAL('modules/pm/write.tpl.html');
			$tpl->err = $err->toArray();
			$tpl->form = $form;
			$tpl->note = $note;
			return $tpl->execute();
		}
		catch (Exception $e)
		{
			return template_error($e);
		}
	}

	private function getFolder($folder_id)
	{
		global $sql;

		Kio::addTitle(t(ucfirst(u1)));
		Kio::addBreadcrumb(t(ucfirst(u1)), 'pm/'.u1);

		$note = new Notifier();

		$this->subcodename = 'box';

		$pager = new Pager('pm/'.u1, User::${'pm'.ucfirst(u1)}, Kio::getConfig('limit', 'pm'));
		$pager->sort(array(
			t('Subject') => 'subject',
			t('Message') => 'message',
			u1 == 'outbox' ? t('To') : t('From') => 'nickname',
			t('Sent') => 'sent'), 'sent', 'asc');

		// Reset new messages counter
		if (User::$pmNew)
		{
			$sql->exec('UPDATE '.DB_PREFIX.'users SET pm_new = 0 WHERE id = '.UID);
		}


		if (isset($_POST['action']) && !empty($_POST['messages']))
		{
			$action_messages = implode(', ', array_map('intval', $_POST['messages']));

			switch ($_POST['action'])
			{
				// Mark messages as read
				case 'read':
					$sql->exec('
						UPDATE '.DB_PREFIX.'pm
						SET is_read = 1
						WHERE id IN('.$action_messages.')
							AND folder = '.$folder_id.'
							AND owner_id = '.UID);
					break;
				// Mark messages as unread
				case 'unread':
					$sql->exec('
						UPDATE '.DB_PREFIX.'pm
						SET is_read = 0
						WHERE id IN('.$action_messages.')
							AND folder = '.$folder_id.'
							AND owner_id = '.UID);
					break;
				// Delete messages
				case 'delete':
					$sql->exec('
						DELETE FROM '.DB_PREFIX.'pm
						WHERE id IN('.$action_messages.')
							AND folder = '.$folder_id.'
							AND owner_id = '.UID);
			}

			redirect(HREF.PATH);
		}

		$stmt = $sql->query('
			SELECT pm.*, u.nickname, u.group_id
			FROM '.DB_PREFIX.'pm pm
			LEFT JOIN '.DB_PREFIX.'users u ON u.id = pm.connector_id
			WHERE pm.owner_id = '.UID.' AND pm.folder = '.$folder_id.'
			ORDER BY '.$pager->orderBy.'
			LIMIT '.$pager->limit.'
			OFFSET '.$pager->offset);

		if ($stmt->rowCount())
		{
			$messages = array();
			
			while ($row = $stmt->fetch())
			{
				if ($row['connector_id'])
				{
					$row['nickname'] = User::format($row['connector_id'], $row['nickname'], $row['group_id']);
				}

				$messages[] = $row;
			}

			try
			{
				$tpl = new PHPTAL('modules/pm/pm.tpl.html');
				$tpl->messages = $messages;
				$tpl->sort = $pager->sorters;
				$tpl->total = User::${'pm'.ucfirst(u1)};
				$tpl->max = Kio::getConfig(u1.'_max', 'pm');
				$tpl->note = $note;
				$tpl->pager = $pager;
				$tpl->pagination = $pager->getLinks();
				return $tpl->execute();
			}
			catch (Exception $e)
			{
				return template_error($e);
			}
		}
		else
		{
			return $note->info(t('There is no messages in the box.'));
		}
	}

	private function getMessage()
	{
		global $sql;

		Kio::addTitle(t(ucfirst(u1)));
		Kio::addBreadcrumb(t(ucfirst(u1)), 'pm/'.u1);

		// Get message content
		$message = $sql->query('
			SELECT pm.*, u.nickname, u.group_id, u.avatar
			FROM '.DB_PREFIX.'pm pm
			LEFT JOIN '.DB_PREFIX.'users u ON u.id = pm.connector_id
			WHERE pm.id = '.(int)u3.' AND pm.owner_id = '.UID)->fetch(PDO::FETCH_ASSOC);

		// Message exists
		if ($message)
		{
			Kio::addTitle($message['subject']);
			Kio::addBreadcrumb($message['subject'], 'pm/'.u1.'/read/'.u3);

			$this->subcodename = 'read';

			// Sender/Recipient has id (is registered)
			if ($message['connector_id'])
			{
				$message['nickname'] = User::format($message['connector_id'], $message['nickname'], $message['group_id']);
			}

			// Mark as read
			if (!$message['is_read'])
			{
				$sql->exec('
					UPDATE '.DB_PREFIX.'pm
					SET is_read = 1
					WHERE id = "'.(int)$message['id'].'"');
			}

			try
			{
				$tpl = new PHPTAL('modules/pm/read.tpl.html');
				$tpl->message = $message;
				return $tpl->execute();
			}
			catch (Exception $e)
			{
				return template_error($e);
			}
		}
		else
		{
			return not_found();
		}
	}
}