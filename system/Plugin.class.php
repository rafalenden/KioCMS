<?php

// KioCMS - Kiofol Content Managment System
// includes/Pager.class.php

class Plugin
{
	// $tpl->panel = Plugin::panel('form-message', $guestbook['parsers']);
	// $tpl->panel = $plug->panel('form-message', $guestbook['parsers']);
	// aggregate_methods, call_user_method, call_user_method_array, classkit_method_add, method_exists, call_user_func

	public static $loaded_plugins = array();

	function __construct()
	{

	}

	function __toString()
	{
		return '';
	}

	public static function exists($plugin)
	{
		return is_file(ROOT.'plugins/'.$plugin.'/'.$plugin.'.plugin.php');
	}

	public static function load($plugin_name)
	{
		
	}

	public function isLoaded($plugin)
	{
		return is_file(ROOT.'plugins/'.$plugin.'/index.php');
	}

	function __call($plugin, $arg)
	{
//		if (!$kio->plugins_off[$plugin] && is_file(ROOT.'plugins/'.$plugin.'/index.php'))
		if (is_file(ROOT.'plugins/'.$plugin.'/index.php'))
		{
			include_once ROOT.'plugins/'.$plugin.'/index.php';
			if (!$this->$plugin)
			{
				$this->{$plugin} = new $plugin($arg);
			}
			self::$loaded_plugins[] = $plugin;
			return $this->{$plugin};
		}
		else
		{
			return $this;
		}


		/* if (is_file('plugins/'.$plugin.'/index.php'))
		  {
		  include_once 'plugins/'.$plugin.'/index.php';


		  return call_user_func_array($plugin, $arguments);
		  } */

		// $this->{$plugin} =  function ($text) {return 'lol';};
		// $this->{$plugin} =  eval(file_get_contents('plugins/'.$plugin.'/index.php'));
		;

		//if (!method_exists($this, $plugin))
	}
}