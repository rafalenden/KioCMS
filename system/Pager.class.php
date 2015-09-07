<?php
 // KioCMS - Kiofol Content Managment System
// includes/Pager.class.php

class Pager
{
	/*
	$pager = new Pager(...);
	$pager->sort(array(...));
	$pager->limit('news');
	*/
	function __construct($url = false, $items = false, $limit = false)
	{
		$this->items = $items;
		$this->limit = $limit ? $limit : 30;
		//sscanf(HREF.PATH, HREF.$url.'/%u', $this->start);
		$this->start = (int)substr(PATH, strlen($url) + 1);
		$this->url = HREF.$url;
		$this->basic_url = $url;
		$this->calculate();
		return $this;
	}

	// (Re)Calculate numbers
	function calculate()
	{
		$this->pages = ceil($this->items / $this->limit);
		$this->current = $this->start ? $this->start : 0;
		$this->current = $this->current <= $this->pages && $this->current > 1 ? $this->current : 1;
		$this->offset = ($this->current - 1) * $this->limit;
	}

	// Return pagination links
	function getLinks()
	{
		$first = $last = $numbers = $pagination = null;

		if ($this->pages > 1)
		{
			if ($this->pages < 6)
			{
				$right = $this->pages - $this->current;
				$left = $this->current - 1;
			}
			else
			{
				switch ($this->current)
				{
					case 1: $left = 0; $right = 4; break; // First page
					case 2: $left = 1; $right = 3; break;
					case 3: $left = 2; $right = 2; break;
					case $this->pages: $left = 4; $right = 0; break; // Last page
					case $this->pages - 1: $left = 3; $right = 1; break;
					case $this->pages - 2: $left = 2; $right = 2; break;
					default: $left = 2; $right = 2;
				}
				// Go to first page
				if ($this->current > 3)
					$first = '<a href="'.$this->url.'" title="'.t('First page').'">'.t('« first').'</a>';
				// Go to last page
				if ($this->current < $this->pages - 2)
					$last = '<a href="'.$this->url.'/'.$this->pages.'" title="'.t('Last page (%pages)', array('%pages' => $this->pages)).'">'.t('last »').'</a>';
			}
			for ($x = $this->current - $left; $x < $this->current + $right + 1; $x++)
				$numbers .= $this->current == $x ? '<span>'.$x.'</span>' : '<a href="'.$this->url.($x != 1 ? '/'.$x : '').'">'.$x.'</a>';

			$pagination .= $first;
			if ($this->current > 1)
			{
				$pagination .= '<a href="'.$this->url;
				if ($this->current - 1 != 1)
					$pagination .= '/'.($this->current - 1);
				$pagination .= '" title="'.t('Previous page').'">'.t('‹ previous').'</a>';
			}
			$pagination .= ($first ? ' … ' : '').$numbers.($last ? ' … ' : '');
			if ($this->current < $this->pages)
				$pagination .= '<a href="'.$this->url.'/'.($this->current + 1).'" title="'.t('Next page').'">'.t('next ›').'</a>';
			$pagination .= $last;

			return $pagination;
		}
	}

	function select()
	{
		$pagination = null;

		$pagination .= '<p class="pagination"><select name="page">';
		for ($x = $this->current - $left; $x < $this->current + $right + 1; $x++)
			$pagination .= '<option value="'.$x.'" '.($this->current == $x ? 'selected="selected"' : '').$x.'</option>';
		$pagination .= '</select></p>';

		return $pagination;
	}

	// Add users custom limit to SQL query
	function limit($max = 30)
	{
		$post = $_POST['limit-'.$this->basic_url];
		if (is_numeric($post))
		{
			setcookie('KioCMS-'.COOKIE.'-limit-'.$this->basic_url, (int)$post, $max && $post >= $max ? TIMESTAMP : TIMESTAMP + 31536000, '/', '', false, true);
			redirect(HREF.PATH);
		}
		$cookie = $_COOKIE['KioCMS-'.COOKIE.'-limit-'.$this->basic_url];
		$this->limit = is_numeric($cookie) && ($max && $cookie <= $max)
			? (int)$cookie
			: $this->limit;
		$this->limit_form = sprintf('%s na stronę', '<input type="text" class="auto" size="2" name="limit-'.$this->basic_url.'" value="'.$this->limit.'" /> ').' <input type="submit" class="button" value="Pokaż" />';
		$this->calculate();
		return $this;
	}

	// Sorting by DB columns
	function sort($elements, $default, $tendency, $prefix = false)
	{
		global $kio, $cfg;

		$start = array_search('sort', Kio::$url);
		$js = Kio::getConfig('javascript_sort') ? '#' : '';

		if (Kio::$url[$start] == 'sort' && in_array(Kio::$url[$start + 1], $elements) && (Kio::$url[$start + 2] == 'asc' || Kio::$url[$start + 2] == 'desc'))
		{
			$by = array(Kio::$url[$start + 1], Kio::$url[$start + 2], Kio::$url[$start + 2] == 'asc' ? 'desc' : 'asc');
			$this->start = isset(Kio::$url[$start + 3]) ? Kio::$url[$start + 3] : 0; // Update cursor position
			$url = '/sort/'.$by[0].'/'.$by[1];
			$sorting = true;
		}
		else
		{
			$by = array($default, $tendency, $tendency == 'asc' ? 'desc' : 'asc');
			$url = '';
			$sorting = false;
			//$this->start = Kio::$url[substr_count($url, '/') + 1];  // Update cursor position
		}

		foreach ($elements as $key => $value)
		{
			$this->sorters[$value] = '<a href="'.$js.$this->url.'/sort/'.$value.'/'.($by[0] == $value
				? $by[2].'" class="sort '.$by[1].'">'.$key
				: 'asc" class="sort">'.$key).'</a>';
		}

		$this->orderBy = ($sorting
			// 'LENGTH('.$as.$by[0].') > 0 '.$by[2].', IFNULL('.$as.$by[0].', 0) '.$by[1].', '.$as.$by[0].' '.$by[1]
			? $prefix.$by[0].' <> "" '.$by[2].', '.$prefix.$by[0].' '.$by[1]
			: $prefix.$by[0].' '.$by[1]);
		$this->url .= $url;
		$this->calculate();
		return $this;
	}

}