<?php

// KioCMS - Kiofol Content Managment System
// modules/contact/index.php

class Contact extends Module
{
	public $note;
	public $codename = 'contact';

	function __construct()
	{
		Kio::addTitle(t('Contact'));
		Kio::addBreadcrumb(t('Contact'), 'contact');

		$this->note = new Notifier();
	}

	public function getContent()
	{
		global $sql;

		$err = new Error();
		$form = array();

		if (Kio::getConfig('informations', 'contact'))
		{
			$info = Notifier::factory('note-contact_info')
				->info(parse(Kio::getConfig('informations', 'contact'), BBCODE.AUTOLINKS.EMOTICONS.CENSURE.PRE));
		}

		if (isset($_POST['send']))
		{
			// Form values
			$form = array(
				'receiver' => filter($_POST['receiver'], 100),
				'sender' => LOGGED ? User::$nickname : filter($_POST['sender'], 100),
				'email' => LOGGED ? User::$email : filter($_POST['email'], 100),
//				'phone' => filter($_POST['phone'], 100),
				'subject' => filter($_POST['subject'], 100),
				'message' => filter($_POST['message'], 250));

			if (!empty($_COOKIE[COOKIE.'-flood-contact']) && Kio::getConfig('flood_interval'))
			{
				$err->setError('flood', t('ERROR_FLOOD'));
			}
			else
			{
				// Errors
				if (!LOGGED)
				{
					$err->setError('sender_empty', t('Sender field is required.'))
						->condition(!$form['sender']);
					$err->setError('sender_exists', t('ERROR_SENDER_EXISTS'))
						->condition(is_registered($form['sender'], 'nickname'));
					$err->setError('email_empty', t('E-mail address field is required.'))
						->condition(!$form['email']);
					$err->setError('email_invalid', t('ERROR_EMAIL_INVALID'))
						->condition($form['email'] && !is_email($form['email']));
				}

//				$err->setError('phone_invalid', t('ERROR_PHONE_INVALID'))
//					->condition($form['phone'] && !preg_match('#^[0-9 ()+-]+$#', $form['phone']));
				$err->setError('subject_empty', t('Subject field is required.'))
					->condition(!$form['subject']);
				$err->setError('message_empty', t('Message field is required.'))
					->condition(!$form['message']);
			}

			if ($err->noErrors())
			{
				$from = "From: $form[email]2";
				$msg = "Imię: $imie\nE-Mail: $form[email]2\nTelefon: $telefon\n\nTreść wiadomości:\n$form[message]\n\n\n----\nWiadomość została wysłana ze strony $adres\nIP: $ip";
				echo mail($form['email'], $temat, $msg, $from) ? $note->success(t('SUCCESS')).redirect() : $note->error(t('Wystąpił błąd, spróbuj wysłać później'));

				if (Kio::getConfig('flood_interval'))
				{
					setcookie(COOKIE.'-contact', 'true', TIMESTAMP + Kio::getConfig('flood_interval') + 1, '/');
				}

				$to = "someone@example.com";
				$subject = "Test mail";
				$message = "Hello! This is a simple email message.";
				$from = "someonelse@example.com";
				$headers = "From: $from";
				mail($to, $subject, $message, $headers);
			}
			else
			{
				$this->note->error($err->toArray());
			}
		}

		$stmt = $sql->setCache('contact')->prepare('
			SELECT id, nickname, group_id
			FROM '.DB_PREFIX.'users
			WHERE id IN (:receivers)');
		$stmt->bindParam(':receivers', Kio::getConfig('receivers', 'contact'));
		$stmt->execute();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$row['g_name'] = Kio::getGroup($row['group_id'], 'name');
			$receivers[] = $row;
		}

		try
		{
			$tpl = new PHPTAL('modules/contact/contact.tpl.html');
			$tpl->message_limit = Kio::getConfig('message_max', 'contact');
			$tpl->form = $form;
			$tpl->user = User::toArray();
			$tpl->receivers = $receivers;
			$tpl->err = $err->toArray();
			$tpl->note = $this->note;
			$tpl->info = isset($info) ? $info : '';
			return $tpl->execute();
		}
		catch (Exception $e)
		{
			return template_error($e);
		}
	}
}