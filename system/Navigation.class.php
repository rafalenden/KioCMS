<?php
// KioCMS - Kiofol Content Managment System
// includes/Navigation.class.php

/**
 * Generate site menu
 * @return ddd
 */
class Navigation extends Tree
{

	function __construct($items = array(), $user_table = 'navigation', $admin_table = 'navigation_admin')
	{
		$this->items = $items;
		$this->table = defined('ADMIN') ? $admin_table : $user_table;
		$this->generate($this->items);
	}

	function getItems()
	{
		global $sql;
		// Make navigation array from query result
		$this->items = Cache::get($this->table . '.txt');
		if ($this->items)
			return $this->items;

		$query = $sql->query('
			SELECT *
			FROM ' . DB_PREFIX . $this->table . '
			ORDER BY display_order');
		while ($row = $query->fetch())
		{
			$this->items[$row['id']] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'parent_id' => $row['parent_id'],
				'url' => $row['url']);
		}

		Cache::put($this->table . '.txt', $this->items);
		return $this->items;
	}

	/**
	 * Sdsasdasdadasdasd
	 * @param $items
	 * @param $parent
	 * @param $level
	 */
	function generate($items = array(), $parent = 0, $level = 0)
	{
		global $cfg;

		if (!$items)
		{
			$items = $this->getItems();
		}

		// Reset the flag each time the function is called
		$is_parent = false;

		// Building tree
		foreach ($items as $key => $value)
		{
			if ($value['parent_id'] == $parent)
			{
				$current = false;

				// Link to front page
				if ($value['url'] == '/')
				{
					$href = LOCAL;
					if (!PATH || Kio::getConfig('front_page') == u0)
					{
						$current = ' class="current"';
					}
				}
				// Internal link
				elseif (strpos($value['url'], '://') === false)
				{
					//PATH == $value['url'] && $current = ' class="current"';
					$href = HREF . $value['url'];
					if (strpos(HREF . PATH . '/', $href . '/') !== false)
						$current = ' class="current"';
				}
				// External link
				else
					$href = $value['url'];

				if (!$is_parent)
				{
					$is_parent = true;
					$this->content .= '<ul class="level-' . $level . '">';
					$level++;
				}

				$value['url'] ? $href = ' href="' . $href . '"' : $current = $href = '';

				$this->content .= '<li' . $current . '><a' . $href . '>' . $value['name'] . '</a>';
				$this->generate($items, $key, $level);
				$this->content .= '</li>';
			}
		}
		// Close list if the wrapper above is opened
		if ($is_parent)
			$this->content .= '</ul>';
	}
}