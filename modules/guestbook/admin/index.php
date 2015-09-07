<?php
 // KioCMS - Kiofol Content Managment System
// modules/guestbook/admin/index.php

$kio->path['admin/modules/guestbook'] = t('Guestbook');

// Tabs
$kio->tabs = tabs(array(
	// Name => URL
	t('Entries') => array('admin/modules/guestbook(/[0-9]+|/sort/.*)?$', 'admin/modules/guestbook'),
	t('Settings') => array('admin/modules/guestbook/settings$', 'admin/modules/guestbook/settings')));

include ROOT.'modules/guestbook/admin/'.(u3 == 'settings' ? 'settings' : 'entries').'.php';