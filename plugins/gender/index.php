<?php
 // KioCMS - Kiofol Content Managment System
// includes/gender.php

class Gender
{
	function __construct($arg)
	{
		list($this->number, $this->men, $this->women, $this->unknown) = $arg;
	}

	function __toString()
	{
		switch ($this->number)
		{
			case 1: $this->gender = $this->men; break;
			case 2: $this->gender = $this->women; break;
			default: $this->gender = $this->unknown;
		}
		return $this->gender;
	}
}
?>