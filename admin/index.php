<?php
    /// KioCMS 1.0.0 - Kiofol Content Managment System
   /// Copyright © 2008 by Kiofol Software
  /// License: GNU General Public License
 /// Author: Rafał "Endzio" Enden
/// admin/index.php

define('ADMIN', true);

$lang_admin = get_lang('lang/admin.*.ini');
$kio->path['admin'] = $lang_admin['NAME'];
require_once ROOT.'admin/functions.php';
////////
$query = $sql->query('SELECT id FROM '.DB_PREFIX.'blocks WHERE type != 0');
while ($row = $query->fetch())
	$blocks[] = $row['id'];
//////////

$kio->right = false;

if (u1)
{
	switch (u1)
	{
		case 'modules':
			$kio->path['admin/modules'] = 'Moduły';
			if (u2)
			{
				require_once ROOT.'modules/'.u2.'/admin/index.php';
//				if (file_exists(ROOT.'modules/'.u2.'/admin/index.php'))
//				{
//					$permit['admin-'.u2].$permit['admin']
//						? require_once ROOT.'modules/'.u2.'/admin/index.php'
//						: no_access(sprintf('Nie masz dostępu do administracji modułem <strong>%s</strong>.', u2));
//				}
//				else
//				{
//					not_found(sprintf($lang_system['PAGE_NOT_FOUND'], u2));
//				}
			}
			else
				require_once ROOT.'admin/modules.php';
			break;
		case 'blocks':
			$kio->path['admin/blocks'] = 'Bloki';
			file_exists(ROOT.'blocks/'.u2.'/index.php')
				? require_once ROOT.'blocks/'.u2.'/index.php'
				: not_found(sprintf($lang_system['PAGE_NOT_FOUND'], u2), array($lang_system['NO_FILE'], $lang_system['NO_CONTENT'], $lang_system['BAD_ADDRESS']));
			break;
		case 'includes':
			file_exists(ROOT.'system/'.u2.'/index.php')
				? ($permit['admin-system-'.u2].$permit['admin']
					? require_once ROOT.'system/'.u2.'/index.php'
					: no_access(sprintf('Dostęp do administracji rozszerzeniem <strong>%s</strong> jest zabroniony.', u2)))
				: not_found(sprintf($lang_system['PAGE_NOT_FOUND'], u2), array($lang_system['NO_FILE'], $lang_system['NO_CONTENT'], $lang_system['BAD_ADDRESS']));
			break;
		default:
			file_exists(ROOT.'admin/'.u1.'/index.php')
				? require_once ROOT.'admin/'.u1.'/index.php'
				: not_found(sprintf($lang_system['PAGE_NOT_FOUND'], u2), array($lang_system['NO_FILE'], $lang_system['NO_CONTENT'], $lang_system['BAD_ADDRESS']));
	}
}
else
	require_once ROOT.'admin/default.php';

$admin['columns'] = 2;

block(
	// Name, Codename, Side, At the top?, Header
	'Wskazówka', 'tips', 'L', 0, 1,
	// Content
	'Co robić, żeby było dobrze?');