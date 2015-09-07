<?php

// KioCMS - Kiofol Content Managment System
// modules/guestbook/index.php
//class Guestbook extends Module, Form
class guestbook extends Module
{
	private $note, $err, $form;
	public $codename = 'guestbook';
	private $edit_mode = false;

	function __construct()
	{
		Kio::addTitle(t('Guestbook'));
		Kio::addBreadcrumb(t('Guestbook'), 'guestbook');

		$this->note = new Notifier();
	}

	private function formSumbit()
	{
		global $sql;

		$form['author'] = isset($_POST['add']) && LOGGED ? User::$nickname : filter($_POST['author'], 100);
		$form['email'] = strtolower(filter($_POST['email'], 100));
		$form['website'] = filter($_POST['website'], 100);
		$form['message'] = filter($_POST['message'], Kio::getConfig('message_max', 'guestbook'), TRIM.NO_HTML.ANTISPAM.ANTIFLOOD_COOKIE, 'guestbook');

		$this->err
			->setError('author_empty', t('Author field is required.'))
			->condition(empty($form['author']))
			->setError('author_exists', t('The nickname you used belongs to a registered user.'))
			->condition(isset($_POST['add']) && !LOGGED && is_registered($form['author']))
			->setError('email_invalid', t('E-mail address you entered is invalid.'))
			->condition(empty($form['email']) && !is_email($form['email']))
			->setError('message_empty', t('Message field is required.'))
			->condition(empty($form['message']))
			->setError('message_spam', t('ERROR_MESSAGE_SPAM'))
			// TODO: coś w stylu Kio::isSpam('guestbook')
			->condition(/* Kio::getConfig('spam-guestbook') */)
			->setError('flood', t(defined('FLOOD') && FLOOD == 1 ? 'ERROR_FLOOD' : 'ERROR_FLOOD2'))
			->condition(!$this->edit_mode && defined('FLOOD'))
			->setError('incorrect_auth', t('ERROR_INCORRECT_AUTH'))
			->condition($_POST['auth'] != AUTH);

		// No errors
		if ($this->err->noErrors())
		{
			// Add
			if (isset($_POST['add']))
			{
				// Nie działa rollback
				//$sql->beginTransaction();
				$sql->exec('
					INSERT INTO '.DB_PREFIX.'guestbook
						(added, author, author_id, author_ip, email, website, message)
					VALUES(
						'.TIMESTAMP.',
						"'.(!LOGGED ? $form['author'] : '').'",
						'.UID.',
						"'.IP.'",
						"'.$form['email'].'",
						"'.($form['website'] && !strpos($form['website'], '://') ? 'http://' : '').$form['website'].'",
						"'.$form['message'].'")');

				$last_id = $sql->lastInsertId();

				$sql->exec('
					UPDATE '.DB_PREFIX.'stats
					SET stat_value = stat_value + 1
					WHERE stat_name = "entries"
						AND stat_owner = "guestbook"');


				$sql->clearCacheGroup('guestbook_*');
				$sql->clearCache('stats');

				setcookie(COOKIE.'-guestbook', true, TIMESTAMP + Kio::getConfig('flood_interval', 'guestbook'), '/');

				$this->note->success(array(
					t('Entry was added successfully.'),
					t('<a href="#entry-'.$last_id.'">Go to entry</a>.')));

				redirect(HREF.'guestbook');
				//$sql->commit();
			}
			// Edit
			else
			{
				$form['author_id'] = User::getId(BY_NICKNAME, $form['author']);

				if ($form['author_id'])
				{
					$form['author'] = '';
				}

				// Dwukrotny limit treści dla moderatorów
				$sql->exec('
						UPDATE '.DB_PREFIX.'guestbook
						SET
							author = "'.$form['author'].'",
							author_id = '.(int)$form['author_id'].',
							email = "'.$form['email'].'",
							website = "'.$form['website'].'",
							message = "'.filter($_POST['message'], Kio::getConfig('message_max', 'guestbook') * 1.5).'"
						WHERE id = '.$edited_id);

				$sql->clearCacheGroup('guestbook_*');

				$this->note->success(t('Entry was modified successfully.'));
				redirect(HREF.'guestbook');
			}
		}
		// Show errors
		else
		{
			$this->note->restore()
				->error($this->err->toArray());
		}

		return $form;
	}

	public function getEntries()
	{
		global $sql;

		if (Kio::getConfig('order_by', 'guestbook') == 'DESC')
		{
			$x = ($this->pager->items + 1) - $this->pager->offset;
			$y = '$x--;';
		}
		else
		{
			$x = $this->pager->offset;
			$y = '$x++;';
		}
		
		$entries = $sql->getCache('guestbook_'.$this->pager->current);

		if (empty($entries))
		{
			$stmt = $sql->query('
				SELECT gb.id, gb.added, gb.author, gb.email, gb.website, gb.message, gb.author_id, gb.author_ip,
					u.nickname, u.group_id, u.avatar, u.signature
				FROM '.DB_PREFIX.'guestbook gb
				LEFT JOIN '.DB_PREFIX.'users u ON u.id = gb.author_id
				ORDER BY gb.id '.Kio::getConfig('order_by', 'guestbook').'
				LIMIT '.$this->pager->limit.'
				OFFSET '.$this->pager->offset);

			if ($stmt->rowCount() > 0)
			{
				while ($row = $stmt->fetch())
				{
					eval($y);
					$row['number'] = $x;
					if ($row['author_id'])
					{
						$row['author'] = User::format($row['author_id'], $row['nickname'], $row['group_id']);
					}
					$row['message'] = parse($row['message'], Kio::getConfig('parsers', 'guestbook'));
					$row['signature'] = $row['signature'] ? parse($row['signature'], Kio::getConfig('parsers', 'guestbook')) : '';
					$entries[] = $row;
				}

				$sql->putCacheContent('guestbook_'.$this->pager->current, $entries);
			}
			else
			{
				$this->note->info('Jeszcze nikt nie dodał żadnego wpisu.');
			}
		}

		return $entries;
	}

	public function getContent()
	{
		global $sql;

		$this->err = new Error();
		$this->pager = new Pager('guestbook', Kio::getStat('entries', 'guestbook'), Kio::getConfig('limit', 'guestbook'));
		$show_form = true;

		$entries = $this->getEntries();
		
		// Editing entry
		if (ctype_digit(u2))
		{
			// guestbook/edit/u2
			$edited_id = u1 == 'edit' ? u2 : '';

			if (!User::hasPermit('guestbook edit'))
			{
				$this->note->error(t('You don&apos;t have access to edit entries.'));

				$show_form = false;
			}
			else if ($edited_id)
			{
				$row = $sql->query('
					SELECT id, added, author, author_id, author_ip, email, website, message
					FROM '.DB_PREFIX.'guestbook
					WHERE id = '.$edited_id)->fetch();

				// Entry exists
				if ($row)
				{
					$form = $row;
					$this->edit_mode = true;

					if (!$row['author'])
					{
						$form['author'] = User::getNickname(BY_ID, $row['author_id']);
					}
				}
				else
				{
					$this->note->error(t('Selected entry doesn&apos;t exist.'));
				}
			}
		}

		if (!$this->edit_mode)
		{
			$form['author'] = User::$nickname;
		}

		// Form action
		$add = isset($_POST['add']) ? true : false;
		$edit = isset($_POST['edit']) ? true : false;

		// On form submit
		if ($add || $edit)
		{
			$form = $this->formSumbit();
		}
		// Deleting entry
		else if (isset($_POST['delete_id'])
			&& ctype_digit($_POST['delete_id'])
			&& $_POST['auth'] == AUTH
			&& User::hasPermit('guestbook delete'))
		{
			$sql->exec('
				UPDATE '.DB_PREFIX.'stats SET content = content - 1 WHERE name = "guestbook_entries";
				DELETE FROM '.DB_PREFIX.'guestbook WHERE id = '.$_POST['delete_id']);
			
			$sql->clearCacheGroup('guestbook_*');
		}

		try
		{
			$tpl = new PHPTAL('modules/guestbook/guestbook.tpl.html');
			$tpl->message_limit = Kio::getConfig('message_max', 'guestbook');
			$tpl->form = $form;
			$tpl->edit_mode = $this->edit_mode;
			$tpl->entries = $entries;
			$tpl->err = $this->err->toArray();
			$tpl->show_form = $show_form;
			$tpl->note = $this->note;
			$tpl->pagination = $this->pager->getLinks();
			return $tpl->execute();
		}
		catch (Exception $e)
		{
			return template_error($e);
		}
	}

	
}
