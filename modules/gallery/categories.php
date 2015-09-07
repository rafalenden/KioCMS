<?php
 // KioCMS - Kiofol Content Managment System
// modules/gallery/categories.php

if (u1 == 'newest')
{
	$order_by = 'added';
	$kio->addPath(t('Newest'), 'gallery/newest');
}
else
{
	$order_by = 'views';
	$kio->addPath(t('Popular'), 'gallery/popular');
}

$module->name = t('Gallery');
$pager = new Pager('gallery/'.u1, $kio->stats['gallery_images'], $cfg->gallery['limit']);

//$query = $sql->setCache('gallery_'.u1.'_'.$pager->current)->query('
$query = $sql->query('
	SELECT id, name, description, added, views, rating, comments, file_extension
	FROM '.DB_PREFIX.'gallery_images
	ORDER BY '.$order_by.' DESC
	LIMIT '.$pager->limit.'
	OFFSET '.$pager->offset);

while ($row = $query->fetch())
{
	if ($row['author_id'])
		$row['author'] = User::format($row['author_id'], $row['nickname'], $row['name']);
	$images[] = $row;
}

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