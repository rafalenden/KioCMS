<?php
 // KioCMS - Kiofol Content Managment System
// admin/system/index.php

defined('KioCMS') || exit;

$kio->path['admin/system'] = 'Konfiguracja';

// Tabs
$kio->tabs = tabs(array(
	// Name => URL
	'Ustawienia globalne' => array('admin/system$', 'admin/system'),
	'Ustawienia serwera' => array('admin/system/server', 'admin/system/server'),
	'Edycja strony głównej' => array('admin/system/front_page', 'admin/system/front_page')));

// Server settings
if (u2 == 'server')
	include_once ROOT.'admin/system/server.php';
// Start page
elseif (u2 == 'front_page')
	include_once ROOT.'admin/system/front_page.php';
// System settings
else
	include_once ROOT.'admin/system/settings.php';