<?php
 // KioCMS - Kiofol Content Managment System
// modules/news/admin/manage_category.php

$info = new Infobox();
$edited_id = u3 == 'edit_category' && ctype_digit(u4) ? u4 : '';

// Editing category
if ($edited_id)
{
	$row = $sql->query('SELECT * FROM '.DB_PREFIX.'news_categories WHERE id = '.$edited_id)->fetch();
	if ($row)
	{
		$form = $row;
		$form['edit_mode'] = true;
	}
}

// Form values
$form['add'] = $_POST['add'] ? true : false;
$form['edit'] = $_POST['edit'] ? true : false;

if ($form['add'] || $form['edit'])
{
	$form['name'] = $_POST['name'];
	$form['description'] = $_POST['description'];

	$errors[0] = !$form['name'] ? $lang2['ERROR_NAME'] : '';

	// No errors
	if (!in_array(true, $errors))
	{
		// Add
		if ($form['add'])
		{
			$sql->query('
				INSERT INTO '.DB_PREFIX.'news_categories (name, description)
				VALUES (
					"'.$form['name'].'",
					"'.$form['descriprion'].'")');
			$info->positive($lang_system['SUCCESS_CATEGORY_ADD']);
			redirect(HREF.'admin/modules/news/categories');
		}
		// Edit
		else
		{
			$sql->query('
				UPDATE '.DB_PREFIX.'news_categories
				SET
					name = "'.$form['name'].'",
					description = "'.$form['description'].'"
				WHERE id = '.$edited_id);
			$info->positive($lang2['SUCCESS_CATEGORY_EDIT']);
			redirect(HREF.'admin/modules/news/categories');
		}
	}
	// Show errors
	else
		$info->negative($errors);
}
// Neutral message
else
	$info->neutral($lang_system['REQUIRED']);

$tpl = new PHPTAL('modules/news/admin/manage_category.html');
$tpl->system = $system;
$tpl->form = $form;
$tpl->errors = $errors;
$tpl->lang2 = $lang2;
$tpl->lang_admin = $lang_admin;
$tpl->lang_system = $lang_system;
echo $tpl->execute();