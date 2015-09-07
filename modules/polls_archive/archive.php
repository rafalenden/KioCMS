<?php
 // KioCMS - Kiofol Content Managment System
// blocks/poll/archive.php

if ($_POST['archive-poll'] || ($_POST['show_archived-poll'] && !$_POST['option_archive-poll']))
{
	$query = sql_query('SELECT id, created, title, votes FROM '.DB_PREFIX.'poll_topics WHERE active = 0 ORDER BY created LIMIT 5');
	if (sql_num_rows($query))
	{
		$poll['subcodename'] = 'archive';
		while ($row = sql_fetch_assoc($query))
			$archived_polls[] = $row;

		$tpl = new PHPTAL('blocks/poll/archive.html');
		$tpl->system = $system;
		$tpl->archived_polls = $archived_polls;
	}
	else
	{
		$poll['subcodename'] = 'archive_null';
		$tpl = new PHPTAL('blocks/poll/archive_null');
	}
}
else
{
	$id = (int)$_POST['option_archive-poll'];
	$query = $sql->query('
		SELECT created, title, votes
		FROM '.DB_PREFIX.'poll_topics
		WHERE id = '.$id.' AND active = 0');
	if ($query->rowCount())
	{
		$poll['subcodename'] = 'b-archive_results';
		$topic = $query->fetch(PDO::FETCH_ASSOC);

		$options = array();
		$query = $sql->query('
			SELECT title, votes
			FROM '.DB_PREFIX.'poll_options
			WHERE topic_id = '.$id.'
			ORDER BY votes DESC');
		while ($row = $query->fetch())
		{
			$row['percent'] = @round(100 * ($row['votes'] / $topic['votes']), 1);
			$options[] = $row;
		}

		$tpl = new PHPTAL('blocks/poll/results.html');
		$tpl->archive = true;
		$tpl->poll = $poll;
		$tpl->your_vote = null;
		$tpl->options = $options;
		$tpl->topic = $topic;
		$tpl->system = $system;
	}
	else
	{
		$poll['subcodename'] = 'archive_null';
		$tpl = new PHPTAL('blocks/poll/archive_null');
		$tpl->archive = true;
	}
}