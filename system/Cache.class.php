<?php
 // KioCMS - Kiofol Content Managment System
// includes/blocks.php

class Cache
{
	const PHP = 1;
	//const SERIALIZE = 2;
	function __construct()
	{
		
	}

	public static function get($file, $type = false)
	{
		return;

		// Content as php array
		if ($type == self::PHP) return @include ROOT.CACHE_DIR.$file;

		// Serializet content
		return ($content = @file_get_contents(ROOT.CACHE_DIR.$file)) ? unserialize($content) : false;
		
	}

        /**
         * awdwadwada
         * @param <type> $file
         * @param <type> $content
         * @param <type> $type
         * @return int asafsaf
         */
	public static function put($file, $content, $type = false)
	{
		return;

		if (!$content) return;

		if ($type == self::PHP) return fwrite(fopen(ROOT.CACHE_DIR.$file, 'w'), '<?php return '.var_export($content, true).';');

		return fwrite(fopen(ROOT.CACHE_DIR.$file, 'w'), serialize($content));
	}

	public static function set($file, $content, $type = false)
	{
		return;

		if (!$content) return;

		if ($type == self::PHP) return fwrite(fopen(ROOT.CACHE_DIR.$file, 'w'), '<?php return '.var_export($content, true).';');

		return fwrite(fopen(ROOT.CACHE_DIR.$file, 'w'), serialize($content));
	}

	public static function cache_put2($file, $content)
	{
		$dir = dirname($file);
		if (!is_dir(ROOT.CACHE_DIR.$dir))
			mkdir(ROOT.CACHE_DIR.$dir, 0700, true);
		return fwrite(fopen(ROOT.CACHE_DIR.$file, 'w'), serialize($content));
	}

	public static function cache_clear2($file)
	{
		return unlink(ROOT.CACHE_DIR.'/'.$file);
	}

	public static function clear($match)
	{
		foreach (glob(ROOT.CACHE_DIR.$match) as $file)
			unlink($file);
	}
}