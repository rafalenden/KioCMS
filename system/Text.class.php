<?php
 // KioCMS - Kiofol Content Managment System
// includes/Navigation.class.php

class Text
{
	static function parse($str, $parsers = 12345)
	{
		global $cfg, $kio;
	
		// 1 (BBCODE) - BBCode
		if (strpos($str, '[') !== false && substr_count($cfg->system['parsers'].$parsers, 1) == 2)
			$str = preg_replace(array_keys($kio->bbcode), $kio->bbcode, $str);
	
		// 2 (AUTOLINKS) - Automatic links generator
		if (substr_count($cfg->system['parsers'].$parsers, 2) == 2)
		{
			$autolinks = array(
				// email@domain
				'#(^|[\n\s\[ <>])(^[\w\d-]+?(?:\.[\w\d\+-]+)*)@([a-z\d-]+(?:\.[a-z\d-]+)*\.[a-z]{2,6})$#ise' => '\'\1<a href="\'.mailto(\'\2@\3\').\'">\'.hash_email(\'\2@\3\').\'</a>\'',
				// xxxx://yyyy
				'#(^|[\n\s\[ <>])([\w]+?://[\w\#$%&~/.\-;:=,?@+]+)#is' => '\1<a href="\2" class="external" rel="nofollow">\2</a>',
				// www|ftp.xxxx.yyyy[/zzzz]
				'#(^|[\n\s\]])((www|ftp)\.[\w\#$%&~/.\-;:=,?@+]+)#is' => '\1<a href="http://\2" class="external" rel="nofollow">\2</a>');
			$str = preg_replace(array_keys($autolinks), $autolinks, $str);
	
			//'#((www|ftp)\.[a-z0-9_/?\-&%\.]+)#si' => '<a href="http://$1" class="external" rel="nofollow">$1</a>');
			//'#[a-z]+://([-]*[.]?[a-z0-9_/-?&%])*#i' => '<a href="$0" class="external" rel="nofollow">$0</a>',
			//"'$1<a href=\"'.mailto('$2@$3').'\">'.hash_email('$2@$3').'</a>'",
			// [a-z0-9_\+-]+(\.[a-z0-9_\+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*\.([a-z]{2,4})
			// ([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is z WordPressa wzięte
			// Manual PHP - '#((?:https?|ftp)://\S+[[:alnum:]]/?)#i' => '<a href="$1" class="external" rel="nofollow">$1</a>',
			// #([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is
			//'#(^| )(www([-]*[.]?[a-z0-9_/-?&%])*)#i' => '<a href="http://$2" class="external" rel="nofollow">$2</a>');
			//'#((?<!//)(www\.\S+[[:alnum:]]/?))#si' => '<a href="http://$1" class="external" rel="nofollow">$1</a>');
		}
	
		// 3 (EMOTICONS) - Emoticons
		if (substr_count($cfg->system['parsers'].$parsers, 3) == 2) $replace = $kio->emoticons;
		// 4 (CENSURE) - Censure
		if (substr_count($cfg->system['parsers'].$parsers, 4) == 2) $replace += $kio->censure;
		if ($replace) $str = str_ireplace(array_keys($replace), $replace, $str);
	
		// 5 (PRE) - Treat string like in <pre> tag
		if (substr_count($cfg->system['parsers'].$parsers, 5) == 2)
		{
			// New line
			$str = nl2br($str);
	
			// Spaces
			$str = str_replace(
				array("\t", '  ', '  '),
				array('&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;'), $str);
		}

		return $str;
	}

	static function clear($str, $limit = false, $filters = 12, $codename = false, $last_ip = false)
	{
		global $cfg, $kio;
		if ($limit && mb_strlen($str) > $limit) $str = mb_substr($str, 0, $limit);
	
		// 1 - Remove whitespace from the beginning and end of a string
		if (substr_count($cfg->system['filters'].$filters, 1) == 2)
			$str = trim($str);
		// 2 - No HTML
		substr_count($cfg->system['filters'].$filters, 2) == 2 && $str = preg_replace('#(script|about|applet|activex|chrome):#is', '\1&#058;', htmlspecialchars(stripslashes($str), ENT_QUOTES, 'UTF-8'));
		// 3 - Antispam
		if (substr_count($cfg->system['filters'].$filters, 3) == 2 && preg_match_all('#'.$kio->spam_words.'#i', $str, $words))
			$kio->{'spam-'.$codename} = implode(', ', array_unique($words[0]));
		// 4 - Antiflooder (Check by last entry IP)
		if (IP == $last_ip && substr_count($cfg->system['filters'].$filters, 4) == 2)
			define('FLOOD', 1);
		// 5 - Antiflooder (Check by cookie)
		elseif ($_COOKIE['KioCMS-'.COOKIE.'-'.$codename] && substr_count($cfg->system['filters'].$filters, 5) == 2 && !defined('FLOOD'))
			define('FLOOD', 2);
	
		return str_replace(
			// Input
			array('`', '\\'),
			// Output
			array('&#96;', '&#92;'), r2n($str));
	}

	function url($str)
	{
		global $kio;
		return strtolower(preg_replace('#[^\d\w-]#', '', strtr(urldecode($str), $kio->chars)));
	}

	function __construct($items = false, $table = 'navigation', $admin_table = 'navigation_admin')
	{
		$this->items = $items;
		$this->table = $table;
		$this->admin_table = $admin_table;
		$this->current = defined('ADMIN') ? $this->admin_table : $this->table;
		$this->generate($this->items);
	}

	function get_items()
	{
		global $sql;
		// Make navigation array from query result
		$query = $sql->queryCache('
			SELECT *
			FROM '.DB_PREFIX.$this->current.'
			ORDER BY display_order', $this->current.'.txt');
		foreach ($query as $row)
		{
			$this->items[$row['id']] = array(
				'id' => $row['id'],
				'name' => $row['name'],
				'parent_id' => $row['parent_id'],
				'url' => $row['url']);
		}
		return $this->items;
	}

	function generate($items = array(), $parent = 0, $level = 0)
	{
		if (!$items) $items = $this->get_items();

		// Reset the flag each time the function is called
		$has_children = false;

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
					if (!PATH) $current = ' class="current"';
				}
				// Internal link
				elseif (strpos($value['url'], '://') === false)
				{
					//PATH == $value['url'] && $current = ' class="current"';
					$href = HREF.$value['url'];
					if (strpos(HREF.PATH.'/', $href.'/') !== false) 
						$current = ' class="current"';
				}
				// External link
				else
					$href = $value['url'];

				if (!$has_children)
				{
					$has_children = true;
					$this->content .= '<ul class="level-'.$level.'">';
					$level++;
				}

				if ($value['url'])
					$href = ' href="'.$href.'"';
				else
					$current = $href = '';

				$this->content .= '<li'.$current.'><a'.$href.'>'.$value['name'].'</a>';
				$this->generate($items, $key, $level);
				$this->content .= '</li>';
			}
		}
		// Close list if the wrapper above is opened
		if ($has_children)
			$this->content .= '</ul>';
	}
}