<?php
 // KioCMS - Kiofol Content Managment System
// includes/MyPDO.class.php

class MyPDO extends PDO
{
	var $queryCount = 0;
	var $queryHistory = array();

	public function query($sql, $cache = false)
	{
		try
		{
			if ($cache)
			{
				$cache = cache_get($cache);
				if ($cache) return $cache;
			}
			$this->queryCount++;
			$this->queryHistory[] = $sql;
			return PDO::query($sql);
		}
		catch (Exception $e)
		{
			exit('<h3>Error while geting SQL query results!</h3><code>Code: '.end($this->errorInfo()).'</code><p><em>For more information check log file.</em></p>');
			// logger(array($e->getMessage, 'Treść zapytania: '.$sql), 'SQL');
		}
		//throw new Exception();
		//return array();
	}

	public function cache($file)
	{
		$cache = cache_get($cache_file);
			if ($cache)
				return $cache;
		return $this;
	}

	public function queryCache($sql, $cache_file)
	{
		try
		{
			$cache = cache_get($cache_file);
			if ($cache)
				return $cache;

			$this->queryCount++;
			$this->queryHistory[] = $sql;
			$content = PDO::query($sql)->fetchAll();
			if ($content) cache_put($cache_file, $content);
			return $content;
		}
		catch (Exception $e)
		{
			exit('<h3>Error while geting SQL query results!</h3><code>Code: '.end($this->errorInfo()).'</code><p><em>For more information check log file.</em></p>');
			// logger(array($e->getMessage, 'Treść zapytania: '.$sql), 'SQL');
		}
		//throw new Exception();
		//return array();
	}

	public function exec($sql, $cache_file = false)
	{
		$this->queryCount++;
		try
		{
			$this->queryHistory[] = $sql;
			if ($cache_file)
				cache_clear($cache_file);
			return PDO::exec($sql);
		}
		catch (Exception $e) {exit('<h3>Error while executing SQL query!</h3><code>Code: '.end($this->errorInfo()).'</code><p><em>For more information check log file.</em></p>');}
	}
}