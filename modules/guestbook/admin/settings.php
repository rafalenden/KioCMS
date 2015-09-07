<?php
 // KioCMS - Kiofol Content Managment System
// modules/guestbook/admin/settings.php

$note = new Notifier();
$err = new Errors();

$save = $_POST['save'] ? true : false;
$form = $save ? $_POST['form'] : $guestbook;
$form['bbcode'] = ($save ? $_POST['bbcode'] : $guestbook['bbcode']) ? 1 : 0;
$form['allow_signatures'] = ($save ? $_POST['allow_signatures'] : $guestbook['allow_signatures']) ? 1 : 0;
$form['blocks'] = $save ? array_diff($blocks, $_POST['blocks']) : ($guestbook['blocks'] ? explode(',', trim($guestbook['blocks'])) : '');

if ($save)
{
	// Errors
	$err->message_max_empty(t('ERROR_MESSAGE_MAX_EMPTY'), !$form['message_max'])
	    ->limit_empty(t('ERROR_ERROR_LIMIT_EMPTY'), !$form['limit'])
	    ->order_by_empty(t('ERROR_ORDER_BY_EMPTY'), $form['order_by']);

	if (!$err->count())
	{
		Settings::update('guestbook');
		Cache::clear('contact.txt');
		$note->success(t('SAVED_SUCCESSFUL'));
		redirect(HREF.'admin/modules/guestbook/settings');
	}
	else
		$note->error($err);
}
else
	$note->error(array(t('MODULE_SETTINGS'), t('REQUIRED_FIELDS')));

$tpl = new PHPTAL('modules/guestbook/admin/settings.html');
$tpl->form = $form;
$tpl->note = $note;
$tpl->columns = Settings::formColumns();
$tpl->blocks = Settings::formBlocks();
echo $tpl->execute();