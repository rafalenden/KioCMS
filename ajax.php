<?php
    /// KioCMS 1.0.0 - Kiofol Content Managment System
   /// Copyright © 2008 by Kiofol Software
  /// License: GNU General Public License
 /// Author: Rafał "Endzio" Enden
/// index.php

define('KioCMS', true);
define('AJAX', true);
//define('Admin', true);
//define('INDEX', true);

ob_start('ob_gzhandler');
require_once './system.php';

header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: store, no-cache, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);

header('Pragma: public');

$action = ROOT.$_POST['action'];


