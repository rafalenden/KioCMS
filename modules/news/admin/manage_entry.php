<?php
 // KioCMS - Kiofol Content Managment System
// modules/news/admin/manage_entry.php

defined('KioCMS') || exit;

// admin/news/edit/u3
$e = u3 == 'edit' && ctype_digit(u4) ? u4 : '';

// Editing entry
if ($e)
{
	$row = sql_fetch_assoc(sql_query('SELECT * FROM '.DB_PREFIX.'news WHERE id = '.$e));
	// Entry exists
	if ($row)
	{
		$form = $row;
		$form['edit_mode'] = true;
		!$row['author'] && $form['author'] = get_user($row['author_id'], 'id');
		$form['added'] = array(date('j', $row['added']), date('m', $row['added']), date('Y', $row['added']), date('H', $row['added']), date('i', $row['added']));
	}
}
if (!$form['edit_mode'])
{
	$form['author'] = $user->nickname;
	$form['added'] = array(date('j'), date('m'), date('Y'), date('H'), date('i'));
}

// Form values
$add = $_POST['add'] ? true : false;
$edit = $_POST['edit'] ? true : false;

// On form submit
if ($add || $edit)
{
	$form = array(
		'title'       => filter($_POST['title'], 100),
		'category_id' => (int)substr($_POST['category_id'], 0, 10),
		'author'      => filter($_POST['author'], 100),
		'description' => filter($_POST['description'], 100),
		'keywords'    => filter($_POST['keywords'], 100),
		'publication' => (int)$_POST['publication']{0},
		'added'       => array_map('intval', $_POST['added']),
		'content'     => filter($_POST['content'], false, $_POST['html']));

	$errors[] = !$form['title'] ? $lang2['ERROR_TITLE'] : '';
	$errors[] = !$form['author'] ? $lang2['ERROR_AUTHOR'] : '';
	$errors[] = !$form['content'] ? $lang2['ERROR_CONTENT'] : '';

	// No errors
	if (!in_array(true, $errors))
	{
		($form['author_id'] = get_user($form['author'])) && $form['author'] = '';

		// Add
		if ($add)
		{
			sql_query('
				INSERT INTO '.DB_PREFIX.'news (title, author, author_id, added, publication, description, keywords, content, extended_content, category_id, lang)
				VALUES (
					"'.$form['title'].'",
					"'.$form['author'].'",
					'.$form['author_id'].',
					'.(int)mktime($form['added'][3], $form['added'][4], 0, $form['added'][1], $form['added'][0], $form['added'][2]).',
					1,
					"'.$form['description'].'",
					"'.$form['keywords'].'",
					"'.$form['content'].'",
					"'.$form['extended_content'].'",
					'.$form['category_id'].',
					"")')
				? sql_query($form['category_id']
					? array('UPDATE '.DB_PREFIX.'news_categories SET entries = entries + 1 WHERE id = '.$form['category_id'], 'UPDATE '.DB_PREFIX.'stats SET value = value + 1 WHERE key = "posted_news"')
					: 'UPDATE '.DB_PREFIX.'stats SET value = value + 1 WHERE key = "posted_news"').
				  redirect(HREF.'admin/modules/news')
				: negative($lang_system['SQL_ERROR']);
		}
		// Edit
		else
		{
			sql_query('
				UPDATE '.DB_PREFIX.'news
				SET
					title = "'.$form['title'].'",
					author = "'.$form['author'].'",
					author_id = '.$form['author_id'].',
					added = '.mktime($form['added'][3], $form['added'][4], 0, $form['added'][1], $form['added'][0], $form['added'][2]).',
					publication = "'.$form['publication'].'",
					description = "'.$form['description'].'",
					keywords = "'.$form['keywords'].'",
					content = "'.$form['content'].'",
					extended_content = "'.$form['extended_content'].'",
					category_id = '.$form['category_id'].'
				WHERE id = '.$e)
				? positive($lang['SUCCESS_MESSAGE_EDIT']).
				  redirect(HREF.'admin/modules/news')
				: negative($lang_system['SQL_ERROR']);
		}
	}
	// Show errors
	else
		negative($errors);
}
// Neutral message
else
	neutral($lang_system['REQUIRED']);

$query = sql_query('SELECT id, name FROM '.DB_PREFIX.'news_categories');
while ($row = sql_fetch_assoc($query))
	$categories[] = $row;

$tpl = get_template('modules/news/admin/manage_entry');
$tpl->module = $module;
$tpl->system = $system;
$tpl->form = $form;
$tpl->user = $user;
$tpl->entries = $entries;
$tpl->errors = $errors;
$tpl->lang = $lang;
$tpl->lang2 = $lang2;
$tpl->categories = $categories;
$tpl->lang_system = $lang_system;
$tpl->entry = $entry;
$tpl->x = $x;
echo $tpl->execute();