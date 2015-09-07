<?php

// KioCMS - Kiofol Content Managment System
// includes/blocks.php

class Error
{
	private $counter = 0;
	private $errors = array();

	/**
	 * Assign errors while creating instance
	 * @param array $vars
	 */
	function __construct($vars = array())
	{
		if ($vars)
			foreach ($vars as $var)
				$this->{$var} = false;
	}

	function __call($error_name, $arg)
	{
		list($message) = $arg;
		return $this->setError($error_name, $message);
	}

	function setError($error_name, $message)
	{
		$this->errors[$error_name] = $message;

		return $this;
	}

	function __toString()
	{
		return end($this);
	}

	public function condition($condition = false, $charsLimit = 0)
	{
		$current = current($this->errors);
		$key = key($this->errors);
		
		if ($condition)
		{
			$this->errors[$key] = $current
				? $current
				: t('Unknown error.');

			$this->counter++;
		}
		else
		{
			$this->errors[$key] = false;
		}
		
		next($this->errors);
		
		return $this;
	}

	function check()
	{
		foreach ($this as $errcode => $vars)
		{
			$this->{$errcode} = $vars[1] ? ($vars[0] ? $vars[0] : '?') : false;
		}
	}

	function toArray()
	{
		return $this->errors;
	}

	function noErrors()
	{
		//var_export(get_object_vars($this));
		return $this->counter == 0;
	}

	function count()
	{
		return $this->counter;
	}

	function isErrors()
	{
		return array_search(true, get_object_vars($this));
	}
	
	public function isError($error_name)
	{
		return !empty($this->errors[$error_name]);
	}

}