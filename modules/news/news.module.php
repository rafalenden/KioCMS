<?php

// KioCMS - Kiofol Content Managment System
// modules/news/index.php

class News extends Module
{
	public $codename = 'news';

	function __construct()
	{
		Kio::addTitle(t('News'));
		Kio::addBreadcrumb(t('News'), 'news');
		Kio::addCssFile('modules/news/news.css');
	}

	public function getContent()
	{
		// Read more
		if (u1 == 'read' && ctype_digit(u2))
		{
			return $this->getFullEntry();
		}

		// News entries
		else
		{
			return $this->getEntries();
		}
	}

	private function getFullEntry()
	{
		global $sql, $plug;

		$stmt = $sql->setCache('news_read_'.u2)->query('
			SELECT u.nickname, u.group_id, c.id c_id, c.name c_name, c.description c_description, n.*
			FROM '.DB_PREFIX.'news n
			LEFT JOIN '.DB_PREFIX.'users AS u ON u.id = n.author_id
			LEFT JOIN '.DB_PREFIX.'news_categories c ON c.id = n.category_id
			WHERE n.id = '.u2);

		if ($entry = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			if ($entry['description'])
			{
				Kio::setDescription($entry['description']);
			}
			if ($entry['keywords'])
			{
				Kio::setKeywords($entry['keywords']);
			}
			if ($entry['c_name'])
			{
				Kio::addTitle($entry['c_name']);
				Kio::addBreadcrumb($entry['c_name'], 'news/category/'.$entry['c_id'].'/'.clean_url($entry['c_name']));
			}
			if ($entry['author_id'])
			{
				$entry['author'] = User::format($entry['author_id'], $entry['nickname'], $entry['group_id']);
			}

			$entry['url'] = 'news/read/'.u2.'/'.($entry['c_name'] ? clean_url($entry['c_name']).'/' : '').clean_url($entry['title']);

			$this->subcodename = 'read';

			Kio::addTitle($entry['title']);
			Kio::addBreadcrumb($entry['title'], $entry['url']);

			if (Plugin::exists('comments'))
			{
				 require_once ROOT.'plugins/comments/comments.plugin.php';
				$comments = new Comments(u2, 'news', $entry['comments'], $entry['url']);
				$comments = $comments->getContent();
			}
			else
			{
				$comments = '';
			}

			try
			{
				$tpl = new PHPTAL('modules/news/read.tpl.html');
				$tpl->entry = $entry;
				$tpl->comments = $comments;
				return $tpl->execute();
			}
			catch (Exception $e)
			{
				return template_error($e);
			}
		}
		else
		{
			not_found(t('Selected entry number does not exists.'));
		}
	}

	private function getEntries()
	{
		global $sql;

		$pager_url = 'news';
		$category_id = 0;

		if (u1 == 'category')
		{
			$category_id = (int)u2;
		}

		$total = Kio::getStat('entries', 'news');

		if ($category_id)
		{
			$category = $sql->setCache('news_categories_'.$category_id)->query('
				SELECT id, name, description, entries
				FROM '.DB_PREFIX.'news_categories
				WHERE id = '.$category_id)->fetch(PDO::FETCH_ASSOC);

			if ($category)
			{
				$total = $category['entries'];

				if ($category['description'])
				{
					Kio::setDescription($category['name'].' - '.$category['description']);
				}
				Kio::addTitle($category['name']);
				Kio::addBreadcrumb($category['name'], 'news/category/'.$category_id.'/'.clean_url($category['name']));

				$pager_url = 'news/category/'.$category_id.'/'.clean_url($category['name']);
			}
			else
			{
				return not_found(t('Selected category does not exists.'), array(
					t('Category was moved or deleted.'),
					t('Entered URL is invalid.')));
			}
		}

		if (!empty($category) || empty($category))
		{
			$this->subcodename = 'entries';
			$pager = new Pager($pager_url, $total, Kio::getConfig('limit', 'news'));

			$stmt = $sql->setCache('news_'.$category_id.'_'.$pager->current)->query('
				SELECT u.nickname, u.group_id, c.id c_id, c.name c_name, c.description c_description, n.*
				FROM '.DB_PREFIX.'news n
				LEFT JOIN '.DB_PREFIX.'users u ON u.id = n.author_id
				LEFT JOIN '.DB_PREFIX.'news_categories c ON c.id = n.category_id
				WHERE '.($category_id ? 'c.id = '.$category_id.'
					AND ' : '').(LOGGED ? 'n.publication > 0' : 'n.publication = 1').'
					AND n.added < '.TIMESTAMP.'
				ORDER BY '.Kio::getConfig('order_by', 'news').'
				LIMIT '.$pager->limit.'
				OFFSET '.$pager->offset);

			while ($row = $stmt->fetch())
			{
				if ($row['author_id'])
				{
					$row['author'] = User::format($row['author_id'], $row['nickname'], $row['group_id']);
				}
				$row['url_title'] = ($row['c_name'] ? clean_url($row['c_name']).'/' : '').clean_url($row['title']);
				$row['content'] = parse($row['content'], Kio::getConfig('parsers', 'news'));
				$entries[] = $row;
			}

			try
			{
				$tpl = new PHPTAL('modules/news/news.tpl.html');
				$tpl->entries = $entries;
				$tpl->pagination = $pager->getLinks();
				return $tpl->execute();
			}
			catch (Exception $e)
			{
				return template_error($e);
			}
		}
	}
}
