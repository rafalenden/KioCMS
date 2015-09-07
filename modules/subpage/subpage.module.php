<?php

// KioCMS - Kiofol Content Managment System
// modules/example/index.php

class Subpage extends Module
{
	public $codename = 'subpage';
	private $id;

	public function  __construct($page_id)
	{
		$this->id = $page_id;
	}

	public function getContent()
	{
		global $sql;

		// Strona zabezpieczona wykonuje dwa niepotrzebne zapytania, mimo, że tekst sie nie wyświetla, należy po pierwszym zapytaniu wykonać fetch_assoc
		$page = $sql->query('
			SELECT * FROM ' . DB_PREFIX . 'subpages
			WHERE id = ' . $this->id)->fetch();

		// Page does not exist
		if (!$page)
		{
			return not_found('Page you have been loking for does not exists.');
		}
		else if ($page['permit'] == 0)
		{
			return no_access();
		}
		else if (!LOGGED && $page['type'] == 2)
		{
			return no_access(array(
				'Wybrana treść jest dostępna tylko dla zalogowanych osób.',
				t('REGISTER')));
		}
		// Show static page
		else
		{
			Kio::addTitle($page['title']);
			Kio::addBreadcrumb($page['title'], $page['id'] . '/' . clean_url($page['title']));

//			$this->subcodename = $page['number'];

			Kio::addHead($page['head']);

			if ($page['description'])
			{
				Kio::setDescription($page['description']);
			}

			if ($page['keywords'])
			{
				Kio::setKeywords($page['keywords']);
			}

			return eval('?>' . $page['content']);
		}
	}
}
