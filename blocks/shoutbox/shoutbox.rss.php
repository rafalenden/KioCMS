<?php

// KioCMS - Kiofol Content Managment System
// blocks/shoutbox/index.php

class Shoutbox extends Block
{
	public function __construct()
	{
		$this->name = t('Shoutbox');
		Kio::addStyle('blocks/shoutbox/shoutbox.css');
	}

	public function getContent()
	{
		global $sql, $user, $cfg;
		
		//Lang::load('blocks/shoutbox/lang.*.php');
		
		$err = new Error();
		$note = new Notifier('note-shoutbox');
		
		$form = array();
		$form['author'] = $user->nickname;

		if ($_POST['reply-shoutbox'])
		{
			$form['author'] = LOGGED ? $user->nickname : filter($_POST['author-shoutbox'], 100);
			$form['message'] = filter($_POST['message-shoutbox'], $cfg->shoutbox['message_max']);

			$err->author_empty(t('Field <strong>author</strong> can not be empty.'), !$form['author']);
			$err->author_exists(t('Entered <strong>nickname</strong> is registered.'), !LOGGED && is_registered($form['author']));
			$err->message_empty(t('Field <strong>message</strong> can not be empty.'), !$form['message']);

			// No errors
			if (!$err->count())
			{
				$sql->exec('
					INSERT INTO ' . DB_PREFIX . 'shoutbox (added, author, message, author_id, author_ip)
					VALUES (
						' . TIMESTAMP . ',
						"' . $form['author'] . '",
						"' . cut($form['message'], $cfg->shoutbox['message_max']) . '",
						' . $user->id . ',
						"' . IP . '")', 'shoutbox.txt');
				$note->success(t('Entry was added successfully.'));
				redirect(HREF . PATH . '#shoutbox');
			}
			// Show errors
			else
			{
				$note->error($err);
			}
		}

		// If cache for shoutbox doesn't exists
		if (!$entries = $sql->getCache('shoutbox'))
		{
			$query = $sql->query('
				SELECT u.nickname, u.group_id, s.added, s.author, s.author_id, s.message
				FROM ' . DB_PREFIX . 'shoutbox s, ' . DB_PREFIX . 'users u
				WHERE u.id = s.author_id
				ORDER BY s.id DESC
				LIMIT ' . $cfg->shoutbox['limit']);

			while ($row = $query->fetch())
			{
				if ($row['author_id'])
				{
					$row['author'] = User::format($row['author_id'], $row['nickname'], $row['group_id']);
					$row['message'] = parse($row['message'], $cfg->shoutbox['parser']);
				}
				$entries[] = $row;
			}

			$sql->putCacheContent('shoutbox', $entries);
		}

		try
		{
			$tpl = new PHPTAL('blocks/shoutbox/sbox_overall.html');
			$tpl->cfg = $cfg;
			$tpl->entries = $entries;
			$tpl->err = $err->toArray();
			$tpl->form = $form;
			$tpl->note = $note;
			$tpl->user = $user;
			return $tpl->execute();
		}
		catch (Exception $e)
		{
			return template_error($e->getMessage());
			//echo Note::error($e->getMessage());
		}
	}
}