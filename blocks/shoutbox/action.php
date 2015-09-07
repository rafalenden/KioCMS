<?php
 // KioCMS - Kiofol Content Managment System
// blocks/shoutbox/action.php

$form['author'] = LOGGED ? $user->nickname : filter($_POST['author-shoutbox'], 100);
$form['message'] = $_POST['message-shoutbox'] ? filter($_POST['message-shoutbox'], $cfg->shoutbox['message_max']) : '';

$err->author_empty(t('Field <strong>author</strong> can not be empty.'), !$form['author']);
$err->author_exists(t('Entered <strong>nickname</strong> is registered.'), !LOGGED && is_registered($form['author']));
$err->message_empty(t('Field <strong>message</strong> can not be empty.'), !$form['message']);

// No errors
if (!$err->count())
{
	$sql->exec('
		INSERT INTO '.DB_PREFIX.'shoutbox (added, author, message, author_id, author_ip)
		VALUES (
			'.TIMESTAMP.',
			"'.$form['author'].'",
			"'.cut($form['message'], $cfg->shoutbox['message_max']).'",
			'.$user->id.',
			"'.IP.'")', 'shoutbox.txt');
	$note->success(t('Entry was added successfully.'));
	redirect(HREF.PATH.'#shoutbox');
}
// Show errors
else
	$note->error($err);