<?php

class Lang
{
	var $collection, $list, $_all = array();

	public function __construct($output = false)
	{
		 //$this->container = $this->load($output);
	}

	function __call($section, $arg)
	{
		list($file, $collect) = $arg;

		$this->_list[] = $section;

		$collect ? $this->_collection[] = $section : $this->_collection = array($section);

		$this->{$section} = $this->load($file);
		$this->_all = (array)$this->{$section} + $this->_all;
		return $this;
	}

	public function setPrev()
	{
		$this->_collection = array(next($this->_list));
	}

	public static function load($file, $lang = false)
	{
		global $kio;
		if (!$lang) $lang = LANG;

		if ($output = @include ROOT.str_replace('*', $lang, $file)) $kio->lang = (array)$output + (array)$kio->lang;
		elseif ($output = @include ROOT.str_replace('*', 'en', $file)) return $output;
		else return false; // print($file)
	}

	public static function save($file, $lang = false)
	{
		global $kio;
		if (!$lang) $lang = LANG;

		if ($output = @include ROOT.str_replace('*', $lang, $file))
		{
			$kio->lang += (array)$output;
			return $kio->lang = array_unique($kio->lang);
		}
		elseif ($output = @include ROOT.str_replace('*', 'en', $file)) return $output;
		else return false; // print($file)
	}

	public static function load0($file, $section = false, $lang = false)
	{
		if (!$lang) $lang = LANG;

		$output = cache_get('lang_'.$lang.'_'.md5($file).'.txt');
		if ($output) return $output;

		if ($output = file_get_contents(ROOT.str_replace('*', $lang, $file)))
		{
			$output = parse_ini($output);
			cache_put('lang_'.$lang.'_'.md5($file).'.txt', $output);
			return $output;
		}
		elseif ($output = cache_get('lang_en_'.md5($file).'.txt')) return $output;
		elseif ($output = file_get_contents(ROOT.$file.'.en.ini'))
		{
			$output = parse_ini($output);
			cache_put('lang_en_'.md5($file).'.txt', $output);
			return $output;
		}
		else
			return false; // print($file)
	}

	public static function get_list()
	{
		$dir = glob(ROOT.'lang/info.*.ini');
		$langs = array();
		foreach ($dir as $lang)
		{
			$lang = substr($lang, -6, 2);
			$info = parse_ini(file_get_contents(ROOT.'lang/info.'.$lang.'.ini'));
			$langs[$lang] = $info['NAME'];
		}
		return $langs;
	}

	public static function t($msgid, $current = false)
	{
		global $lang;

		if ($current)
			return isset($lang->{$current}[$msgid])
				? $lang->{$current}[$msgid]
				: (isset($lang->system[$msgid]) ? $lang->system[$msgid] : $msgid);

		foreach ($lang->_collection as $secton)
			if (isset($lang->{$secton}[$msgid])) return $lang->{$secton}[$msgid];

		return isset($lang->system[$msgid]) ? $lang->system[$msgid] : $msgid;
	}

	function t_old($msgid, $current = false)
	{
		global $lang;
		$current = $current ? $current : $lang->{$lang->current};
		return isset($current[$msgid])
			? $current[$msgid]
			: (isset($lang->system[$msgid]) ? $lang->system[$msgid] : $msgid);
	}

	public static function render_list()
	{
		$list = get_list();
		$langs = null;
		foreach ($list as $code => $name)
		{
			$langs .= LANG == $code
				// Active
				? '<a href="'.LOCAL.$code.(u0 ? '/'.PATH : '').'" class="active" title="'.$name.'"><img src="'.LOCAL.'images/flags/'.$code.'.png" alt="'.$code.'" /></a> '
				// Inactive
				: '<a href="'.LOCAL.$code.(u0 ? '/'.PATH : '').'" class="inactive" title="'.$name.'"><img src="'.LOCAL.'images/flags/'.$code.'.png" alt="'.$code.'" /></a> ';
		}
		return $langs;
	}
}
