<?php
 // KioCMS - Kiofol Content Managment System (cms.kiofol.com)
// blocks/newsletter/action.php

$form['email'] = filter($_POST['email-newsletter'], 100);

switch (true)
{
	// Empty e-mail field
	case !$form['email']:
		$err->email_empty = t('Field e-mail can not be empty.');
		break;
	// Invalid e-mail
	case !is_email($form['email']):
		$err->email_invalid = t('The e-mail address you entered is invalid.');
		break;
	// No errors
	default:
		$code = $sql->query('SELECT code FROM '.DB_PREFIX.'newsletter WHERE email = "'.$form['email'].'"')->fetchColumn();
		$err->email_exists_or_not = $code
			// Adding - E-mail exists
			? ($_POST['add-newsletter'] ? t('Entered <strong>e-mail</strong> is already signed.') : '')
			// Deleting - E-mail not exists
			: ($_POST['delete-newsletter'] || $_POST['delete2-newsletter'] ? t('Entered <strong>e-mail</strong> is not signed.') : '');
		break;
}

if (!$err->count() && CORRECT_AUTH)
{
	// When adding
	if ($_POST['add-newsletter'])
	{
		$sql->exec('
			INSERT INTO '.DB_PREFIX.'newsletter (email, code, time, user_ip)
			VALUES(
				"'.$form['email'].'",
				'.rand(0, 9999).',
				'.TIMESTAMP.',
				"'.IP.'")');
		$note->success(t('<strong>E-mail</strong> address was successfully added.'));
		redirect(HREF.PATH.'#newsletter');
		// mail ($youremail,"Nowy użytkownik newslettera - $email.",$email."\nFrom: $mail_newsletter\nReply-To: $email\n");
	}
	// When deleting
	elseif ($_POST['delete-newsletter'] || $_POST['delete2-newsletter'])
	{
		$tpl = 'blocks/newsletter/enter_code_form.html';
		$form['code'] = $_POST['code-newsletter'];

		// Enter code
		if (!$_POST['delete2-newsletter'])
		{
			// mail ($youremail,"Nowy użytkownik newslettera - $email.",$email."\nFrom: $mail_newsletter\nReply-To: $email\n");
			$note->info(t('Check your e-mail box and type out <strong>code</strong>.'));
		}
		// Code entered
		elseif ($_POST['delete2-newsletter'])
		{
			// Empty code field
			if (!$form['code'])
				$err->code_empty = t('Field <strong>code</strong> can not be empty.');
			// Code is invalid
			elseif ($form['code'] != $code)
				$err->code_invalid = t('Entered <strong>code</strong> is incorrect.');

			// No errors
			if (!$err->count())
			{
				$sql->exec('DELETE FROM '.DB_PREFIX.'newsletter WHERE email = "'.$form['email'].'" AND code = '.$form['code']);
				$note->success(t('<strong>E-mail</strong> address was successfully deleted.'));
				redirect(HREF.PATH.'#newsletter');
			}
			else
				$note->error($err);
		}
	}
}
else
	$note->error($err);