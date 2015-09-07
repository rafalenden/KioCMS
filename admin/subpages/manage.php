<?php
 // KioCMS - Kiofol Content Managment System
// admin/news/entries/add.php

defined('KioCMS') || include_once '../ajax.php';

// admin/news/edit/u3
$e = u2 == 'edit' && ctype_digit(u3) ? u3 : '';

// Editing entry
if ($e)
{
	$db = sql_fetch_array(sql_query('SELECT * FROM '.db_prefix.'subpages WHERE id = '.$e));
	if ($db)
	{
		$edit_mode = true;
		$form = $db;
		$form['author'] = $db['author'] ? $db['author'] : identify_user($db['author_id']);
		$form['added'] = array(date('j', $db['added']), date('m', $db['added']), date('Y', $db['added']), date('H', $db['added']), date('i', $db['added']));
	}
}
if (!$edit_mode)
{
	$form['author']  = $user['username'];
	$form['added'] = array(date('j'), date('m'), date('Y'), date('H'), date('i'));
}

// Form values
$form['add'] = $_POST['add'] ? true : false;
$form['edit'] = $_POST['edit'] ? true : false;

if ($form['add'] || $form['edit'])
{
	$form = $_POST;

	$errors[] = !$form['name'] ? $lang['ERROR_TITLE'] : '';
	$errors[] = !$form['author'] ? $lang['ERROR_AUTHOR'] : '';
	$errors[] = !$form['content'] ? $lang['ERROR_CONTENT'] : '';

	// No errors
	if (!in_array(true, $errors))
	{
		$form['author_id'] = (int)identify_user($form['author']);
		$form['author_id'] && $form['author'] = '';
		// Add
		if ($form['add'])
		{
			sql_query('
				INSERT INTO '.db_prefix.'news (title, author, added, description, keywords, content, extended_content, author_id, category_id)
				VALUES (
					"'.$form['title'].'",
					"'.$form['author'].'",
					'.(int)mktime($form['added'][3], $form['added'][4], 0, $form['added'][1], $form['added'][0], $form['added'][2]).',
					1,
					"'.$form['description'].'",
					"'.$form['keywords'].'",
					"'.$form['content'].'",
					"'.$form['extended_content'].'",
					'.$form['author_id'].',
					'.$form['category'].')')
				? sql_query($form['category']
					? array('UPDATE '.db_prefix.'news_categories SET entries = entries + 1 WHERE id = '.$form['category'], 'UPDATE '.db_prefix.'stats SET value = value + 1 WHERE key = "posted_news"')
					: 'UPDATE '.db_prefix.'stats SET value = value + 1 WHERE key = "posted_news"').
				  redirect(local_url.'admin/news')
				: negative(array($lang_system['ERROR_SQL'], sql_error()));
		}
		// Edit
		else
		{
			sql_query('
				UPDATE '.db_prefix.'news
				SET
					title = "'.$form['title'].'",
					author = "'.$form['author'].'",
					added = '.(int)mktime($form['added'][3], $form['added'][4], 0, $form['added'][1], $form['added'][0], $form['added'][2]).',
					/*type = "'.$form['type'].'",*/
					description = "'.$form['description'].'",
					keywords = "'.$form['keywords'].'",
					content = "'.$form['content'].'",
					extended_content = "'.$form['extended_content'].'",
					author_id = '.$form['author_id'].',
					category_id = '.$form['category'].'
				WHERE id = '.$e)
				? positive($lang['SUCCESS_MESSAGE_EDIT']).
				  redirect(local_url.'admin/news')
				: negative(array($lang_system['ERROR_SQL'], sql_error()));
		}
	}
	// Show errors
	else
		negative($errors);
}
// Neutral message
else
	neutral($lang_system['REQUIRED']);

// Form
echo '<form action="'.local_url.'admin/news/'.($edit_mode ? 'edit/'.$e : 'write').'" method="post" id="form"><table class="form">
<tr class="title"><th>&nbsp;</th><td>'.$lang['MAIN_INFORMATIONS'].'</td></tr>
<tr class="top"><th><label for="form-title"'.($errors[0] ? ' class="error"' : '').'><span class="required">*</span> '.$lang['TITLE'].'</label></th><td><input class="text" type="text" name="title" id="form-title" value="'.$form['name'].'" size="35" /></td></tr>
<tr><th><label for="form-category">'.$lang['CATEGORY'].'</label></th><td><select name="category" id="form-category"><option value="0">-</option>';

$query = sql_query('SELECT id, name FROM '.db_prefix.'news_categories');
while ($category = sql_fetch_array($query))
	echo '<option value="'.$category['id'].'"'.($form['category'] == $category['id'] ? ' selected="selected"' : '').'>'.$category['name'].'</option>';

echo '</select></td></tr>
<tr><th><label for="form-author"'.($errors[1] ? ' class="error"' : '').'><span class="required">*</span> '.$lang['AUTHOR'].'</label></th><td><input class="text" size="35" type="text" name="author" id="form-author" value="'.$form['author'].'" /></td></tr>

<tr class="title"><th>&nbsp;</th><td>'.$lang['META'].'</td></tr>
<tr><th><label for="form-description">'.$lang['DESCRIPTION'].'</label></th><td><textarea name="description" id="form-description" rows="3" cols="50">'.$form['description'].'</textarea></td></tr>
<tr><th><label for="form-keywords">'.$lang['KEYWORDS'].'</label></th><td><input class="text" size="35" type="text" name="keywords" id="form-keywords" value="'.$form['KEYWORDS'].'" /></td></tr>

<tr class="title"><th>&nbsp;</th><td>'.$lang['ADDITIONAL'].'</td></tr>
<tr><th rowspan="4"><label>Opcje</label></th><td><label><input name="blocks_headers" type="checkbox" '.($form['blocks_headers'] ? ' checked="checked"' : '').'/> HTML</label></td></tr>
<tr><td><label><input name="styled_usernames" type="checkbox" '.($form['styled_usernames'] ? ' checked="checked"' : '').'/> BBCode</label><div class="description">Formatuje tekst (np. [b]pogrubienie[/b])</div></td></tr>
<tr><td><label><input name="bbcode" type="checkbox" '.($form['bbcode'] ? ' checked="checked"' : '').'/> Emotikony</label></td></tr>
<tr><td><label><input name="comments" type="checkbox" '.($form['comments'] ? ' checked="checked"' : '').'/> Komentarze</label></td></tr>
<tr class="top"><th><label for="form-type">'.$lang['PUBLISHED'].'</label></th><td><select name="type" id="form-type"><option value="1">'.$lang['YES'].'</option><option value="0">'.$lang['NO'].'</option><option value="2">'.$lang['FOR_LOGGED_IN'].'</option></select></td></tr>
<tr class="top"><th><label for="form-added"><span class="required">*</span> '.$lang['PUBLISH_DATE'].'</label></th><td><input type="text" class="auto" size="2" maxlenght="2" name="added[0]" value="'.$form['added'][0].'" /><select name="added[1]">';

for ($x = 1; $x <= 12; $x++)
	echo '<option'.($form['added'][1] == $x ? ' selected="selected"' : '').' value="'.$x.'">'.$lang_system[$x].'</option>';
echo '</select><input type="text" class="auto" size="4" maxlenght="4" name="added[2]" value="'.$form['added'][2].'" />

<input type="text" class="auto" size="2" maxlenght="2" name="added[3]" value="'.$form['added'][3].'" />:<input type="text" class="auto" size="2" maxlenght="2" name="added[4]" value="'.$form['added'][4].'" /><div class="description">'.$lang['PUBLISH_DATE_DESCRIPTION'].'</div></td></tr>

<tr class="title"><th>&nbsp;</th><td>'.$lang['CONTENT'].'</td></tr>
<tr><th><label for="form-content"'.($errors[2] ? ' class="error"' : '').'><span class="required">*</span> '.$lang['BASIC_CONTENT'].'</label></th><td>'.panel('form-content').'<textarea class="auto" name="content" id="form-content" rows="8" cols="50">'.$form['content'].'</textarea></td></tr>

<tr><th><label for="form-extended_content">'.$lang['EXTENDED_CONTENT'].'</label></th><td>'.panel('form-extended_content').'<textarea class="auto" name="extended_content" id="form-extended_content" rows="16" cols="50">'.$form['extended_content'].'</textarea></td></tr>

<tr><th rowspan="2"><label>Przywracanie stanu</label></th><td><label><input name="delete_comments" type="checkbox" '.($form['delete_comments'] ? ' checked="checked"' : '').'/> Usuń komentarze</label></td></tr>
<tr><td><label><input name="reset_views" type="checkbox" '.($form['reset_views'] ? ' checked="checked"' : '').'/> Zeruj licznik wyświetleń</label></td></tr>

<tr class="bottom"><th>&nbsp;</th><td><input class="button submit" type="submit" name="'.($edit_mode ? 'edit' : 'add').'" value="'.($edit_mode ? $lang['SAVE'] : $lang['ADD_ENTRY']).'" /><input type="reset" value="'.$lang_system['RESET'].'" class="button2 reset" title="'.$lang_system['RESET_TITLE'].'" /></td></tr></table></form>';
?>