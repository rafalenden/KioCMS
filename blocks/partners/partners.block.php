<?php

// KioCMS - Kiofol Content Managment System
// blocks/partners/partners.block.php

class Partners extends Block
{

	public function getContent()
	{
		global $sql;

		$stmt = $sql->setCache('partners')->query('
			SELECT name, type, url, src
			FROM '.DB_PREFIX.'partners
			WHERE type != 0
			ORDER BY display_order ASC');

		if ($stmt)
		{
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				switch ($row['type'])
				{
					case 1:
						$this->content .= '<a href="'.$row['url'].'">'.$row['name'].'</a><br />';
						break;
					case 2:
						$this->content .= '<a href="'.$row['url'].'" title="'.$row['name'].'"><img src="'.$row['src'].'" alt="'.$row['name'].'" /></a><br />';
						break;
					case 3:
						//<embed height="22" width="11" type="application/x-shockwave-flash" src="/WYSIWYG/spaw2%20light/empty/asxas"></embed>
						//<object><a>LDkswmd</a></object>
						$row['flash'] = parse_ini($row['src']); // unserialize
						$this->content .= '<a href="'.$row['url'].'"><object type="application/x-shockwave-flash" data="'.$row['flash']['data'].'" width="'.$row['flash']['width'].'" height="'.$row['flash']['height'].'">'.$row['name'].'</object></a><br />';
						break;
				}
			}
		}
		else
		{
			$this->content = t('There is no content to display.');
		}

		return $this->content;
	}
}