<?php

// KioCMS - Kiofol Content Managment System
// blocks/poll/index.php

class Poll extends Block
{

	public function __construct($attributes = array())
	{
		parent::__construct($attributes);

		Kio::addCssFile('blocks/poll/poll.css');
	}

	public function getContent()
	{
		global $sql;

		$note = new Notifier('note-poll');

		$stmt = $sql->setCache('poll_topic')->query('
			SELECT id, title, votes
			FROM '.DB_PREFIX.'poll_topics
			WHERE active = 1');

		$topic = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($topic)
		{
			$vote_id = $sql->query('
				SELECT option_id
				FROM '.DB_PREFIX.'poll_votes
				WHERE topic_id = '.$topic['id'].' AND voter_ip = "'.IP.'"')->fetchColumn();

			$stmt = $sql->setCache('poll_options')->query('
				SELECT id, title, votes
				FROM '.DB_PREFIX.'poll_options
				WHERE topic_id = '.$topic['id'].' ORDER BY votes DESC');

			// User already voted
			if ($vote_id)
			{
				$options = array();
				$block->subcodename = 'results';

				foreach ($stmt as $row)
				{
					$row['percent'] = @round(100 * ($row['votes'] / $topic['votes']), 1);
					$options[] = $row;
				}
				$tpl = new PHPTAL('blocks/poll/results.html');
				$tpl->vote_id = $vote_id;
			}
			else if ($_POST['vote-poll'] && $_POST['option-poll'])
			{
				$option_id = (int)$_POST['option-poll'];
				$sql->clearCacheGroup('poll_*')->exec('
					UPDATE '.DB_PREFIX.'poll_options o, '.DB_PREFIX.'poll_topics t
					SET o.votes = o.votes + 1, t.votes = t.votes + 1
					WHERE o.topic_id = '.$topic['id'].' AND o.id = '.$option_id.' AND t.id = '.$topic['id'].';
					INSERT INTO '.DB_PREFIX.'poll_votes (topic_id, option_id, voter_id, voter_ip, voted)
					VALUES ('.$topic['id'].', '.$option_id.', '.$user->id.', "'.IP.'", '.TIMESTAMP.')');
				redirect(PATH.'#poll');
			}
			else
			{
				$block->subcodename = 'voting';
				$options = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$tpl = new PHPTAL('blocks/poll/voting.html');
			}

			$stmt->closeCursor();

			$tpl->topic = $topic;
			$tpl->options = $options;
			$tpl->note = $note;
			return $tpl->execute();
		}
		else
		{
			return t('There is no content to display.');
		}
	}
}