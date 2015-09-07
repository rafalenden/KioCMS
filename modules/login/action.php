<?php
// KioCMS - Kiofol Content Managment System
// modules/login/action.php

$note->restore();

$form = array(
	'logname' => $_POST['logname-session'] ? filter($_POST['logname-session'], 100) : '',
	'pass' => $_POST['pass-session'] ? filter($_POST['pass-session'], 100) : '');

$err->empty_logname('Logname field is required.', !$form['logname'])
	->logname_not_exists(t('The logname you used isn&apos;t registered.'), $form['logname'] && !is_registered($form['logname'], 'logname'))
	->pass_empty(t('Password field is required.'), !$form['pass'])
	->pass_invalid(t('Password is invalid.'), $form['pass'] && md5($form['pass']) != $GLOBALS['session']['pass']);

$err->isErrors() ? $note->error($err) : redirect(REFERER);