<?php
 // KioCMS - Kiofol Content Managment System
// modules/gallery/image.php

$image = $sql->getCache('gallery_image_'.u2);

if (!$image)
{
	$cache = false;
	$image = $sql->query('
		SELECT a.id AS a_id, a.name a_name, a.description a_description, a.permit, a.images, i.*, u.nickname, u.group_id
		FROM '.DB_PREFIX.'gallery_images i
		LEFT JOIN '.DB_PREFIX.'gallery_albums a ON a.id = i.album_id
		LEFT JOIN '.DB_PREFIX.'users u ON u.id = i.author_id
		WHERE i.id = '.u2)->fetch();
}
else
	$cache = true;

if ($image)
{
	$kio->addPath($image['a_name'], 'gallery/album/'.$image['a_id'].'/'.clean_url($image['a_name']))
		->addPath($image['name'], 'gallery/image/'.$image['id'].'/'.clean_url($image['name']));

	$module->subcodename = 'image';

	if (!$cache)
	{
		$image['counter'] = 0;
		$image['thumbs'] = array();

		$query = $sql->query('
			SELECT *
			FROM '.DB_PREFIX.'gallery_images
			WHERE album_id = '.(int)$image['album_id']);

		while ($row = $query->fetch())
		{
			$image['counter']++;
			$y[] = $row['id'].'/'.clean_url($row['name']);
			if ($row['id'] == $image['id'])
				$image['current'] = $image['counter'];
			$image['thumbs'][] = $row;
		}

		if ($image['author_id'])
			$image['author'] = User::format($image['author_id'], $image['nickname'], $image['group_id']);

		// TODO: http://www.pixastic.com/lib/
		$image['src'] = 'modules/gallery/images/'.$image['id'].'.'.$image['file_extension'];
		//list($image['width'], $image['height']) = getimagesize(ROOT.$image['src']);
		$image['prev'] = $y[$image['current'] - 2];
		$image['next'] = $y[$image['current']];

		$sql->putCacheContent('gallery_image_'.u2, $image);
	}

	if ($image['description'])
		$kio->description = $image['name'].' - '.$image['description'];

	// http://localhost/~kiocms/?images/gallery/15/5-5-0-0-0-0-0-0-0-0-0/biba.jpg

	try
	{
		$tpl = new PHPTAL('modules/gallery/image.html');
		$tpl->cfg = $cfg;
		$tpl->image = $image;
		$tpl->thumbs = $image['thumbs'];
		$tpl->comments = '';
		$tpl->comments = $plug->comments($image['id'], 'gallery_images', $image['comments'], 'gallery/image/'.$image['id'].'/'.clean_url($image['name']));
		echo $tpl->execute();
	}
	catch (Exception $e)
	{
		echo template_error($e);
	}
	
}
else
	echo not_found(sprintf('Zdjęcie o numerze <strong>%s</strong> nie istnieje', u2), array('Zdjęcie zostało usunięte z bazy danych', 'Wprowadzony adres jest nieprawidłowy'));