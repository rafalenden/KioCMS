<?php

// KioCMS - Kiofol Content Managment System
// modules/users/index.php

class Users extends Module
{
	public $name = 'Users';
	public $codename = 'users';

	public function __construct()
	{
		$this->name = t($this->name);

		Kio::addTitle($this->name);
		Kio::addBreadcrumb($this->name, $this->codename);

		$this->blocks = array(
			'left' => array('user_panel', 'partners', 'news_categories', 'shoutbox'));
	}

	public function getContent()
	{
		global $sql;

		$pager = new Pager('users', Kio::getStat('total', 'users'), Kio::getConfig('limit', 'users'));
		$pager->sort(
			array(
				t('Nickname') => 'nickname',
				t('Group') => 'g_name',
				t('Gender') => 'gender',
				t('Title') => 'title',
				//t('E-mail') => 'email',
				//$cfg->system['communicator_name'] => 'communicator',
				t('Location') => 'locality',
				t('Country') => 'country',
				t('Registered') => 'registered'),
			// Defaults
			'registered', 'asc');

		$query = $sql->query('
			SELECT id, name, inline, members
			FROM ' . DB_PREFIX . 'groups
			ORDER BY display_order');

		while ($row = $query->fetch())
		{
			if ($row['inline'])
			{
				$row['name'] = sprintf($row['inline'], $row['name']);
			}

			$groups[] = $row;
		}

		$query = $sql->query('
			SELECT u.id, u.nickname, u.email, u.registered, u.group_id, u.gender, u.locality, u.country, u.communicator, u.title, g.name g_name
			FROM ' . DB_PREFIX . 'users u
			LEFT JOIN ' . DB_PREFIX . 'groups g ON g.id = u.group_id
			ORDER BY ' . $pager->orderBy . '
			LIMIT ' . $pager->limit . '
			OFFSET ' . $pager->offset);

		while ($row = $query->fetch())
		{
			$row['nickname'] = User::format($row['id'], $row['nickname'], $row['group_id']);

			switch ($row['gender'])
			{
				case 1:
					$row['gender'] = ' <img class="gender" src="' . LOCAL . 'themes/' . THEME . '/images/male.png" alt="' . t('Male') . '" title="' . t('Male') . '" />';
					break;
				case 2:
					$row['gender'] = ' <img class="gender" src="' . LOCAL . 'themes/' . THEME . '/images/female.png" alt="' . t('Female') . '" title="' . t('Female') . '" />';
					break;
				default:
					$row['gender'] = '';
			}

			$users[] = $row;
		}

		try
		{
			$tpl = new PHPTAL('modules/users/users.tpl.html');
			$tpl->sort = $pager->sorters;
			$tpl->users = $users;
			$tpl->groups = $groups;
			$tpl->pagination = $pager->getLinks();
			return $tpl->execute();
		}
		catch (Exception $e)
		{
			return template_error($e);
		}
	}
}