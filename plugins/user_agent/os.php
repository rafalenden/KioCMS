<?php
 // KioCMS - Kiofol Content Managment System
// includes/os.php

function os($agent)
{
	if (stripos($agent, 'Win') !== false)
	{
		$windows = array(
			'NT 6.0' => 'Vista',
			'NT 5.2' => 'Server 2003',
			'NT 5.1' => 'XP',
			'NT 5.0' => '2000',
			'NT' => 'NT',
			'Me' => 'Me',
			'98' => '98',
			'95' => '95',);
		foreach ($windows as $code => $name)
			if (stripos($agent, 'Windows '.$code) !== false)
				return array(
					'name' => 'Windows '.$name,
					'icon' => $code == 'NT 6.0' ? 'windows_vista' : ($code == 'NT 5.2' || $code == 'NT 5.1' ? 'windows_new' : 'windows_old'));
	}
	elseif (stripos($agent, 'Linux') !== false)
	{
		$names = array('CentOS', 'Debian', 'Fedora', 'Gentoo', 'KateOS', 'Knoppix', 'Kubuntu', 'Mandriva', 'Mandrake', 'PLD', 'RedHat', 'Slackware', 'SuSE', 'SUSE', 'Ubuntu', 'Xubuntu');
		foreach ($names as $name)
			if (stripos($agent, $name) !== false)
				return array(
					'name' => $name,
					'icon' => strtolower($name));
		return array('name' => 'Linux', 'icon' => 'linux');
	}
	else
	{
		$names = array('AmigaOS', 'BeOS', 'Darwin', 'FreeBSD', 'IRIX', 'J2ME', 'Mac OS', 'NetBSD', 'Nintendo Wii', 'OpenBSD', 'OS/2', 'PalmOS', 'PlayStation', 'QNX', 'ReactOS', 'Symbian', 'SunOS');
		foreach ($names as $name)
			if (stripos($agent, $name) !== false)
				return array(
					'name' => $name,
					'icon' => strtolower(str_replace(array(' ', '/'), array('_', ''), $name)));
	}
}