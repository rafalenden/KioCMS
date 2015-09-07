<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tree
 *
 * @author Lolek
 */
class Tree
{
	private $items;
	private $tree = array();
	
	function __construct($items = array())
	{
		$this->items = $items;
		$this->generate($this->items);
	}

	function build($items = array(), $parent = 0, $level = 0)
	{
		// Reset the flag each time the function is called
		$is_parent = false;

		// Building tree
		foreach ($items as $item)
		{
			if ($value['parent_id'] == $parent)
			{
				if (!$is_parent)
				{
					$is_parent = true;
					
					$level++;
				}

				$this->tree[$level] = $item;

				$this->generate($items, $key, $level);
			}
		}
	}

}
?>
