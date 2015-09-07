<?php
// KioCMS - Kiofol Content Managment System
// plugins/comments/action.php

$form['author']  = filter($_POST['author'], 100);
$form['content'] = filter($_POST['content'], $comments['content_max'] + 1);

if ($form['add'])
{
	if (LOGGED) $form['author'] = $user->nickname;
	else
		if (is_registered($form['author']))
			$errors[0] = $lang_comments['ERROR_AUTHOR2'];
}
if (!$form['author']) $errors[1] = $lang_comments['ERROR_AUTHOR'];
if (!$form['content']) $errors[2] = $lang_comments['ERROR_CONTENT'];

// No errors
if (!in_array(true, $errors))
{
	// Add
	if ($form['add'])
	{
		try
		{
			$sql->exec('
				INSERT INTO '.DB_PREFIX.'comments (holder, connector_id, author, author_id, author_ip, added, content, backlink)
				VALUES(
					"'.u0.'",
					'.$connector_id.',
					"'.(!LOGGED || $_POST['edit'] ? $form['author'] : '').'",
					'.$user->id.',
					"'.IP.'",
					'.TIMESTAMP.',
					"'.cut($form['content'], $comments['content_max']).'",
					"'.$backlink.'")');
			$last_id = $sql->lastInsertId();
			$sql->exec('UPDATE '.DB_PREFIX.$holder_sql.' SET comments = (comments + 1) WHERE id = '.$connector_id);
			setcookie('KioCMS-'.COOKIE.'-comments', 'true', TIMESTAMP + $comments['flood_interval'] + 1, '/');
			redirect(HREF.PATH.('#comment-'.$last_id));
		}
		catch (Exception $e)
		{
			$info->negative($lang_system['SQL_ERROR']);
		}
	}
	// Edit
	else
	{
		// Trzeba wykonać tylko get_user, ponieważ is_registered jest zbędne i wykonuje 1 niepotrzebne zapytanie
		if (is_registered($form['author']))
		{
			$form['author_id'] = get_user($form['author'], 'nickname');
			$form['author'] = '';
		}
		else
			$form['author_id'] = 0;

		try
		{
			$sql->exec('
				UPDATE '.DB_PREFIX.'comments
				SET
					author = "'.$form['author'].'",
					author_id = '.$form['author_id'].',
					content = "'.$form['content'].'",
					backlink = "'.$backlink.'"
				WHERE id = '.$edited_id);
			redirect(HREF.$backlink.'#comment-'.$edited_id);
		}
		catch (Exception $e)
		{
			$info->negative($lang_system['SQL_ERROR']);
		}
	}
}
// Show errors
else
	$info->negative($errors);