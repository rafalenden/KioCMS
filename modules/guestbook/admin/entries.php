<?php
 // KioCMS - Kiofol Content Managment System
// modules/guestbook/admin/entries.php

if ($kio->stats['guestbook_entries'])
{
	$pager = new Pager('admin/modules/guestbook', $kio->stats['guestbook_entries']);
	$pager->limit()->sort(array(
		t('Author') => 'nickname',
		t('IP') => 'author_ip',
		t('Message') => 'message',
		t('Added') => 'added'), 'added', 'desc');

	$query = $sql->query('
		SELECT gb.id, gb.added, gb.author, gb.author_id, gb.author_ip, gb.email, gb.website, gb.message, u.nickname, u.group_id
		FROM '.DB_PREFIX.'guestbook AS gb
		LEFT JOIN '.DB_PREFIX.'users AS u ON u.id = gb.author_id
		ORDER BY '.$pager->order.'
		LIMIT '.$pager->limit.'
		OFFSET '.$pager->offset);
	while ($row = $query->fetch())
	{
		if ($row['author_id'])
			$row['author'] = User::format($row['author_id'], $row['nickname'], $row['group_id']);
		$entries[] = $row;
	}

	$tpl = new PHPTAL('modules/guestbook/admin/entries.html');
	$tpl->stats = $kio->stats;
	$tpl->entries = $entries;
	$tpl->sort = $pager->sorters;
	$tpl->limit_form = $pager->limit_form;
	$tpl->pagination = $pager->links();
	echo $tpl->execute();
}
else
	echo '<div class="null">'.$lang_admin['NULL'].'</div>';