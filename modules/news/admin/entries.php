<?php
 // KioCMS - Kiofol Content Managment System
// modules/news/admin/entries/index.php

if ($kio->stats['news_entries'])
{
	$pager = new Pager('admin/modules/news', $kio->stats['news_entries']);
	$pager->limit()->sort(array(
		t('ID') => 'n_id',
		t('Title') => 'n_title',
		t('Language') => 'lang',
		t('Content') => 'content',
		t('Author') => 'nickname',
		t('Category') => 'c_name',
		t('Added') => 'added'), 'added', 'desc');

	$query = $sql->query('
		SELECT u.nickname, u.group_id, c.id c_id, c.name c_name, c.description c_description, n.*, n.id n_id, n.title n_title
		FROM '.DB_PREFIX.'news n
		LEFT JOIN '.DB_PREFIX.'users u ON u.id = n.author_id
		LEFT JOIN '.DB_PREFIX.'news_categories c ON c.id = n.category_id
		ORDER BY '.$pager->order.'
		LIMIT '.$pager->limit.'
		OFFSET '.$pager->offset);
	while ($row = $query->fetch())
	{
		if ($row['author_id'])
			$row['author'] = User::format($row['author_id'], $row['nickname'], $row['group_id']);
		$row['url_title'] = ($row['c_name'] ? clean_url($row['c_name']).'/' : null).clean_url($row['title']);
		$entries[] = $row;
	}

	$tpl = new PHPTAL('modules/news/admin/entries.html');
	$tpl->stats = $kio->stats;
	$tpl->entries = $entries;
	$tpl->sort = $pager->sorters;
	$tpl->limit_form = $pager->limit_form;
	$tpl->pagination = $pager->links();
	echo $tpl->execute();
}
else
	echo $lang_admin['NULL'];