<?php
 // KioCMS - Kiofol Content Managment System
// system/functions2.php

defined('KioCMS') || exit;

// mb_strlen
if (!$cfg->system['functions']['mb_strlen'])
{
	function mb_strlen($str)
	{
		return strlen($str);
	}
}

// date_default_timezone_set
if (!$cfg->system['functions']['date_default_timezone_set'])
{
	function date_default_timezone_set($str)
	{
		if ($cfg->system['functions']['putenv'])
			return putenv('TZ='.$str);
	}
}