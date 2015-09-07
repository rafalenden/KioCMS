<?php
 // KioCMS - Kiofol Content Managment System
// blocks/poll/index.php

$lang->poll('blocks/poll/lang.*.ini');
$poll['name'] = t('NAME');
$kio->css[] = 'blocks/poll/poll.css';
$info = new Infobox();

$query = $sql->queryCache('SELECT id, title, votes FROM '.DB_PREFIX.'poll_topics WHERE active = 1', 'poll_topic.txt');
if ($query)
{
	$topic = current($query);
	$vote_id = $sql->query('
		SELECT option_id
		FROM '.DB_PREFIX.'poll_votes
		WHERE topic_id = '.$topic['id'].' AND voter_ip = "'.IP.'"')->fetchColumn();
	$query = $sql->queryCache('
		SELECT id, title, votes
		FROM '.DB_PREFIX.'poll_options
		WHERE topic_id = '.$topic['id'].' ORDER BY votes DESC', 'poll_options.txt');

	// User already voted
	if ($vote_id)
	{
		$options = array();
		$poll['subcodename'] = 'results';

		foreach ($query as $row)
		{
			$row['percent'] = @round(100 * ($row['votes'] / $topic['votes']), 1);
			$options[] = $row;
		}
		$tpl = new PHPTAL('blocks/poll/results.html');
		$tpl->vote_id = $vote_id;

	}
	elseif ($_POST['vote-poll'] && $_POST['option-poll'])
	{
		$option_id = (int)$_POST['option-poll'];
		$sql->exec('
			UPDATE '.DB_PREFIX.'poll_options o, '.DB_PREFIX.'poll_topics t
			SET o.votes = o.votes + 1, t.votes = t.votes + 1
			WHERE o.topic_id = '.$topic['id'].' AND o.id = '.$option_id.' AND t.id = '.$topic['id'].';
			INSERT INTO '.DB_PREFIX.'poll_votes (topic_id, option_id, voter_id, voter_ip, voted)
			VALUES ('.$topic['id'].', '.$option_id.', '.$user->id.', "'.IP.'", '.TIMESTAMP.')', 'poll_*.txt');
		redirect(PATH.'#poll');
	}
	else
	{
		$poll['subcodename'] = 'voting';
		$options = $query;
		$tpl = new PHPTAL('blocks/poll/voting.html');
	}

	$tpl->poll = $poll;
	$tpl->topic = $topic;
	$tpl->options = $options;
	$tpl->lang = $lang;
	echo $tpl->execute();
}
else
	echo $lang->poll['NULL'];