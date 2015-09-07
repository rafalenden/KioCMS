<?php

/**
 * Description of Block
 *
 */
interface BlockInterface
{
	public function getContent();
	//public function isAllowed($resource);
}

class Block
{
	public $name = '';
	public $codename = '';
	public $subcodename = '';
	public $sector = '';
	public $displayOrder = 0;
	public $headerVisible = true;
	public $content = '';
	public static $sectors = array();

	private $asModule = false;

	public $blocks = array(
		'left' => array('user_panel', 'partners', 'news_categories', 'shoutbox'),
		'right' => array('poll', 'new_users', 'searcher', 'newsletter', 'przykladowy', 'calendar'));

	function __construct($attributes = array(), $as_module = false)
	{
		$this->name = t($attributes['name']);
		$this->codename = $attributes['codename'];
		$this->subcodename = !empty($attributes['subcodename']) ? $attributes['subcodename'] : '';
		$this->sector = $attributes['sector'];
		$this->displayOrder = $attributes['display_order'];
		$this->headerVisible = $attributes['header_visible'] = true; //////

		$this->content = $attributes['content'] ? $attributes['content'] : $this->getContent();

		if ($as_module)
		{
			$this->asModule = true;
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

	public static function loadBlocks($sectors = array())
	{
		global $sql;

		if (true || Kio::getConfig('show_blocks'))
		{
			$query = $sql->setCache('blocks')->query('
				SELECT * FROM '.DB_PREFIX.'blocks
				WHERE type != 0
				ORDER BY display_order');


			while ($row = $query->fetch(PDO::FETCH_ASSOC))
			{
				if (!$sectors || !empty($sectors[$row['sector']]))
				{
					if (in_array($row['codename'], (array)$sectors[$row['sector']]))
					{
						if ($row['content'])
						{
							$block = new Block($row);
							$block->name = $row['name'];
						}
						else
						{
							require_once ROOT.'blocks/'.$row['codename'].'/'.$row['codename'].'.block.php';

							$block = new $row['codename']($row);
						}

						self::$sectors[$row['sector']][$row['codename']] = $block;
					}
				}
			}
		}
	}

	public static function getBlockData($block_codename)
	{
		global $sql;
		
		if (!$data)
		{
			// getCacheContent
			return $sql->query('
				SELECT * FROM '.DB_PREFIX.'blocks
				WHERE codename = "'.$block_codename.'"')->fetch(PDO::FETCH_ASSOC);
		}
	}

	public static function sectorEmpty($sector_name)
	{
		return empty(self::$sectors[$sector_name]);
	}

	public static function getSector($sector_name)
	{
		return self::$sectors[$sector_name];
	}
	
	public function blockAsModule($codename)
	{
		return $this->asModule;
	}

	public function isLast()
	{
		$last = end(self::$sectors[$this->sector]);
		return $this == $last;
	}

	public static function exists($codename)
	{
		return is_file(ROOT.'blocks/'.$codename.'/'.$codename.'.block.php');
	}
}
