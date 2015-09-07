<?php
 // KioCMS - Kiofol Content Managment System
// modules/news/admin/settings.php

$info = new Infobox();
$save = $_POST['save'] ? true : false;
$form = $save ? $_POST['form'] : $news;
$form['bbcode'] = ($save ? $_POST['bbcode'] : $news['bbcode']) ? 1 : 0;
$form['sort'] = $save ? $form['sort'] : explode(' ', $form['sort']);
$form['blocks'] = $save ? array_diff($blocks, $_POST['blocks']) : ($news['blocks'] ? explode(', ', $news['blocks']) : '');

if ($save)
{
	$errors[0] = !$form['limit'] ? 'Pole <strong>wpisów na stronę</strong> nie może zostać puste.' : '';
	$errors[1] = !$form['sort'] ? 'Należy określić <strong>sortowanie wpisów</strong>.' : '';

	if (!in_array(true, $errors))
	{
		$form['sort'] = $form['sort'][0].' '.$form['sort'][1];
		save_config($news, 'news', HREF.'admin/modules/news/settings');
	}
	else
		$info->negative($errors);
}
else
	$info->neutral(array($lang_admin['MODULE_SETTINGS'], $lang_system['REQUIRED']));

$tpl = new PHPTAL('modules/news/admin/settings.html');
$tpl->form = $form;
$tpl->lang2 = $lang2;
$tpl->lang_admin = $lang_admin;
$tpl->info = $info->show();
$tpl->columns = form_columns();
$tpl->blocks = form_blocks();
echo $tpl->execute();