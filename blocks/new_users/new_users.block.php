<?php

// KioCMS - Kiofol Content Managment System
// blocks/new_users/index.php

class New_Users extends Block
{

	public function getContent()
	{
		global $sql;

		$stmt = $sql->setCache('new_users')->query('
			SELECT id, nickname, registered
			FROM '.DB_PREFIX.'users
			ORDER BY id DESC
			LIMIT 10');

		if ($stmt)
		{
			$items = array();

			while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$items[$row['nickname']] = array(
					HREF.'profile/'.$row['id'].'/'.clean_url($row['nickname']),
					clock($row['registered']));
			}

			return items($items);
		}
		else
		{
			return t('There is no content to display.');
		}
	}
}