<?php
 // KioCMS - Kiofol Content Managment System
// includes/blocks.php

class Logger
{
	function __construct($vars = array(), $id = false, $form_auth = true)
	{
		if ($vars)
			foreach ($vars as $var)
				$this->{$var} = false;
	}

	function __call($error_name, $arg)
	{
		list($message, $condition) = $arg;
		$this->{$error_name} = $condition ? ($message ? $message : '?') : false;
		return $this;
	}

	function __toString()
	{
		return end($this);
	}

	function check()
	{
		foreach ($this as $errcode => $vars)
			$this->{$errcode} = $vars[1] ? ($vars[0] ? $vars[0] : '?') : false;
	}

	function as_array()
	{
		return get_object_vars($this);
	}

	function toArray()
	{
		return get_object_vars($this);
	}

	function no_errors()
	{
		return !in_array(true, (array)$this);
	}

	function count()
	{
		return array_search(true,  get_object_vars($this));
	}

	function isErrors()
	{
		return array_search(true,  get_object_vars($this));
	}
}