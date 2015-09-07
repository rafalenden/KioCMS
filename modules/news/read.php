<?php
 // KioCMS - Kiofol Content Managment System
// modules/news/read.php

$stmt = $sql->setCache('news_read_'.u2)->query('
	SELECT u.nickname, u.group_id, c.id c_id, c.name c_name, c.description c_description, n.*
	FROM '.DB_PREFIX.'news n
	LEFT JOIN '.DB_PREFIX.'users AS u ON u.id = n.author_id
	LEFT JOIN '.DB_PREFIX.'news_categories c ON c.id = n.category_id
	WHERE n.id = '.u2);

if ($entry = $stmt->fetch(PDO::FETCH_ASSOC))
{
	if ($entry['description'])
		$kio->description = $entry['description'];
	if ($entry['keywords'])
		$kio->keywords =  $entry['keywords'];
	if ($entry['c_name'])
		$kio->addPath($entry['c_name'], 'news/category/'.$entry['c_id'].'/'.clean_url($entry['c_name']));
	if ($entry['author_id'])
		$entry['author'] = User::format($entry['author_id'], $entry['nickname'], $entry['group_id']);

	$entry['url'] = 'news/read/'.u2.'/'.($entry['c_name'] ? clean_url($entry['c_name']).'/' : '').clean_url($entry['title']);

	$module->subcodename = 'read';
	$kio->addPath($entry['title'], $entry['url']);

	try
	{
		$tpl = new PHPTAL('modules/news/read.html');
		$tpl->cfg = $cfg;
		$tpl->entry = $entry;
		$tpl->comments = $plug->comments(u2, 'news', $entry['comments'], $entry['url']);
		echo $tpl->execute();
	}
	catch (Exception $e)
	{
		echo template_error($e);
	}
}
else
	not_found(t('Selected entry number does not exists.'));