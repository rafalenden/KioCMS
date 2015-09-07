<?php
/// KioCMS 1.0.0 - Kiofol Content Managment System
/// Copyright © 2008 by Kiofol Software
/// License: GNU General Public License
/// Author: Rafał "Endzio" Enden
/// index.php

define('KioCMS', true);
//define('Admin', true);
//define('INDEX', true);

ob_start('ob_gzhandler');

header('Content-Type: text/html; charset=UTF-8');
header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: public');

require_once './init.php';


ob_start();

$kio->left = $kio->right = true;

// $module = $block = new stdClass();
// Check for contents to display (int) or to include (string)
$s = ctype_digit(u0) ? (int)u0 : (int)Kio::getConfig('front_page');

// Static page
if ($s)
{
	try
	{
		if (!Module::exists('subpage'))
		{
			throw new Exception(t('Module dosn&apos;t exists'));
		}

		require_once ROOT.'modules/subpage/subpage.module.php';

		$module = new Subpage($s);
		echo $module->getContent();

		define('MODULE', 'subpage-'.$s);
	}
	catch (Exception $e)
	{
		define('MODULE', 'error');
		echo $e->getMessage();
	}
}
else if (u0)
{
	switch (u0)
	{
		// Administration
		case 'admin':
			include_once ROOT.'admin/index.php';
			if (!$module->name)
			{
				$module->name = end($kio->title);
			}
			define('MODULE', 'admin');
			break;
		// Logout
		case 'logout':
			define('MODULE', 'logout');
			User::logout();
			break;
		// Redirect to external page
		case 'redirect':
			define('MODULE', 'redirect');
			ctype_digit(u1) && ($row = sql_fetch_assoc(sql_query('SELECT url FROM '.DB_PREFIX.'redirect WHERE id = '.u1))) ? sql_query('UPDATE '.DB_PREFIX.'redirect SET clicks = clicks + 1 WHERE id = '.u1).
					redirect($row['url']) : redirect(LOCAL);
			break;
		// Load block as module
		case 'blocks':
			try
			{
				// Load block
				if (!Block::exists(u1))
				{
					throw new Exception(t('Block dosn&apos;t exists'));
				}

				require_once ROOT.'blocks/'.u1.'/'.u1.'.block.php';
				$codename = u1;
				$module = new $codename(Block::getBlockData(u1), true);
				
				Kio::addTitle($module->name);
				Kio::addBreadcrumb($module->name, 'blocks/'.u1);
				echo $module->getContent();

				define('MODULE', u1);
			}
			// Block doesn't exist
			catch (Exception $e)
			{
				define('MODULE', 'error_404-module');
				echo $e->getMessage().'<br/><br/>In file <strong>'.$e->getFile().'</strong> ar line '.$e->getLine().'';

//				define('MODULE', 'error_404-block');
//				not_found(sprintf('Blok <strong>%s</strong> nie istnieje.', u1), array(
//					'Blok obsługujący nie jest zainstalowany',
//					$lang_system['FIRST_404_COUSE'],
//					$lang_system['SECOND_404_COUSE']));
			}
			break;
		// Module
		default:
			try
			{
				if (!Module::exists(u0))
				{
					throw new Exception(t('Module dosn&apos;t exists'));
				}

				require_once ROOT.'modules/'.u0.'/'.u0.'.module.php';
				$codename = u0;
				$module = new $codename();
				echo $module->getContent();

				define('MODULE', u0);
			}
			catch (Exception $e)
			{
				define('MODULE', 'error_404-module');
				echo $e->getMessage().'<br/><br/>In file <strong>'.$e->getFile().'</strong> ar line '.$e->getLine().'';

//				not_found(t('Podstrona <strong>%page</strong> nie istnieje.', array('%page' => u0)), array(
//					'Moduł obsługujący nie jest zainstalowany.',
//					'FIRST_404_COUSE',
//					'SECOND_404_COUSE'));
			}
	}
}
// Load front page module
else
{
	try
	{
		$codename = Kio::getConfig('front_page');

		if (!Module::exists($codename))
		{
			throw new Exception(t('Module dosn&apos;t exists'));
		}

		require_once ROOT.'modules/'.$codename.'/'.$codename.'.module.php';
		$module = new $codename();
		echo $module->getContent();

		define('MODULE', $codename);
	}
	catch (Exception $e)
	{
		define('MODULE', 'error_404-module');

		echo $e->getMessage().'<br/>'.$e->getFile().':'.$e->getLine();
	}
	//if (!$module->name) $module->name = end($kio->path);
}

if (!$module->name)
{
	$module->name = Kio::getModuleName();
}

$module->content = ob_get_contents();
ob_end_clean();

//$kio->blocks_disabled = ${MODULE}['blocks'] ? ${MODULE}['blocks'] : $cfg->system['blocks'];

require_once ROOT.'themes/'.THEME.'/'.THEME.'.theme.php';

unset($config, $system, $kio, $user, $form, $lang, $lang_system, $module, $stats, $permit, $groups, $sql);