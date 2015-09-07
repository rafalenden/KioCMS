<?php
 // KioCMS - Kiofol Content Managment System
// plugins/panel/index.php

class panel
{
	function __construct($arg)
	{
		list($this->element_name, $this->parsers) = $arg;
	}

	function __toString()
	{
		$tpl = new PHPTAL('plugins/panel/buttons.html');
		$tpl->element_name = $this->element_name;
		return $tpl->execute();
	}
}