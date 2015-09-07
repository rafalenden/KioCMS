<?php
 // KioCMS - Kiofol Content Managment System
// modules/gallery/album.php

$album = $sql->setCache('gallery_album_'.u2)->query('
	SELECT id, name, description, permit, images
	FROM '.DB_PREFIX.'gallery_albums
	WHERE id = '.u2)->fetch();

if ($album)
{
	$kio->addPath($album['name'], 'gallery/album/'.$album['id'].'/'.clean_url($album['name']));
	$kio->description = $album['description']
		? $album['name'].' - '.$album['description']
		: $cfg->system['description'];

	$pager = new Pager(
		'gallery/album/'.$album['id'].'/'.clean_url($album['name']),
		$album['images'],
		$cfg->gallery['limit']);

	if ($album['images'])
	{
		$query = $sql->setCache('gallery_album_'.$album['id'].'_images_'.$pager->current)->query('
			SELECT id, name, description, added, views, rating, comments, file_extension
			FROM '.DB_PREFIX.'gallery_images
			WHERE album_id = '.$album['id'].'
			ORDER BY added DESC
			LIMIT '.$pager->limit.'
			OFFSET '.$pager->offset);

		while ($row = $query->fetch())
		{
			if ($row['author_id'])
				$row['author'] = User::format($row['author_id'], $row['nickname'], $row['name'], $row['inline']);
			$images[] = $row;
		}
	}
	else
		$note->info(t('This album is empty.'));

	try
	{
		$tpl = new PHPTAL('modules/gallery/thumbnails.html');
		$tpl->cfg = $cfg;
		$tpl->note = $note;
		$tpl->images = $images;
		$tpl->album = $album;
		$tpl->pagination = $pager->getLinks();
		echo $tpl->execute();
	}
	catch (Exception $e)
	{
		echo template_error($e);
	}
}
else
	echo not_found(sprintf('Album o numerze <strong>%s</strong> nie istnieje.', u2));