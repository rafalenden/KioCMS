SQL Standard
CREATE TABLE kio_gallery_images
(
	image_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	image_name VARCHAR(255) NOT NULL,
	image_description VARCHAR(255),
	image_album_id SMALLINT(5) UNSIGNED NOT NULL,
	image_author_id INT(10) NOT NULL,
	image_created INT(11) UNSIGNED NOT NULL,
	image_views SMALLINT(5) UNSIGNED NOT NULL,
	image_rating TINYINT(1) NOT NULL,
	image_comments SMALLINT(5) NOT NULL,
	image_extension VARCHAR(5) NOT NULL
);

rating:
-1 - ocenianie wyłączone
0 - brak ocen
1-5 - ocena użytkowników

comments:
-1 - komentarze wyłaczone
0 - brak komentarzy
> 0 - liczba komentarzy