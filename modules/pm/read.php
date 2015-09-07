<?php
 // KioCMS - Kiofol Content Managment System
// modules/pm/read.php

$kio->addPath(t(ucfirst(u1)), 'pm/'.u1);

// Get message content
$message = $sql->query('
	SELECT pm.*, u.nickname, u.group_id, u.avatar
	FROM '.DB_PREFIX.'pm pm
	LEFT JOIN '.DB_PREFIX.'users u ON u.id = pm.connector_id
	WHERE pm.id = '.(int)u3.' AND pm.owner_id = '.$user->id)->fetch(PDO::FETCH_ASSOC);

// Message exists
if ($message)
{
	$module->subcodename = 'read';
	$kio->addPath($message['subject'],'pm/'.u1.'/read/'.u3);

	// Sender/Recipient has id (is registered)
	if ($message['connector_id'])
		$message['nickname'] = User::format($message['connector_id'], $message['nickname'], $message['group_id']);

	// Message is unread
	if (!$message['is_read'])
		$sql->exec('UPDATE '.DB_PREFIX.'pm SET is_read = 1 WHERE id = "'.(int)$message['id'].'"');

	try
	{
		$tpl = new PHPTAL('modules/pm/read.html');
		$tpl->message = $message;
		$tpl->user = $user;
		$tpl->kio = $kio;
		echo $tpl->execute();
	}
	catch (Exception $e)
	{
		echo template_error($e);
	}
}
else
	echo not_found();