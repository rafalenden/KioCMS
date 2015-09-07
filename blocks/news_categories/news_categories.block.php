<?php

// KioCMS - Kiofol Content Managment System
// blocks/news_categories/news_categories.block.php

class News_Categories extends Block
{

	public function getContent()
	{
		global $sql;

		$stmt = $sql->setCache('news_categories')->query('
			SELECT *
			FROM '.DB_PREFIX.'news_categories
			ORDER BY name
			LIMIT 10');

		if ($stmt)
		{
			$items = array();

			while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$items[$row['name'].' ('.$row['entries'].')'] = array(
					HREF.'news/category/'.$row['id'].'/'.clean_url($row['name']),
					$row['description'] ? $row['description'] : t('Entries marked as %category', array('%category' => $row['name'])));
			}

			return $this->content = items($items);
		}
		else
		{
			return $this->content = t('There is no content to display.');
		}
	}
}