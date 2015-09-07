<?php
    /// KioCMS 1.0.0 - Kiofol Content Managment System
   /// Copyright © 2008 by Kiofol Software
  /// License: GNU General Public License
 /// Author: Rafał "Endzio" Enden
/// admin/news/index.php

defined('KioCMS') || exit;
$lang = include_lang('admin/subpages');
$module = get_config('subpages');

$title[] = $lang['NAME'];

// Tabs
$module['tabs'] = tabs(array(
	// Name => Target / array(Target, AJAX Target, Hash)
	'Przegląd' => array('admin/subpages', 'admin/subpages/overview.php', 'overview'),
	'Dodaj wpis' => array('admin/subpages/add', 'admin/subpages/manage.php', 'add'),
	'Ustawienia' => array('admin/subpages/settings', 'admin/subpages/settings.php', 'settings')),
	// Search in
	u2);

switch (u2)
{
	case 'settings': include_once root_dir.'admin/subpages/settings.php'; break;
	case 'add':
	case 'edit': include_once root_dir.'admin/subpages/manage.php'; break;
	default: include_once root_dir.'admin/subpages/overview.php';
}
?>