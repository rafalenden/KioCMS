<?php
 // KioCMS - Kiofol Content Managment System
// includes/Guard.class.php

class Guard
{
	function __construct($lang = false)
	{
		$this->lang = $lang;
	}

	function check($lang = false)
	{
		foreach ($this->errors as $key => $condition)
			if ($condition)
				$errors[] = $this->lang[$key] ? $this->lang[$key] : $key;
		$this->errors = $errors;
	}
}