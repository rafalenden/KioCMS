<?php

class Module extends Form
{
	public $name;
	public $codename;
	public $subcodename;
	public $content;
	public $cfg = array();
	public $blocks = array(
		'left' => array('user_panel', 'partners', 'news_categories', 'shoutbox'),
		'right' => array('poll', 'new_users', 'searcher', 'newsletter', 'przykladowy', 'calendar'));

	function __construct()
	{
		
	}

	/// final?
	public static final function load222($codename)
	{
		if (!is_file(ROOT.'modules/'.$codename.'/'.$codename.'.module.php'))
		{
			throw new Exception(t('Module dosn&apos;t exists'));
		}

		require_once ROOT.'modules/'.$codename.'/'.$codename.'.module.php';

		$module = new $codename();

		$module->codename = $codename;
		$module->cfg = $GLOBALS['cfg']->{$codename};

		define('MODULE', $codename);

		return $module;
	}

	public function init()
	{
		if (!is_file(ROOT.'modules/'.$this->codename.'/'.$this->codename.'.module.php'))
		{
			throw new Exception(t('Module dosn&apos;t exists'));
		}

		$this->cfg = $GLOBALS['cfg']->{$this->codename};
	}

	public static function exists($codename)
	{
		return is_file(ROOT.'modules/'.$codename.'/'.$codename.'.module.php');
	}

	public function getContent()
	{
		return $this->content;
	}

	public function hasBlocks($sector = false)
	{

	}

	public function getBlocks($sector = false)
	{
		
	}
}
?>
