<?php
 // KioCMS - Kiofol Content Managment System
// admin/ajax.php

define('KioCMS', true);
define('AJAX', true);
include_once '../../system.php';
include_once root_dir.'admin/functions.php';
$ajax = basename(dirname($_SERVER['PHP_SELF']));
$lang_admin = get_lang('admin/main');
$lang = get_lang('admin/'.$ajax.'/index');
$module = get_config($ajax);