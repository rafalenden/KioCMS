<?php
 // KioCMS - Kiofol Content Managment System
// includes/sqlite.php

defined('KioCMS') || exit;

$db = sqlite_open(db_host, db_user, db_pass) || die(sqlite_error_string(sqlite_last_error()));

function sql_query($query)
{
	$GLOBALS['system']['queries']++;
	$query = str_replace('`', '', $query);
	return sqlite_query(is_array($query) ? implode(';'."\n", $query) : $query, SQLITE_ASSOC);
}
function sql_fetch_array($query)
{
	return sqlite_fetch_array($query);
}
function sql_num_rows($query)
{
	return sqlite_num_rows($query);
}
function sql_error()
{
	return sqlite_error_string(sqlite_last_error());
}
function sql_close()
{
	return sqlite_close($db);
}
sql_query('SET NAMES utf8');
?>