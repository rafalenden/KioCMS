<?php
 // KioCMS - Kiofol Content Managment System
// includes/mysql.php

defined('KioCMS') || exit;

$cfg->system['queries'] = 0;



if (DB_PDO)
{
	try
	{
		define('OPD_DIR', ROOT.'system/OPD/');
		require(OPD_DIR.'opd.class.php');

		$sql = new OPD('mysql:host='.DB_HOST.';dbname='.DB_NAME.(DB_PORT ? ';port='.DB_PORT : null), DB_USER, DB_PASS);
		$sql->setCacheDirectory(ROOT.CACHE_DIR);
//		$sql->debugConsole = true;

		//$sql = new MyPDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.(DB_PORT ? ';port='.DB_PORT : null), DB_USER, DB_PASS);
		$sql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql->exec('SET NAMES utf8');
	}
	catch (Exception $e)
	{
		exit('<div style="text-align: center;"><br /><h3>'.t('Error while connecting to SQL database!').'</h3><code>'.$e->getMessage().'</code></div>');

		//exit('<h3>Error while connecting to SQL database!</h3><code>'.$e->getMessage().'</code><p>For more information check log file.</p>');
	}
}

function query($content)
{
	global $sql;
	return $sql->query($content);
}

/*
mysql_connect(DB_HOST, DB_USER, DB_PASS) || die(mysql_error());
mysql_select_db(DB_NAME) || die(mysql_error());

function sql_query($query)
{
	global $cfg;
	if (is_array($query))
	{
		$cfg->system['queries'] += count($query);
		return array_map('mysql_query', $query);
	}
	else
	{
		$cfg->system['queries']++;
		return mysql_query($query);
	}
}
function sql_fetch_array($query)
{
	return mysql_fetch_array($query);
}
function sql_fetch_assoc($query)
{
	return mysql_fetch_assoc($query);
}
function sql_fetch_row($query)
{
	return mysql_fetch_row($query);
}
function sql_insert_id()
{
	return mysql_insert_id();
}
function sql_num_rows($query)
{
	return mysql_num_rows($query);
}
function sql_result($query, $row = 0, $field = false)
{
	return mysql_result($query, $row, $field);
}
function sql_error()
{
	return mysql_error();
}
function sql_close()
{
	return mysql_close();
}
sql_query('SET NAMES utf8');
*/