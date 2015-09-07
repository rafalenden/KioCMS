<?php
// KioCMS - Kiofol Content Managment System
// includes/blocks.php

/**
 * 
 */
class Notifier
{
	private $content = array();
	private $class = '';
	private $id = '';

	public function __construct($id = false)
	{
		if (!empty($id))
		{
			$this->id = $id;
		}
	}

	public function __toString()
	{
		$output = $this->output();
		return $output ? $output : '';
	}

	public function error($content, $session = false, $class = false)
	{
		if ($this->class != 'error')
			$this->content = array();
		$this->content = array_merge($this->content, (array)$content);
		$this->class = $class ? $class : 'error';

		if ($session)
			$_SESSION['note-'.$this->id] = serialize(array('class' => $this->class, 'content' => $this->content));

		return $this;
	}

	public function info($content, $session = false, $class = false)
	{
		if ($this->class != 'info')
			$this->content = array();
		$this->content = array_merge($this->content, (array)$content);
		$this->class = $class ? $class : 'info';
		//if ($id) $this->id = $id;

		if ($session)
			$_SESSION['note-'.$this->id] = serialize(array('class' => $this->class, 'content' => $this->content));
	}

	public function success($content, $session = true, $class = false)
	{
		if ($this->class != 'success')
			$this->content = array();
		$this->content = array_merge($this->content, (array)$content);
		$this->class = $class ? $class : 'success';

		if ($session)
			$_SESSION['note-'.$this->id] = serialize(array('class' => $this->class, 'content' => $this->content));
	}

	public function warning($content, $session = true, $class = false)
	{
		if ($this->class != 'warning')
			$this->content = array();
		$this->content = array_merge($this->content, (array)$content);
		$this->class = $class ? $class : 'warning';

		if ($session)
			$_SESSION['note-'.$this->id] = serialize(array('class' => $this->class, 'content' => $this->content));
	}

	public function output()
	{
		if (!empty($_SESSION['note-'.$this->id]))
		{
			$note = unserialize($_SESSION['note-'.$this->id]);
			$this->class = $note['class'];
			$this->content = $note['content'];
			// $this->content = array_map('filter', (array)unserialize($_SESSION['positive-'.$this->id]));
			$_SESSION['note-'.$this->id] = null;
		}

		if ($this->content)
		{
			$messages = '';
			foreach ((array)$this->content as $message)
				if ($message)
				{
					$messages .= '<li>' . $message . '</li>';
				}

			$this->content = null;

			return '<div class="note '.$this->class.'"'.($this->id ? ' id="'.$this->id.'"' : null).'><ol>'.$messages.'</ol></div>';
		}
	}

	public function restore()
	{
		$this->class = $this->content = null;
		return $this;
	}

	public static function factory($id = false)
	{
		$instance = new Notifier($id);
		return $instance;
	}
}