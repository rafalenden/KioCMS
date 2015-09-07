<?php
 // KioCMS - Kiofol Content Managment System
// includes/blocks.php

class Output
{
	function __construct()
	{
		global $cfg;
		$this->path[] = $cfg->system['title'];
		$this->description = $cfg->system['description'];
		$this->keywords = $cfg->system['keywords'];
		$this->header = $cfg->system['header'];
		$this->show_blocks = true;
		$this->blocks = $cfg->system['blocks'];
		$this->columns = $cfg->system['columns'];
	}
	function add_block($params)
	{
		$this->blocks[$params['side']] += $params;
	}
	function remove_block($codename)
	{
		
	}
	function get_blocks()
	{
		if ($this->columns > 1)
		{
			$get_blocks = sql_query('
				SELECT * FROM '.DB_PREFIX.'blocks
				WHERE '.($this->blocks ? 'id NOT IN('.$this->blocks.') AND ' : '').($this->columns == 2
					? 'side = "L" AND type != 0'
					: 'type != 0').'
				ORDER BY position');
			while ($block = sql_fetch_assoc($get_blocks))
			{
				ob_start();
				if ($block['content'])
					echo $block['content'];
				else
				{
					$GLOBALS[$block['codename']] = $block;
					include ROOT.'blocks/'.$block['codename'].'/index.php';
				}
				$this->{($block['side'] == 'R' ? 'right' : 'left')}[$block['codename']] = array(
					'name' => $GLOBALS[$block['codename']]['name'] ? $GLOBALS[$block['codename']]['name'] : $block['name'],
					'codename' => $block['codename'],
					'subcodename' => $GLOBALS[$block['codename']]['subcodename'],
					'header' => $block['header'],
					'content' => ob_get_contents());
				ob_end_clean();
			}
		}
		$this->blocks = ''; // Aby nie mieszało przy dodawaniu modułu, gdy ten sam moduł jest ostatnim blokiem po lewej stronie
	}
}