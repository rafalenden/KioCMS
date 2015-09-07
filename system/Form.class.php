<?php
 // KioCMS - Kiofol Content Managment System
// includes/blocks.php
//
//$form = new Form();
//// filter(string, limit)
//$form->elements(array(
//	'logname'    => array(
//		'value' => $_POST['logname'] ? filter($_POST['logname'], 100) : '',
//		'error_condition' => !$form['logname'],
//		'error_info' => $lang['ERROR_LOGNAME']),
//	'nickname'   => $_POST['nickname'] ? filter($_POST['nickname'], 100) : '',
//	'pass'       => $_POST['pass'] ? filter($_POST['pass'], 100) : '',
//	'pass2'      => $_POST['pass2'] ? filter($_POST['pass2'], 100) : '',
//	'email'      => strtolower(filter($_POST['email'], 100)),
//	'rules'      => $_POST['rules'] ? true : false,
//	'newsletter' => $_POST['newsletter'] ? 1 : 0,
//	'pm_notify'  => $_POST['pm_notify'] ? 1 : 0,
//	'hide_email' => $_POST['hide_email'] ? 1 : 0));
//
//$form->errors(array(
//	!$form['logname'] => $lang['ERROR_LOGNAME'],
//	is_registered($form['logname'], 'logname') => $lang['ERROR_LOGNAME2']));

class Form extends Error
{
	function __construct()
	{
		$this->errors = array();
	}
	
	function elements($elements = array())
	{
		foreach ($elements as $element)
		{
			$this->$element = element;
		}
	}
	function errors($errors)
	{
		foreach ($errors as $condition => $info)
		{
			$this->errors[] = $condition ? $info : false;
		}
	}
}