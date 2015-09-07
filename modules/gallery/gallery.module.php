<?php

// KioCMS - Kiofol Content Managment System
// modules/gallery/index.php

class Gallery extends Module
{
	public $codename = 'gallery';
	private $note;

	public function __construct()
	{
		Kio::addTitle(t('Gallery'));
		Kio::addBreadcrumb(t('Gallery'), 'gallery');
		Kio::addCssFile('modules/gallery/gallery.css');
	}

	public function getContent()
	{
		global $kio, $sql, $user, $cfg;

		$this->note = new Notifier();

		// Tabs
		Kio::addTabs(array(
				// Name => array(URL, pattern)
				'Albumy' => array('^gallery$', 'gallery'),
				'Najnowsze' => 'gallery/newest',
				'Popularne' => 'gallery/popular'));

		switch (true)
		{
			// Show album
			case u1 == 'album' && ctype_digit(u2):
				return $this->getAlbum();
				break;
			// Show image
			case u1 == 'image' && ctype_digit(u2):
				return $this->getImage();
				break;
			// Show category
			case u1 == 'newest':
			case u1 == 'popular':
				return $this->getCategory(u1);
				break;
			// Albums list
			default:
				return $this->getAlbumList();
		}
	}

	private function getAlbum()
	{
		global $sql;

		$album = $sql->setCache('gallery_album_'.u2)->query('
			SELECT id, name, description, permit, images
			FROM '.DB_PREFIX.'gallery_albums
			WHERE id = '.u2)->fetch();

		if ($album)
		{
			Kio::addTitle($album['name']);
			Kio::addBreadcrumb($album['name'], 'gallery/album/'.$album['id'].'/'.clean_url($album['name']));

			Kio::setDescription($album['description'] ? $album['name'].' - '.$album['description'] : Kio::getConfig('description'));

			$pager = new Pager(
					'gallery/album/'.$album['id'].'/'.clean_url($album['name']),
					$album['images'],
					Kio::getConfig('limit', 'gallert'));

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
					if (!empty($row['author_id']))
					{
						$row['author'] = User::format($row['author_id'], $row['nickname'], $row['name'], $row['inline']);
					}

					$images[] = $row;
				}
			}
			else
			{
				$this->note->info(t('This album is empty.'));
			}

			try
			{
				$tpl = new PHPTAL('modules/gallery/thumbnails.tpl.html');
				$tpl->note = $this->note;
				$tpl->images = $images;
				$tpl->album = $album;
				$tpl->pagination = $pager->getLinks();
				return $tpl->execute();
			}
			catch (Exception $e)
			{
				return template_error($e);
			}
		}
		else
		{
			return not_found(sprintf('Album o numerze <strong>%s</strong> nie istnieje.', u2));
		}
	}

	private function getImage()
	{
		global $sql, $plug;

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
		{
			$cache = true;
		}

		if ($image)
		{
			Kio::addTitle($image['a_name']);
			Kio::addBreadcrumb($image['a_name'], 'gallery/album/'.$image['a_id'].'/'.clean_url($image['a_name']));

			Kio::addTitle($image['name']);
			Kio::addBreadcrumb($image['name'], 'gallery/image/'.$image['id'].'/'.clean_url($image['name']));

			$this->subcodename = 'image';

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
					{
						$image['current'] = $image['counter'];
					}

					$image['thumbs'][] = $row;
				}

				if ($image['author_id'])
				{
					$image['author'] = User::format($image['author_id'], $image['nickname'], $image['group_id']);
				}

				// TODO: http://www.pixastic.com/lib/
				$image['src'] = 'modules/gallery/images/'.$image['id'].'.'.$image['file_extension'];
				//list($image['width'], $image['height']) = getimagesize(ROOT.$image['src']);
				$image['prev'] = $y[$image['current'] - 2];
				$image['next'] = $y[$image['current']];

				$sql->putCacheContent('gallery_image_'.u2, $image);
			}

			if ($image['description'])
			{
				Kio::setDescription($image['name'].' - '.$image['description']);
			}

			// http://localhost/~kiocms/?images/gallery/15/5-5-0-0-0-0-0-0-0-0-0/biba.jpg

			try
			{
				$tpl = new PHPTAL('modules/gallery/image.tpl.html');
				$tpl->image = $image;
				$tpl->thumbs = $image['thumbs'];
				$tpl->comments = '';
				$tpl->comments = $plug->comments($image['id'], 'gallery_images', $image['comments'], 'gallery/image/'.$image['id'].'/'.clean_url($image['name']));
				return $tpl->execute();
			}
			catch (Exception $e)
			{
				return template_error($e);
			}
		}
		else
		{
			return not_found(sprintf('Zdjęcie o numerze <strong>%s</strong> nie istnieje', u2),
				array('Zdjęcie zostało usunięte z bazy danych', 'Wprowadzony adres jest nieprawidłowy'));
		}
	}

	private function getCategory()
	{
		global $sql, $plug;

		if (u1 == 'newest')
		{
			$order_by = 'added';

			Kio::addTitle(t('Newest'));
			Kio::addBreadcrumb(t('Newest'), 'gallery/newest');
		}
		else
		{
			$order_by = 'views';

			Kio::addTitle(t('Popular'));
			Kio::addBreadcrumb(t('Popular'), 'gallery/popular');
		}

		$this->name = t('Gallery');
		$pager = new Pager('gallery/'.u1, Kio::getStat('images', 'gallery'), Kio::getConfig('limit', 'gallery'));

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
			{
				$row['author'] = User::format($row['author_id'], $row['nickname'], $row['name']);
			}
			$images[] = $row;
		}

		try
		{
			$tpl = new PHPTAL('modules/gallery/thumbnails.tpl.html');
			$tpl->note = $note;
			$tpl->images = $images;
			$tpl->album = $album;
			$tpl->pagination = $pager->getLinks();
			return $tpl->execute();
		}
		catch (Exception $e)
		{
			return template_error($e);
		}
	}

	private function getAlbumList()
	{
		global $sql;

		$this->subcodename = 'albums';

		$pager = new Pager('pm/'.u1, Kio::getStat('images', 'gallery'), Kio::getConfig('limit', 'gallery'));

		//		$albums = Cache::get('gallery_albums_'.$pager->current.'.txt');

		$albums = $sql->setCache('gallery_'.$pager->current)->query('
			SELECT id, name, description, added, thumbnail, images
			FROM '.DB_PREFIX.'gallery_albums
			LIMIT '.$pager->limit.'
			OFFSET '.$pager->offset)->fetchAll(PDO::FETCH_ASSOC);

		try
		{
			$tpl = new PHPTAL('modules/gallery/gallery.tpl.html');
			$tpl->albums = $albums;
			$tpl->pager = $pager;
			return $tpl->execute();
		}
		catch (Exception $e)
		{
			return template_error($e);
		}
	}
}