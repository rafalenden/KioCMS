<?php
 // KioCMS - Kiofol Content Managment System
// modules/news/admin/categories.php

$total_categories = current($sql->query('SELECT COUNT(id) AS categories FROM '.DB_PREFIX.'news_categories')->fetch());

if ($total_categories)
{
	$pager = new Pager('admin/modules/news/categories', $total_categories);
	$pager->limit();
	$pager->sort(array(
		$lang2['ID'] => 'id',
		$lang2['CATEGORY_NAME'] => 'name',
		$lang2['DESCRIPTION'] => 'description',
		$lang2['TOTAL_ENTRIES'] => 'entries'), 'id', 'desc');

	$query = $sql->query('
		SELECT *
		FROM '.DB_PREFIX.'news_categories
		ORDER BY '.$pager->order.'
		LIMIT '.$pager->limit.'
		OFFSET '.$pager->offset);
	while ($row = $query->fetch())
		$categories[] = $row;

	$tpl = new PHPTAL('modules/news/admin/categories.html');
	$tpl->system = $system;
	$tpl->total_categories = $total_categories;
	$tpl->sorters = $pager->sorters;
	$tpl->limit_form = $pager->limit_form;
	$tpl->categories = $categories;
	$tpl->lang_admin = $lang_admin;
	$tpl->lang_system = $lang_system;
	$tpl->pagination = $pager->paginate();
	echo $tpl->execute();
}
else
	echo $lang[14];