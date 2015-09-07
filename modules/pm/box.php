<?php
 // KioCMS - Kiofol Content Managment System
// modules/pm/box.php

$kio->addPath(t(ucfirst(u1)), 'pm/'.u1);
$module->subcodename = 'box';

$pager = new Pager('pm/'.u1, $user->{'pm_'.u1}, $cfg->pm['limit']);
$pager->sort(array(
	t('Subject') => 'subject',
	t('Message') => 'message',
	u1 == 'outbox' ? t('To') : t('From') => 'nickname',
	t('Sent') => 'sent'), 'sent', 'asc');

// Reset new messages counter
if ($user->pm_new)
	$sql->exec('UPDATE '.DB_PREFIX.'users SET pm_new = 0 WHERE id = '.$user->id);

switch ($_POST['action'])
{
	// Delete messages
	case 'delete': $delete = true; break;
	// Mark messages as read
	case 'read': $set = 'is_read = 1'; break;
	// Mark messages as unread
	case 'unread': $set = 'is_read = 0';
}

if ($set || ($delete && is_array($_POST['messages'])))
{
	if ($set)
		$sql->exec('UPDATE '.DB_PREFIX.'pm SET '.$set);
	else
	{
		$sql->exec('
			DELETE FROM '.DB_PREFIX.'pm
			WHERE id IN('.implode(', ', array_map('intval', $_POST['messages'])).')
				AND folder = '.$folder.'
				AND owner_id = '.$user->id);
	}

	redirect(HREF.PATH);
}

$stmt = $sql->query('
	SELECT pm.*, u.nickname, u.group_id
	FROM '.DB_PREFIX.'pm pm
	LEFT JOIN '.DB_PREFIX.'users u ON u.id = pm.connector_id
	WHERE pm.owner_id = '.$user->id.' AND pm.folder = '.$folder.'
	ORDER BY '.$pager->order.'
	LIMIT '.$pager->limit.'
	OFFSET '.$pager->offset);

if ($stmt->rowCount())
{
	while ($row = $stmt->fetch())
	{
		if ($row['connector_id'])
			$row['nickname'] = User::format($row['connector_id'], $row['nickname'], $row['group_id']);
		
		$messages[] = $row;
	}

	try
	{
		$tpl = new PHPTAL('modules/pm/box.html');
		$tpl->cfg = $cfg;
		$tpl->messages = $messages;
		$tpl->sort = $pager->sorters;
		$tpl->total = $user->{'pm_'.u1};
		$tpl->kio = $kio;
		$tpl->max = $cfg->pm[u1.'_max'];
		$tpl->note = $note;
		$tpl->pager = $pager;
		$tpl->pagination = $pager->getLinks();
		echo $tpl->execute();
	}
	catch (Exception $e)
	{
		echo template_error($e);
	}
}
else
	echo $note->info('There are no messages in the box.');