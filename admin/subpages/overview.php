<?php
 // KioCMS - Kiofol Content Managment System
// admin/subpages/overview.php

defined('KioCMS') || include_once '../ajax.php';

list($total_subpages) = sql_fetch_array(sql_query('SELECT COUNT(id) AS total_subpages FROM '.db_prefix.'subpages'));

if ($total_subpages)
{
	$sort = sort_elements(2, array('id', 'asc'), 'admin/subpages', 's.', array(
		'ID' => 'id',
		'Tytuł' => 'title',
		'Treść' => 'content',
		'Autor' => 'author_id',
		'Dodana' => 'added',
		'Modyfikowana' => 'modified',
		'Dostęp' => 'access_type'));

	$total_pages = ceil($total_subpages / 30);
	$p = is_numeric(u3) ? u3 : '';
	$p = $p && $p <= $total_pages && $total_subpages > 0 && $p > 1 ? $p : 1;

	$query = sql_query('
		SELECT u.nickname, g.name AS g_name, g.style AS g_style, s.*
		FROM '.db_prefix.'subpages AS s
		LEFT JOIN '.db_prefix.'users AS u ON u.id = s.author_id
		LEFT JOIN '.db_prefix.'users_groups AS g ON g.id = u.group_id
		ORDER BY '.$sort['by'].'
		LIMIT 30
		OFFSET '.($p - 1) * 30);
	if ($total_pages > 1)
	{
		include_once root_dir.'includes/pagination'.$cfg->system['pagination_type'].'.php';
		$pagination = pagination($total_pages, $p, local_url.'admin/news/');
	}
	echo '<form method="post" id="check_all" action=""><table class="list"><thead>
<tr>
<td colspan="7">'.$pagination.'<input type="submit" class="button" name="delete_selected" value="Usuń zaznaczone" /></td>
</tr>
<tr>
<th class="first tight"><input type="checkbox" /></th>
<th class="second left" colspan="2">'.$sort['links'][0].' / '.$sort['links'][1].' / '.$sort['links'][2].'</th>
<th class="third">'.$sort['links'][3].'</th>
<th class="fourth">'.$sort['links'][4].' / '.$sort['links'][5].'</th>
<th class="fifth">'.$sort['links'][6].'</th>
<th class="last">Opcje</th>
</tr>
</thead>
<tfoot><tr><td class="pagination" colspan="7">'.$pagination.'<p class="total">Wszystkich wpisów: <strong>'.$total_subpages.'</strong></p></td></tr></tfoot><tbody>';
	while ($db = sql_fetch_array($query))
	{
		if ($db['access'] == 0) $access = 'Nikt';
		elseif ($db['access'] == 1) $access = 'Wszyscy';
		elseif ($db['access'] == 2) $access = 'Zalogowani';
		elseif (!is_numeric($db['access'])) $access = 'Na hasło';
		$comments = $db['comments'] == '-1' ? $db['comments'] = 'Wyłączone' : $db['comments'];
		$views = $db['views'] == '-1' ? $db['views'] = 'Wyłączone' : $db['views'];

		echo "\n".'<tr><td class="first tight"><label><input type="checkbox" name="entry" value="'.$db['id'].'" /></label></td><td class="second tight">'.$db['id'].'</td><td class="third left"><a href="'.local_url.$db['id'].'/'.clean_url($db['title']).'" class="block">'.$db['title'].'<span class="note">'.nl2br($db['content']{100} ? substr($db['content'], 0, 100).'...' : $db['content']).'</span></a></td><td class="fourth">'.user($db['author_id'], $db['nickname'] ? $db['nickname'] : $db['author'], $db['u_style'], $db['g_name'], $db['g_style']).'</td><td class="fifth"><div class="description">'.timer($cfg->system['date_format'], $db['time']).'</div><div class="description">'.timer($cfg->system['date_format'], $db['modified']).'</div></td><td class="sixth">'. $access.'</td><td class="last"><a href="'.local_url.'admin/subpages/edit/'.$db['id'].'" class="edit">E</a> | <a href="'.local_url.'guestbook/delete/'.$db['id'].'" class="delete">U</a></td></tr>';
	}
	echo '</tbody></table></form>';
}
else
	echo '<div class="null">'.$lang[14].'</div>';
?>