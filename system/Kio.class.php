<?php

// KioCMS - Kiofol Content Managment System
// includes/blocks.php

class Kio
{
	public static $langPhrases = array();

	private static $config = array();
	private static $stats = array();
	private static $groups = array();
	private static $description, $keywords, $head;
	private static $cssFiles = array();
	private static $cssCode = array();
	private static $jsFiles = array();
	private static $jsCode = array();
	private static $timer = array();
	private static $title = array();
	public static $path = array();
	public static $months, $monthsFormated, $days, $daysFormated, $today, $yesterday, $tommorow;
	public static $show_blocks = true, $blocks, $columns, $functions;
	public static $lol = array();

	public static $url = array();
	public static $urlPrefix = array();

	private static $tabs = array();
	public static $timeZones = array(
		-12 => 'Pacific/Kwajalein', -11 => 'Pacific/Samoa', -10 => 'Pacific/Honolulu', -9.5 => 'Pacific/Marquesas',
		-9 => 'Pacific/Gambier', -8 => 'America/Tijuana', -7 => 'America/Chihuahua', -6 => 'America/Chicago',
		-5 => 'America/New_York', -4.5 => 'America/Caracas', -4 => 'America/Santiago', -3.5 => 'America/St_Johns',
		-3 => 'America/Buenos_Aires', -2 => 'Atlantic/South_Georgia', -1 => 'Atlantic/Cape_Verde',
		0 => 'Europe/London',
		1 => 'Europe/Paris', 2 => 'Europe/Helsinki', 3 => 'Europe/Moscow', 3.5 => 'Asia/Tehran',
		4 => 'Asia/Baku', 4.5 => 'Asia/Kabul', 5 => 'Asia/Karachi', 5.5 => 'Asia/Calcutta',
		5.75 => 'Asia/Katmandu', 6 => 'Asia/Dhaka', 6.5 => 'Asia/Rangoon', 7 => 'Asia/Bangkok',
		8 => 'Asia/Hong_Kong', 9 => 'Asia/Tokyo', 9.5 => 'Australia/Adelaide', 10 => 'Australia/Sydney',
		10.5 => 'Australia/Lord_Howe', 11 => 'Asia/Magadan', 11.5 => 'Pacific/Norfolk', 12 => 'Pacific/Fiji',
		12.75 => 'Pacific/Chatham', 13 => 'Pacific/Tongatapu');

	function __construct()
	{
		//$this->start = microtime(true);
	}

	public static function loadConfig()
	{
		global $sql;

		if (!(self::$config = $sql->getCache('config')))
		{
			$query = $sql->query('SELECT config_owner, config_name, config_value FROM '.DB_PREFIX.'config');

			while ($row = $query->fetch())
			{
				self::$config[$row['config_owner']][$row['config_name']] = $row['config_value'];
			}

			$sql->putCacheContent('config', self::$config);
		}
	}

	public static function getConfig($config_name, $config_owner = 'system')
	{
		return isset(self::$config[$config_owner][$config_name])
			? self::$config[$config_owner][$config_name]
			: '';
	}

	public static function loadStats()
	{
		global $sql;

		if (!(self::$stats = $sql->getCache('stats')))
		{
			$query = $sql->query('SELECT stat_owner, stat_name, stat_value FROM '.DB_PREFIX.'stats');

			while ($row = $query->fetch())
			{
				self::$stats[$row['stat_owner']][$row['stat_name']] = $row['stat_value'];
			}

			$sql->putCacheContent('stats', self::$stats);
		}
	}

	public static function getStat($stat_name, $stat_owner = 'system')
	{
		return self::$stats[$stat_owner][$stat_name];
	}

	public static function loadGroups()
	{
		global $sql;

		if (!(self::$groups = $sql->getCache('groups')))
		{
			$query = $sql->query('SELECT * FROM '.DB_PREFIX.'groups');

			while ($row = $query->fetch(PDO::FETCH_ASSOC))
			{
				self::$groups[$row['id']] = $row;
			}

			$sql->putCacheContent('groups', self::$groups);
		}
	}
	public static function getGroup($group_id, $varname = false)
	{
		return $varname ? self::$groups[$group_id][$varname] : self::$groups[$group_id];
	}

