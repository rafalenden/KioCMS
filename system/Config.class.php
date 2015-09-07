<?php
/**
 * Description of Block
 *
 */
class Config
{
	public $name = '';
	public $codename = '';
	public $subcodename = '';
	public $sectorId = 0;
	public $displayOrder = 0;
	public $headerVisible = true;
	public $content = '';

	private static $vars = array();

    function  __callStatic($attributes = array())
	{
		$this->name = $attributes['name'];
		$this->codename = $attributes['codename'];
		$this->subcodename = $attributes['subcodename'];
		$this->sectorId = $attributes['sectorId'];
		$this->displayOrder = $attributes['displayOrder'];
		$this->headerVisible = $attributes['headerVisible'] = true; //////
		$this->content = $attributes['content'];
	}

	public static function get($config_name, $section = 'system')
	{
		return self::$vars[$section][$config_name];
	}

	public static function init()
	{
		global $sql;

		if (!(self::$vars = $sql->getCache('config')))
		{
			$query = $sql->query('SELECT holder, name, content FROM ' . DB_PREFIX . 'config');

			while ($row = $query->fetch())
			{
				self::$vars[$row['holder']][$row['name']] = $row['content'];
			}

			$sql->putCacheContent('config', self::$vars);
		}
	}

	static function isActive()
	{

	}
	
	public static function getClassNameFromCodename($codename)
	{
		 $codename[0] = strtoupper($codename[0]);
	}

	/**
	 * Show block output
	 */

	public function getContent()
	{
		return $this->content;
	}
	
	public function toArray()
	{
		return array(
			'name' => $this->name,
			'codename' => $this->codename,
			'subcodename' => $this->subcodename,
			'sectorId' => $this->sectorId,
			'displayOrder' => $this->displayOrder,
			'showHeader' => $this->showHeader,
			'content' => $this->content
		);
	}

}

?>
