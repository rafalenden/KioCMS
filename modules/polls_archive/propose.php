<?php
 // KioCMS - Kiofol Content Managment System
// blocks/poll/propose.php

$poll['subcodename'] = 'propose';
if ($_POST['add_propose-poll'])
{
	if ($_POST['add_propose-poll'] && !$_POST['propose_text-poll'])
		$poll['error'] = true;
	else
	{
		$poll['error'] = false;
		$sql->exec('
			INSERT INTO '.DB_PREFIX.'poll_proposes (title, user_id, user_ip, sent)
			VALUES ("'.filter($_POST['propose_text-poll'], 100).'", '.$user->id.', "'.$_SERVER['REMOTE_ADDR'].'", '.TIMESTAMP.')');
		redirect(HREF.PATH.'#poll');
	}
}
$tpl = new PHPTAL('blocks/poll/propose.html');
$tpl->poll = $poll;