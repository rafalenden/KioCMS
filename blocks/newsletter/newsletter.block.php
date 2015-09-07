<?php

// KioCMS - Kiofol Content Managment System
// blocks/newsletter/index.php
class Newsletter extends Block
{

	public function getContent()
	{
		$err = new Error();
		$note = new Notifier('note-newsletter');

		$form = array();
		$tpl = 'blocks/newsletter/newsletter_form.html';

		if (isset($_POST['add-newsletter'])
			|| isset($_POST['delete-newsletter'])
			|| isset($_POST['delete2-newsletter']))
		{
			include_once ROOT.'blocks/newsletter/action.php';
		}

		try
		{
			$tpl = new PHPTAL($tpl);
			$tpl->err = $err->toArray();
			$tpl->note = $note;
			$tpl->form = $form;
			return $tpl->execute();
		}
		catch (Exception $e)
		{
			return template_error($e->getMessage());
		}
	}
}