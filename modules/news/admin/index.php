<?php
 // KioCMS - Kiofol Content Managment System
// modules/news/admin/index.php

$kio->path['admin/modules/news'] = t('News');

// Tabs
$kio->tabs = tabs(array(
	t('Entries') => array('admin/modules/news'.SORT_PATTERN.'$', 'admin/modules/news'),
	t('Add entry') => array('admin/modules/news/add$', 'admin/modules/news/add'),
	t('Categories') => array('admin/modules/news/categories', 'admin/modules/news/categories'),
	t('Add category') => array('admin/modules/news/add_category$', 'admin/modules/news/add_category'),
	t('Settings') => array('admin/modules/news/settings$', 'admin/modules/news/settings')));

switch (u3)
{
	case 'settings': include_once ROOT.'modules/news/admin/settings.php'; break;
	case 'add_category':
	case 'edit_category': include_once ROOT.'modules/news/admin/manage_category.php'; break;
	case 'categories': include_once ROOT.'modules/news/admin/categories.php'; break;
	case 'categories': include_once ROOT.'modules/news/admin/categories.php'; break;
	case 'add':
	case 'edit': include_once ROOT.'modules/news/admin/manage_entry.php'; break;
	default: include_once ROOT.'modules/news/admin/entries.php';
}