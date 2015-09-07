<?php
 // KioCMS - Kiofol Content Managment System
// modules/news/entries.php

$pager_url = 'news';
$category_id = 0;

if (u1 == 'category')
	$category_id = (int)u2;

$total = $kio->stats['news_entries'];

if ($category_id)
{
	$category = $sql->setCache('news_categories_'.$category_id)->query('
		SELECT id, name, description, entries
		FROM '.DB_PREFIX.'news_categories
		WHERE id = '.$category_id)->fetch(PDO::FETCH_ASSOC);

	if ($category)
	{
		$total = $category['entries'];
		if ($category['description']) $kio->description = $category['name'].' - '.$category['description'];
		$kio->path['news/category/'.$category_id.'/'.clean_url($category['name'])] = $category['name'];
		$pager_url = 'news/category/'.$category_id.'/'.clean_url($category['name']);
	}
	else
		echo not_found(t('Selected category does not exists.'), array(
			t('Category was moved or deleted.'),
			t('Entered URL is invalid.')));
}

if ($category || !$category_id)
{
	$module->subcodename = 'list';
	$pager = new Pager($pager_url, $total, $cfg->news['limit']);

	$stmt = $sql->setCache('news_'.$category_id.'_'.$pager->current)->query('
		SELECT u.nickname, u.group_id, c.id c_id, c.name c_name, c.description c_description, n.*
		FROM '.DB_PREFIX.'news n
		LEFT JOIN '.DB_PREFIX.'users u ON u.id = n.author_id
		LEFT JOIN '.DB_PREFIX.'news_categories c ON c.id = n.category_id
		WHERE '.($category_id ? 'c.id = '.$category_id.'
			AND ' : '').(LOGGED ? 'n.publication > 0' : 'n.publication = 1').'
			AND n.added < '.TIMESTAMP.'
		ORDER BY '.$cfg->news['sort'].'
		LIMIT '.$pager->limit.'
		OFFSET '.$pager->offset);

	while ($row = $stmt->fetch())
	{
		if ($row['author_id'])
			$row['author'] = User::format($row['author_id'], $row['nickname'], $row['group_id']);
		$row['url_title'] = ($row['c_name'] ? clean_url($row['c_name']).'/' : '').clean_url($row['title']);
		$row['content'] = parse($row['content'], $cfg->news['parsers']);
		$entries[] = $row;
	}

	try
	{
		$tpl = new PHPTAL('modules/news/entries.html');
		$tpl->cfg = $cfg;
		$tpl->entries = $entries;
		$tpl->pagination = $pager->getLinks();
		echo $tpl->execute();
	}
	catch (Exception $e)
	{
		echo template_error($e);
	}
}