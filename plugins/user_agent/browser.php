<?php
 // KioCMS - Kiofol Content Managment System
// includes/browser.php

function browser($agent)
{
	$browsers = array(
		// Most popular first (performance)
		'Firefox' => 'Firefox/([\d.]+)',
		'IE' => '\(compatible; MSIE[ /]([\d.]+)',
		'Opera' => 'Opera[ /]([\d.]+)',
		'Chrome' => 'chrome/([\d.]+)',
		'Safari' => 'safari/([\d.]+)',
		'Opera Mini' => 'Opera Mini[ /]([\d.]+)',

		// Others
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
		//'Maxthon' => 'Maxthon',
		//'MyIE2' => 'MyIE2',
		'Minefield' => 'Minefield/([\d.]+)',
		//'Minimo' => 'minimo/([\d.]+)',
		//'Mosaic' => 'Mosaic[ /]([\w.]+)',
		'Netscape' => 'netscape\d?/([\d.]+)',
		//'NetSurf' => 'NetSurf/([\d.]+)',
		'OmniWeb' => 'omniweb/[ a-z]?([\d.]+)',
		//'Oregano' => 'Oregano[\d]?[ /](\d.+)',
		'OpenWave' => 'UP\.(?:Browser)?[ /]([\w.]+)',
		'Phoenix' => 'Phoenix/([\d.]+)',
		'QNX Voyager' => 'QNX Voyager[ /]([\d.]+)',
		'NetFront' => 'NetFront[ /]([\d.]+)',
		'PSP' => 'psp.*playstation.*portable\D*([\w\.]+)',
		'Shiira' => 'Shiira/([\d.]+)',
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
		if (stripos($agent, $name) !== false)
		{
			preg_match('#'.$match.'#i', $agent, $matches);
			return array(
				'name' => $name,
				'version' => $matches[1],
				'icon' => strtolower(strpos($name, ' ') !== false ? str_replace(' ', '_', $name) : $name));
		}
	}
	return array('icon' => 'unknown');
}