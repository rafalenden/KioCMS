<?php
    /// KioCMS 1.0.0 - Kiofol Content Managment System
   /// Copyright © 2008 by Kiofol Software
  /// License: GNU General Public License
 /// Author: Rafał "Endzio" Enden
/// admin/comments/index.php

defined('KioCMS') || exit;
$lang = include_lang('includes/comments');
$module = get_config('comments');

$title[] = $lang['COMMENTS'];

// Tabs
$module['tabs'] = tabs(array(
	// Name => Target / array(Target, AJAX Target)
	'Przegląd wpisów' => array('admin/comments', 'admin/comments/entries.php', 'entries'),
	'Ustawienia' => array('admin/comments/settings', 'admin/comments/settings.php', 'settings')),
	// Search in
	u2);

// Entries
if (u2 == 'settings')
	include_once root_dir.'admin/comments/settings.php';

// Settings
else
	include_once root_dir.'admin/comments/entries.php';
?>