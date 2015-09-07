<?php
 // KioCMS - Kiofol Content Managment System
// modules/pm/write.php

$kio->addPath(t('Send new'), 'pm/write');
$err = new Error();

if ((u2 == 'resend' || u2 == 'reply') && ctype_digit(u3))
{
	if (u2 == 'reply')
	{
		$message = $sql->query('
			SELECT connector_id, subject
			FROM '.DB_PREFIX.'pm
			WHERE id = '.u3.' AND folder != 1')->fetch(PDO::FETCH_ASSOC);

		$form['subject'] = 'Re: '.$message['subject'];
	}
	else
	{
		$message = $sql->query('
			SELECT connector_id, subject, message
			FROM '.DB_PREFIX.'pm
			WHERE id = '.(int)u3.' AND folder = 1')->fetch(PDO::FETCH_ASSOC);

		$form['subject'] = $message['subject'];
		$form['message'] = $message['message'];
	}

	$form['receiver'] = User::getNickname(BY_ID, $message['connector_id']);
}
elseif (ctype_digit(u2))
	$form['receiver'] = User::getNickname(BY_ID, u2);

if ($_POST['send'])
{
	// Form values
	$form = array(
		'receiver'  => filter($_POST['receiver'], 100),
		'subject'   => filter($_POST['subject'], 100),
		'save'      => $_POST['save'],
		'bbcode'    => $_POST['bbcode'] ? BBCODE : 0,
		'emoticons' => $_POST['emoticons'] ? EMOTICONS : 0,
		'autolinks' => $_POST['autolinks'] ? AUTOLINKS : 0,
		'message'   => filter($_POST['message'], 250));

	$err->receiver_empty(t('ERROR_RECEIVER_EMPTY'), !$form['receiver']);
	$err->receiver_not_exists(t('ERROR_RECEIVER_NOT_EXISTS'), $form['receiver'] && !User::getId(BY_NICKNAME, $form['receiver']));
	$err->subject_empty(t('ERROR_SUBJECT_EMPTY'), !$form['subject']);
	$err->message_empty(t('ERROR_MESSAGE_EMPTY'), !$form['message']);

	// No errors
	if (!$err->count())
	{
		$form['receiver'] = User::getId(BY_NICKNAME, $form['receiver']);
		$form['message'] = cut($form['message'], $cfg->pm['message_max']);
		$form['parsers'] = $form['bbcode'].$form['autolinks'].$form['emoticons'].CENSURE.PRE;

		$stmt = $sql->prepare('
			INSERT INTO '.DB_PREFIX.'pm
				(sent, owner_id, connector_id, subject, message, folder, is_read, parsers)
			VALUES
				(:sent, :owner_id, :connector_id, :subject, :message, :folder, :is_read, :parsers)'.
				($form['save'] ? ', (:sent, :owner_id, :connector_id, :subject, :message, :folder, :is_read, :parsers)' : ''));

		$stmt->execute(array(
			'sent' => TIMESTAMP,
			'owner_id' => $form['receiver'],
			'connector_id' => $user->id,
			'subject' => $form['subject'],
			'message' => $form['message'],
			'folder' => 0,
			'is_read' => 0,
			'parsers' => $form['parsers']));

		setcookie('KioCMS-'.COOKIE.'-pm', 'true', TIMESTAMP + $cfg->pm['flood_interval'] + 1, '/');
		$note->success('Wiadomość została wysłana.');
		redirect(HREF.'pm/inbox');
	}
	// Show errors
	else
		$note->error($err);
}
else
	$note->info(array(t('WELCOME_MESSAGE'), t('REQUIRED')));


try
{
	$tpl = new PHPTAL('modules/pm/write.html');
	$tpl->cfg = $cfg;
	$tpl->err = $err->toArray();
	$tpl->form = $form;
	$tpl->user = $user;
	$tpl->note = $note;
	echo $tpl->execute();
}
catch (Exception $e)
{
	echo template_error($e);
}