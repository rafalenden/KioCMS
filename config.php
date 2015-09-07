<?php
 // KioCMS - Kiofol Content Managment System
// config.php

define('DB_HOST', 'localhost'); // Database host/file
define('DB_PORT', 3307); // Database port
define('DB_NAME', 'EXAMPLE_DB_NAME'); // Database name
define('DB_USER', 'EXAMPLE_USERNAME'); // Database user
define('DB_PASS', 'EXAMPLE_USER_PASS'); // Database password
define('DB_PREFIX', 'kio_'); // Tables prefix
define('DB_TYPE', 'mysql'); // Type of database
define('DB_PDO', true); // Connecting to DB via PDO drivers
//'ROOT' , '' // Root/Base directory,
define('LOCAL', '/kiocms/'); // Local directory
define('SITE_URL', 'http://localhost/kiocms/'); // Website address
define('COOKIE' , 'KioCMS-2325'); // Cookie name prefix
define('CACHE_DIR', 'cache_MY_SECRET_HASH_CODE/'); // Cache directory
define('LOGS', false); // Save logs
define('ERRORS', E_ALL ^ E_NOTICE); // Errors reporting
//'LC'   , 'pl_PL', // Locale for setlocale() function
define('INSTALLED', false); // KioCMS is installed?
