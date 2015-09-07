<?php
 // KioCMS - Kiofol Content Managment System
// modules/contact/admin/index.php

$kio->path['admin/modules/contact'] = t('Contact');
$note = new Notifier();
$err = new Error();

$save = $_POST['save'] ? true : false;
$blocks = Settings::getBlocks();

if ($save)
{
	$form = $_POST['form'];
	$form['blocks'] = array_diff(array_keys($blocks), (array)$_POST['blocks']);

	$err->receivers_empty($lang2['ERROR_RECEIVERS_EMPTY'], !$form['receivers']);
	$err->receivers_invalid($lang2['ERROR_RECEIVERS_INVALID'], $form['receivers'] && !preg_match('#^\d+(, *\d)*$#', $form['receivers']));

	if (!$err->count())
	{
		Settings::update('contact');
		Cache::clear('contact.txt');
		$info->positive(t('SAVED_SUCCESSFUL'));
		redirect(HREF.'admin/modules/contact');
	}
	else
		$note->error($err);
}
else
{
	$form = $cfg->contact;
	$form['blocks'] = explode(', ', $cfg->contact['blocks']);

	$note->info(array($lang_admin['MODULE_SETTINGS'], $lang_system['REQUIRED']));
}

$tpl = new PHPTAL('modules/contact/admin/settings.html');
$tpl->note = $note;
$tpl->form = $form;
$tpl->err = $err;
$tpl->columns = Settings::formColumns();
$tpl->blocks = Settings::formBlocks();
echo $tpl->execute();