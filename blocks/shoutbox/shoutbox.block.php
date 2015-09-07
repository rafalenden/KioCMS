<?php

// KioCMS - Kiofol Content Managment System
// blocks/shoutbox/index.php

class Shoutbox extends Block
{

	public function __construct($attributes = array())
	{
		parent::__construct($attributes);

		Kio::addCssFile('blocks/shoutbox/shoutbox.css');
	}

	public function getContent()
	{
		global $sql;

		//Lang::load('blocks/shoutbox/lang.*.php');

		$err = new Error();
		$note = new Notifier('note-shoutbox');

		$form['author'] = LOGGED ? User::$nickname : '';
		$form['message'] = '';

		if (isset($_POST['reply-shoutbox']))
		{
			$form['author'] = LOGGED ? User::$nickname : filter($_POST['author-shoutbox'], 100);
			$form['message'] = filter($_POST['message-shoutbox'], Kio::getConfig('message_max', 'shoutbox'));

			$err->setError('author_empty', t('Author field is required.'))
				->condition(!$form['author']);
			$err->setError('author_exists', t('Entered nickname is registered.'))
				->condition(!LOGGED && is_registered($form['author']));
			$err->setError('message_empty', t('Message field is required.'))
				->condition(!$form['message']);

			// No errors
			if ($err->noErrors())
			{
				$sql->exec('
					INSERT INTO '.DB_PREFIX.'shoutbox (added, author, message, author_id, author_ip)
					VALUES (
						'.TIMESTAMP.',
						"'.$form['author'].'",
						"'.cut($form['message'], Kio::getConfig('message_max', 'shoutbox')).'",
						'.UID.',
						"'.IP.'")');
				
				$sql->clearCache('shoutbox');

				$note->success(t('Entry was added successfully.'));
				redirect(HREF.PATH.'#shoutbox');
			}
			// Show errors
			else
			{
				$note->error($err->toArray());
			}
		}

		// If cache for shoutbox doesn't exists
		if (!$entries = $sql->getCache('shoutbox'))
		{
			$query = $sql->query('
				SELECT u.nickname, u.group_id, s.added, s.author, s.author_id, s.message
				FROM '.DB_PREFIX.'shoutbox s
				LEFT JOIN '.DB_PREFIX.'users u ON u.id = s.author_id
				ORDER BY s.id DESC
				LIMIT '.Kio::getConfig('limit', 'shoutbox'));

			while ($row = $query->fetch())
			{
				if ($row['author_id'])
				{
					$row['author'] = User::format($row['author_id'], $row['nickname'], $row['group_id']);
					$row['message'] = parse($row['message'], Kio::getConfig('parser', 'shoutbox'));
				}
				$entries[] = $row;
			}

			$sql->putCacheContent('shoutbox', $entries);
		}

		try
		{
			$tpl = new PHPTAL('blocks/shoutbox/shoutbox.tpl.html');
			$tpl->entries = $entries;
			$tpl->err = $err->toArray();
			$tpl->form = $form;
			$tpl->note = $note;
			return $tpl->execute();
		}
		catch (Exception $e)
		{
			return template_error($e->getMessage());
			//echo Note::error($e->getMessage());
		}
	}
}