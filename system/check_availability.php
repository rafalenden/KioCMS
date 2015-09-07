<?php
 // KioCMS - Kiofol Content Managment System
// modules/registration/check.php

define('KioCMS', true);
define('AJAX', true);

require '../../init.php';

echo $_POST['logname'] || $_POST['nickname']
	 ? is_registered(filter($_POST['logname'].$_POST['nickname'], 100), $_POST['logname'] ? 'logname' : 'nickname')
	 	? t('Name is <strong>not available</strong>')
		: t('Name is <strong>available</strong>')
	 : t('Field can&apos;t be empty.');
exit;