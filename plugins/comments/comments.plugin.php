<?php

// KioCMS - Kiofol Content Managment System
// plugins/comments/index.php

class Comments extends Plugin
{
	var $edited = array();
	private $edit_mode = false;

	function __construct($connector_id, $owner, $total_comments, $backlink)
	{
		$this->connector_id = $connector_id;
		$this->owner = $owner;
		$this->total_comments = $total_comments;
		$this->backlink = $backlink;
	}

	function getContent()
	{
		global $cfg, $user, $sql, $plug;

		$note = new Notifier();
		$tpl = new PHPTAL('plugins/comments/comments.tpl.html');
		$err = new Error();

		$tpl->entries = '';

		if ($this->total_comments != -1 && !Kio::getConfig('view_only_logged', 'comments'))
		{
			if ($this->total_comments > 0)
			{
				$tpl->backlink = $this->backlink;
				$tpl->cfg = $cfg;
				$tpl->user = $user;
				$tpl->entries = $this->getEntries();
			}
			else
			{
				$note->info('There is no comments.');
			}

			if (!Kio::getConfig('add_only_logged', 'comments') || LOGGED)
			{
				if ($this->edited)
				{
					$form = array(
						'id' => $this->edited['comment_id'],
						'author' => $this->edited['comment_author'],
						'author_id' => $this->edited['comment_author_id'],
						'message' => $this->edited['comment_message']);

					if (!$form['author'])
					{
						$form['author'] = User::getNickname(BY_ID, $this->edited['comment_author_id']);
					}
					
					$this->edit_mode = true;
				}
				else
				{
					$form['author'] = User::$nickname;
				}

				$add = isset($_POST['add']) ? true : false;
				$edit = isset($_POST['edit']) ? true : false;

				// Add or delete
				if (isset($_POST['add']) || $edit)
				{
					$form['author'] = isset($_POST['add']) && LOGGED
						? User::$nickname
						: filter($_POST['author'], 100);
					$form['message'] = filter($_POST['message'], Kio::getConfig('message_max', 'comments'));

					$err->setError('author_empty', t('Author field is required.'))
						->condition(!$form['author']);
					$err->setError('author_exists', t('Entered nickname is registered.'))
						->condition($add && !LOGGED && is_registered($form['author'], 'nickname'));
					$err->setError('message_empty', t('Message field is required.'))
						->condition(!$form['message']);

					// No errors
					if ($err->noErrors())
					{
						// Add
						if (isset($_POST['add']))
						{
							$sql->exec('
								INSERT INTO '.DB_PREFIX.'comments (
									comment_owner, comment_owner_child_id, comment_author,
									comment_author_id, comment_author_ip, comment_added,
									comment_message, comment_backlink)
								VALUES(
									"'.u0.'",
									'.$this->connector_id.',
									"'.(!LOGGED || isset($_POST['edit']) ? $form['author'] : '').'",
									'.UID.',
									"'.IP.'",
									'.TIMESTAMP.',
									"'.$form['message'].'",
									"'.$this->backlink.'")');

							$last = $sql->lastInsertId();

							$sql->exec('
								UPDATE '.DB_PREFIX.$this->owner.'
								SET comments = (comments + 1)
								WHERE id = '.$this->connector_id);

							setcookie(COOKIE.'-comments', 'true', TIMESTAMP + Kio::getConfig('flood_interval', 'comments') + 1, '/');
							redirect(HREF.PATH.'#comment-'.$last);
						}
						// Edit
						else if (isset($_POST['edit']))
						{
							if ($form['author_id'] = User::getId(BY_NICKNAME, $form['author']))
							{
								$form['author'] = '';
							}
							else
							{
								$form['author_id'] = 0;
							}

							$sql->exec('
								UPDATE '.DB_PREFIX.'comments
								SET
									comment_author = "'.$form['author'].'",
									comment_author_id = '.$form['author_id'].',
									comment_message = "'.$form['message'].'"
								WHERE comment_id = '.$this->edited['comment_id']);

							redirect(HREF.$this->edited['comment_backlink'].'#comment-'.$this->edited['comment_id']);
						}
					}
					// Show errors
					else
					{
						$note->error($err->toArray());
					}
				}
				// Deleting entry
				else if (isset($_POST['delete_id']) && ctype_digit($_POST['delete_id']))
				{
					$sql->exec('
						DELETE FROM '.DB_PREFIX.'comments WHERE comment_id = '.$_POST['delete_id'].';
						UPDATE '.DB_PREFIX.$this->owner.' SET comments = (comments - 1) WHERE id = '.$this->connector_id);

					redirect(strpos(REFERER, 'admin') ? REFERER : '#comments');
				}

				//$tpl->comments = $comments;
				$tpl->form = $form;
				$tpl->err = $err->toArray();
			}
			else
			{
				$note->error(sprintf('Dodawanie komentarzy jest możliwe tylko dla <a href="%1$slogin">zalogowanych</a> osób, <a href="%1$sregistration">zarejestruj się</a> jeśli nie masz jeszcze konta.', HREF));
			}
		}
		else if ($this->total_comments != -1)
		{
			$note->error(array('Komentarze są widoczne tylko dla zalogowanych osób.', '<a href="'.HREF.'registration">Zarejestruj się</a> jeśli nie masz jeszcze konta.'));
		}

		$tpl->edit_mode = $this->edit_mode;
		$tpl->total_comments = $this->total_comments;
		$tpl->note = $note;
		return $tpl->execute();
	}

	private function getEntries()
	{
		global $sql;
		
		$start = array_search('edit_comment', Kio::$url);
		$edited_id = $start && ctype_digit(Kio::$url[$start + 1]) ? Kio::$url[$start + 1] : '';

		if (Kio::getConfig('order_by', 'comments') == 'DESC')
		{
			$x = $this->total + 1;
			$ascending = false;
		}
		else
		{
			$x = 1;
			$ascending = true;
		}

		$query = $sql->query('
			SELECT c.comment_id, c.comment_author, c.comment_author_id, c.comment_added,
				c.comment_message, c.comment_backlink, u.nickname, u.group_id, u.avatar
			FROM '.DB_PREFIX.'comments c
			LEFT JOIN '.DB_PREFIX.'users u ON u.id = c.comment_author_id
			WHERE c.comment_owner_child_id = '.$this->connector_id.' AND c.comment_owner = "'.u0.'"
			ORDER BY c.comment_added '.Kio::getConfig('order_by', 'comments'));

		while ($row = $query->fetch())
		{
			$row['x'] = $ascending ? $x++ : $x--;

			if ($edited_id == $row['comment_id'])
			{
				$this->edited = $row;
				$edited_x = $x;
			}
			
			if ($row['comment_author_id'])
			{
				$row['comment_author'] = User::format($row['comment_author_id'], $row['nickname'], $row['group_id']);
			}

			$entries[] = $row;
		}

		return $entries;
	}
}