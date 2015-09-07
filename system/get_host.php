<?php
 // KioCMS - Kiofol Content Managment System
// includes/get_host.php

$ip = $_POST['ip'];
if ($ip) echo gethostbyaddr($ip);
exit;