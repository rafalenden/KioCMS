<?php
 // KioCMS - Kiofol Content Managment System
// admin/comments/settings.php

defined('KioCMS') || include_once '../ajax.php';

$total = sql_fetch_array(sql_query('SELECT COUNT(`id`) AS `comments` FROM `'.db_prefix.'comments`'));
$total_pages = ceil($total['comments'] / 30);
$p = ctype_digit(u3) ? u3 : '';
$p = $p && $p <= $total_pages && $total['comments'] > 0 && $p > 1 ? $p : 1;
if (is_numeric($_POST['delete_id']))
{
	sql_query('DELETE FROM `'.db_prefix.'comments` WHERE `id` = '.(int)$_POST['delete_id'])
		? redirect(strpos($_SERVER['HTTP_REFERER'], 'admin') ? $_SERVER['HTTP_REFERER'] : '#comments')
		: negative(array($lang_system['ERROR_SQL'], sql_error()));
}
if ($total['comments'])
{
	$query = sql_query('
		SELECT u.`username` '.($cfg->system['styled_usernames'] ? ', u.`style` AS `u_style`, g.`name` AS `g_name`, g.`style` AS `g_style`' : '').', c.`id`, c.`for`, c.`for_id`, c.`backlink`, c.`time`, c.`author`, c.`author_id`, c.`author_ip`, c.`content`
		FROM `'.db_prefix.'comments` AS `c`
		LEFT JOIN `'.db_prefix.'users` AS `u` ON u.`id` = c.`author_id`
		'.($cfg->system['styled_usernames'] ? 'LEFT JOIN `'.db_prefix.'users_groups` AS `g` ON g.`id` = u.`group_id`' : '').'
		ORDER BY c.`id` DESC
		LIMIT 30
		OFFSET '.($p - 1) * 30);
	$pagination = pagination($total_pages, $p, local_url.'admin/comments/entries');
	echo '<fieldset class="sort">awdawdawdaw<br /></fieldset>
<form method="post" id="check_all" action=""><table class="list"><thead>
<tr><td class="pagination" colspan="5">'.$pagination.'<p class="total">Wszystkich komentarzy: <strong>'.$total['comments'].'</strong></p></td></tr>
<tr><th class="first"><input type="checkbox" /></th><th class="second left">Autor</th><th class="third">Dodany</th><th class="sixth left">Treść / Lokacja</th><th class="last">Opcje</th></tr></thead><tfoot><tr><td class="pagination" colspan="5">'.$pagination.'<p class="total">Wszystkich komentarzy: <strong>'.$total['comments'].'</strong></p></td></tr></tfoot><tbody>';
	while ($db = sql_fetch_array($query))
	{
		echo "\n".'<tr id="comment-'.$db['id'].'"><td class="first"><input type="checkbox" name="entry" value="'.$db['id'].'" /></td><td class="second left">'.user($db['author_id'], $db['username'] ? $db['username'] : $db['author'], $db['u_style'], $db['g_name'], $db['g_style']).'<div class="note">'.$db['author_ip'].'</div></td><td class="third">'.timer($cfg->system['date_format'], $db['time']).'</td><td class="sixth left">'.$db['content'].'<div class="note"><a href="'.local_url.$db['backlink'].'#comments" class="block">'.$db['backlink'].'</a></div></td><td class="last"><a href="'.local_url.$db['backlink'].'/edit_comment/'.$db['id'].'#form" class="edit">E</a> | <a  href="#delete-comment-'.$db['id'].'" class="delete" accesskey="'.$db['id'].'">U</a></td></tr>';
	}
	echo '</tbody></table></form>';
}
else
	echo '<div class="null">'.$lang[14].'</div>';
?>