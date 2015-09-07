<?php
 // KioCMS - Kiofol Content Managment System
// plugins/panel/preview.php

if ($test = $_REQUEST['text'])
{
	define('KioCMS', true);
	define('AJAX', true);
	define('Preview', true);
	require '../../init.php';
	echo parse(filter($test, 200 + 1), 12345);
}