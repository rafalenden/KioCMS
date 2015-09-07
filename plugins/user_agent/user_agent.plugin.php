<?php
 // KioCMS - Kiofol Content Managment System
// plugins/user_agent/index.php

class User_Agent extends Plugin
{
	public static function getOS($agent)
	{
		if (strpos($agent, 'Win'))
		{
			$windows = array(
				'NT 6.1' => '7',
				'NT 6.0' => 'Vista',
				'NT 5.2' => 'Server 2003',
				'NT 5.1' => 'XP',
				'NT 5.0' => '2000',
				'NT' => 'NT',
				'Me' => 'Me',
				'98' => '98',
				'95' => '95',);

			foreach ($windows as $code => $name)
			{
				if (stripos($agent, 'Windows '.$code))
				{
					return array(
						'name' => 'Windows '.$name,
						'icon' => $code == 'NT 6.0' ? 'windows_vista' : (substr($code, 3) > 5.0 ? 'windows_new' : 'windows_old'));
				}
			}

			return array('name' => 'Windows', 'icon' => 'windows_new');
		}
		elseif (strpos($agent, 'Linux'))
		{
			$oses = array('CentOS', 'Debian', 'Fedora', 'Gentoo', 'KateOS', 'Knoppix', 'Kubuntu', 'Mandriva', 'Mandrake', 'PLD', 'RedHat', 'Slackware', 'SuSE', 'Ubuntu', 'Xubuntu');

			foreach ($oses as $os)
				if (stripos($agent, $os)) return array('name' => $os, 'icon' => strtolower($os));

			return array('name' => 'Linux', 'icon' => 'linux');
		}
		else
		{
			$oses = array('AmigaOS', 'BeOS', 'Darwin', 'FreeBSD', 'IRIX', 'J2ME', 'Mac OS', 'NetBSD', 'Nintendo Wii', 'OpenBSD', 'OS/2', 'PalmOS', 'PlayStation', 'QNX', 'ReactOS', 'Symbian', 'SunOS');

			foreach ($oses as $os)
				if (stripos($agent, $os))
					return array('name' => $os, 'icon' => strtolower(str_replace(array(' ', '/'), array('_', ''), $os)));
		}
	}

	public static function getBrowser($agent)
	{
		$browsers = array(
			'Firefox' => 'Firefox/([\d.]+)',
			'IE' => '\(compatible; MSIE[ /]([\d.]+)',
			'Opera' => 'Opera[ /]([\d.]+)',
			'Chrome' => 'chrome/([\d.]+)',

			//'ABrowse'  => 'abrowse[ /\-]([\d.]+)',
			//'Amaya' => 'amaya/([\d.]+)'
			'AmigaVoyager' => 'AmigaVoyager/([\d.]+)',
			//'Arachne' => 'Arachne/([\d.]+)',
			'AWeb' => 'Aweb[/ ]([\d.]+)',
			//'Beonex' => 'beonex/([\d.]+)',
			'BonEcho' => 'BonEcho/([\d.]+)',
			//'BrowseX' => 'BrowseX.*\(([\d.]+)',
			'Camino' => 'camino/([\d.]+)',
			//'Curl' => 'curl[ /]([\d.]+)',
			'DeskBrowse' => 'deskbrowse/([\d.]+)',
			'Dillo' => 'dillo/([\d.]+)',
			//'ELinks' => 'ELinks[ /][\(]*([\d.]+)',
			'Epiphany' => 'Epiphany/([\d.]+)',
			'Firebird' => 'Firebird/([\d.]+)',
			'Flock' => 'Flock/([\d.]+)',
			'Galeon' => 'galeon/([\d.]+)',
			'GranParadiso' => 'GranParadiso/([\d.]+)',
			'IBrowse' => 'ibrowse[ /]([\d.]+)',
			'ICab' => 'icab[/ ]([\d.]+)',
			'Iceape' => 'Iceape/([\d.]+)',
			'Iceweasel' => 'Iceweasel/([\d.]+)',
			'K-Ninja' => 'K-Ninja[ /]([\d.]+)',
			'K-Meleon' => 'K-Meleon[ /]([\d.]+)',
			'Kazehakase' => 'Kazehakase/([\w.]+)',
			'Konqueror' => 'Konqueror/([\d.]+)',
			'Links' => 'Links[ /]\(([\d.]+)',
			'Lynx' => 'lynx/([\w.]+)',
			'Maxthon' => 'Maxthon',
			//'MyIE2' => 'MyIE2',
			'Minefield' => 'Minefield/([\d.]+)',
			//'Minimo' => 'minimo/([\d.]+)',
			//'Mosaic' => 'Mosaic[ /]([\w.]+)',
			'Netscape' => 'netscape\d?/([\d.]+)',
			//'NetSurf' => 'NetSurf/([\d.]+)',
			'OmniWeb' => 'omniweb/[ a-z]?([\d.]+)',
			//'Oregano' => 'Oregano[\d]?[ /](\d.+)',
			'OpenWave' => 'UP\.(?:Browser)?[ /]([\w.]+)',
			'Opera Mini' => 'Opera Mini[ /]([\d.]+)',
			'Phoenix' => 'Phoenix/([\d.]+)',
			'QNX Voyager' => 'QNX Voyager[ /]([\d.]+)',
			'NetFront' => 'NetFront[ /]([\d.]+)',
			'PSP' => 'psp.*playstation.*portable\D*([\w\.]+)',
			'Shiira' => 'Shiira/([\d.]+)',
			'Safari' => 'safari/([\d.]+)',
			'SeaMonkey' => 'SeaMonkey/([\w.]+)',
			//'Songbird' => 'songbird/([\d.]+)',
			'SunriseBrowser' => 'SunriseBrowser/([\d.]+)',
			//'Swiftfox' => 'Firefox/([\d.]+)',
			'Thunderbird' => 'Thunderbird/([\d.]+)',
			//'W3M' => 'w3m/([\d.]+)',
			'Wget' => 'Wget[ /]([\d.]+)',
			'Mozilla' => 'rv:([\w.]+)');

		foreach ($browsers as $name => $match)
		{
			if (stripos($agent, $name))
			{
				preg_match('#'.$match.'#i', $agent, $matches);
				return array(
					'name' => $name.' '.$matches[1],
					'icon' => strtolower(strpos($name, ' ') !== false ? str_replace(' ', '_', $name) : $name));
			}
		}
	}
}