	public static function loadLangPhrases()
	{
		global $sql;

		//setlocale(LC_ALL, $lang_system['LOCALE']); ////
		// Get language phrases
		if (!(self::$langPhrases = $sql->getCache('lang_'.LANG)))
		{
			$query = $sql->query('
				SELECT DISTINCT * FROM '.DB_PREFIX.'lang_phrases
				LEFT JOIN '.DB_PREFIX.'lang_translations
				ON translation_key = phrase_value AND translation_lang = "'.LANG.'"');

			while ($row = $query->fetch())
			{
				self::$langPhrases[$row['phrase_value']] = $row['translation_value'];
			}

			$sql->putCacheContent('lang_'.LANG, self::$langPhrases);
		}
	}

	public static function addCssFile($css_path)
	{
		if (!in_array($css_path, self::$cssFiles))
		{
			self::$cssFiles[] = $css_path;
		}
	}

	public static function getCssFiles()
	{
		if (self::$cssFiles)
		{
			$css = '<style type="text/css">'."\n";
			foreach (self::$cssFiles as $path)
				$css .= "\t".'@import url(\''.LOCAL.$path.'\');'."\n";
			$css .= '</style>'."\n";
			return $css;
		}
	}

	public static function addCssCode($css_code)
	{
		if (!in_array($css_code, self::$cssCode))
		{
			self::$cssCode[] = $css_code;
		}
	}

	public static function getCssCode()
	{
		if (self::$cssCode)
		{
			$css = '<style type="text/css">'."\n";

			foreach (self::$cssCode as $code)
			{
				$css .= $code."\n";
			}

			$css .= '</style>'."\n";

			return $css;
		}
	}

	public static function addJsFile($js_path)
	{
		if (!in_array($js_path, self::$jsFiles))
		{
			self::$jsFiles[] = $css_path;
		}
	}

	public static function getJsFiles()
	{
		if (self::$jsFiles)
		{
			$js = '';

			foreach (self::$jsFiles as $path)
			{
				'<script type="text/javascript" src="'.LOCAL.$path.'"></script>'."\n";
			}

			return $js;
		}
	}

	public static function addJsCode($js_code)
	{
		if (!in_array($js_code, self::$jsCode))
		{
			self::$jsCode[] = $js_code;
		}
	}

	public static function getJsCode()
	{
		if (self::$jsCode)
		{
			$js = '<script type="text/javascript">'."\n";

			foreach (self::$jsCode as $code)
			{
				$js .= $code."\n";
			}

			$js .= '</script>'."\n";
			return $js;
		}
	}

	public static function addTitlePart($title)
	{
		self::$title[] = $title;
	}

	public static function addTitle($title)
	{
		self::$title[] = $title;
	}

	public static function setTitle($title)
	{
		self::$title = array($title);
	}

	public static function getTitle()
	{
		return implode(self::getConfig('separator'), array_reverse(self::$title));
	}

	public static function setDescription($description)
	{
		self::$description = $description;
	}

	public static function getDescription()
	{
		return self::$description;
	}

	public static function setKeywords($keywords)
	{
		self::$keywords = $keywords;
	}

	public static function getKeywords()
	{
		return self::$keywords;
	}

	public static function addHead($head)
	{
		if ($head)
		{
			self::$head[] = $head;
		}
	}

	public static function setHead($head)
	{
		self::$head = array($head);
	}

	public static function getHead()
	{
		return self::$head;
	}

	public static function addBreadcrumb($name, $url = false)
	{
		if ($url)
		{
			self::$path[$url] = $name;
		}
		else
		{
			self::$path[] = $name;
		}
	}

	public static function setBreadcrumb($name, $url = false)
	{
		if ($url)
		{
			self::$path = array($url => $name);
		}
		else
		{
			self::$path = array($name);
		}
	}

	public static function breadcrumbsExists()
	{
		return u0 && (!empty(self::$path[null]) || preg_grep('#[A-z]#', array_keys(self::$path))) ? true : false;
	}

	public static function getModuleName()
	{
		return end(self::$title);
	}

	public static function startTimer($name = 'total')
	{
		self::$timer[$name] = microtime(true);
	}

	public static function getTimer($name = 'total')
	{
		return round((microtime(true) - self::$timer[$name]), 3);
	}

	public static function addTabs($tabs, $index = false)
	{
		self::$tabs[] = $tabs;
	}

	public static function getTabs($tabs_index)
	{
		if (empty(self::$tabs[$tabs_index]))
		{
			return;
		}

		$tabs = '';
		$active = false;

		$first = reset(self::$tabs[$tabs_index]);

		$section = implode('/', array_slice(self::$url, 0, substr_count(end(self::$tabs[$tabs_index]), '/') + 1));
		$section_exists = in_array($section, self::$tabs[$tabs_index]);

		$tabs = '<ul class="tabs">';

		foreach (self::$tabs[$tabs_index] as $name => $url)
		{
			if (is_array($url))
			{
				$pattern = !empty($url[0]) ? $url[0] : $url;
				$url = !empty($url[1]) ? $url[1] : $url;
				$active = !$active && preg_match('#'.$pattern.'#', PATH) ? ' class="current"' : '';
			}
			else
			{
				$pattern = $url;
				$active = !$active && strpos(PATH, $pattern) === 0 ? ' class="current"' : '';
			}

			$tabs .= '<li'.$active.'><a href="'.HREF.$url.'">'.$name.'</a></li>';
		}

		$tabs .= '</ul>';

		return $tabs;
	}

	function xxx()
	{
		global $cfg;

		$this->path[] = $cfg->system['title'];
		$this->description = $cfg->system['description'];
		$this->keywords = $cfg->system['keywords'];
		$this->header = $cfg->system['header'];
		$this->show_blocks = true;
		$this->blocks = $cfg->system['blocks'];
		$this->columns = $cfg->system['columns'];
		$this->functions = array(
			'set_magic_quotes_runtime', 'ini_set', 'date_default_timezone_set', 'mb_strlen');
		$this->functions = array_map('function_exists', array_combine($this->functions, $this->functions));

		// Dla wielozdaniowych fraz należy użyć \s - tanie\skomputery
		$this->spam_words = str_replace(
				// Input
				array(',', ' ', '#'),
				// Output
				array('|', '', ''), $cfg->system['spam_words']);

		// Characters to replace
		$this->chars = unserialize($cfg->system['chars']) + array(
			// Special chars (Input => Output)
			' ' => '_', '&#92;' => '', '&#34;' => '', '&#039;' => '', '&#96;' => '',
			'&quot;' => '', '&gt;' => '', '&lt;' => '', '&amp;' => '');

		// Check timezone_identifiers_list()
		$this->time_zones = array(
			-12 => 'Pacific/Kwajalein', -11 => 'Pacific/Samoa', -10 => 'Pacific/Honolulu', -9.5 => 'Pacific/Marquesas',
			-9 => 'Pacific/Gambier', -8 => 'America/Tijuana', -7 => 'America/Chihuahua', -6 => 'America/Chicago',
			-5 => 'America/New_York', -4.5 => 'America/Caracas', -4 => 'America/Santiago', -3.5 => 'America/St_Johns',
			-3 => 'America/Buenos_Aires', -2 => 'Atlantic/South_Georgia', -1 => 'Atlantic/Cape_Verde',
			0 => 'Europe/London',
			1 => 'Europe/Paris', 2 => 'Europe/Helsinki', 3 => 'Europe/Moscow', 3.5 => 'Asia/Tehran',
			4 => 'Asia/Baku', 4.5 => 'Asia/Kabul', 5 => 'Asia/Karachi', 5.5 => 'Asia/Calcutta',
			5.75 => 'Asia/Katmandu', 6 => 'Asia/Dhaka', 6.5 => 'Asia/Rangoon', 7 => 'Asia/Bangkok',
			8 => 'Asia/Hong_Kong', 9 => 'Asia/Tokyo', 9.5 => 'Australia/Adelaide', 10 => 'Australia/Sydney',
			10.5 => 'Australia/Lord_Howe', 11 => 'Asia/Magadan', 11.5 => 'Pacific/Norfolk', 12 => 'Pacific/Fiji',
			12.75 => 'Pacific/Chatham', 13 => 'Pacific/Tongatapu');

		$this->bbcode = eval('?>'.file_get_contents(ROOT.'includes/parser/bbcode/'.($cfg->system['bbcode_parser'] ? $cfg->system['bbcode_parser'].'.php' : 'index.php')));
		$this->emoticons = eval('?>'.file_get_contents(ROOT.'includes/parser/emoticons/'.($cfg->system['emoticons_parser'] ? $cfg->system['emoticons_parser'].'.php' : 'index.php')));
		$this->censure = eval('?>'.file_get_contents(ROOT.'includes/parser/censure/'.($cfg->system['censure_parser'] ? $cfg->system['censure_parser'].'.php' : 'index.php')));
	}

	function addBlock($params)
	{
		$this->blocks[$params['side']] += $params;
	}

	function removeBlock($codename)
	{
		
	}

	function tabs($tabs)
	{
		$first = reset($tabs);
		$section = implode('/', array_slice($this->url, 0, substr_count(end($tabs), '/') + 1));
		$section_exists = in_array($section, $tabs);
		$this->tabs = '<ul class="tabs">';
		$active = false;

		foreach ($tabs as $name => $url)
		{
			if (is_array($url))
			{
				$pattern = !empty($url[0]) ? $url[0] : $url;
				$url = !empty($url[1]) ? $url[1] : $url;
				$active = !$active && preg_match('#'.$pattern.'#', PATH) ? ' class="current"' : '';
			}
			else
			{
				$pattern = $url;
				$active = !$active && strpos(PATH, $pattern) === 0 ? ' class="current"' : '';
			}

			$this->tabs .= '<li'.$active.'><a href="'.HREF.$url.'">'.$name.'</a></li>';
		}

		$this->tabs .= '</ul>';
	}

	public static function langSwitcher()
	{
		$flags = '';
		$all_langs = get_all_langs();
		foreach ($all_langs as $code => $name)
		{
			if (LANG == $code)
			{
				$flags .= '
					<a href="'.LOCAL.$code.(u0 ? '/'.PATH : '').'" class="active" title="'.$name.'">
						<img src="'.LOCAL.'images/flags/'.$code.'.png" alt="'.$code.'" />
					</a>';
			}
			else
			{
				$flags .= '
					<a href="'.LOCAL.$code.(u0 ? '/'.PATH : '').'" class="inactive" title="'.$name.'">
						<img src="'.LOCAL.'images/flags/'.$code.'.png" alt="'.$code.'" />
					</a> ';
			}
		}
		return $flags."\n";
	}

	public static function getBreadcrumbs()
	{
		$path = '';
		//array_shift($this->path);

		foreach (self::$path as $key => $value)
			if (!is_int($key))
				$path .= ' › <a'.($key ? ' href="'.HREF.$key.'"' : '').'>'.$value.'</a>';

		return $path;
	}

	public static function setTimeLocales()
	{
		global $cfg;


		date_default_timezone_set(self::$timeZones[!empty(User::$timeZone) ? User::$timeZone : Kio::getConfig('time_zone')]);

		// Translate days and months in date()
		if (TRANSLATE_DATE && preg_match('#F|M|l|d#', self::getConfig('date_format').self::getConfig('short_date_format')))
		{
			self::$months = self::$monthsFormated = array(
				1 => 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',
				13 => 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

			self::$days = self::$daysFormated = array(
				1 => 'Monday', 'Tu', 'We', 'Th', 'Fr', 'Saturday', 'Sunday',
				8 => 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun');

			for ($i = 1; $i < 25; $i++)
				self::$monthsFormated[$i] = strtr(addcslashes(t(self::$months[$i]), 'A..z'), array('l' => '&#108;', 'D' => '&#68;'));

			for ($i = 1; $i < 15; $i++)
				self::$daysFormated[$i] = strtr(addcslashes(t(self::$days[$i]), 'A..z'), array('l' => '&#108;', 'D' => '&#68;'));
		}

		if (Kio::getConfig('time_relative'))
		{
			self::$today = strtr(addcslashes(t('Today'), 'A..z'), array('l' => '&#108;', 'D' => '&#68;'));
			self::$yesterday = strtr(addcslashes(t('Yesterday'), 'A..z'), array('l' => '&#108;', 'D' => '&#68;'));
			self::$tommorow = strtr(addcslashes(t('Tommorow'), 'A..z'), array('l' => '&#108;', 'D' => '&#68;'));
		}
	}
}