SQL Standard
CREATE TABLE kio_gallery_albums
(
	album_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	album_name VARCHAR(255) NOT NULL,
	album_description VARCHAR(255),
	album_created INT(11) UNSIGNED NOT NULL,
	album_access VARCHAR(100) NOT NULL DEFAULT 0,
	album_thumbnail VARCHAR(4),
	album_images TINYINT(3) UNSIGNED NOT NULL
);

album_thumbnail:
0 - brak obrazka
true - obrazek obecny (rozszerzenie))

album_access:
alphanumeric - dostęp na hasło
-2 - strona jest nieaktywna
-1 - dostęp dla zalogowanych
0 - dostęp dla wszystkich
>= 1  - dostęp dla grupy o tym samym id