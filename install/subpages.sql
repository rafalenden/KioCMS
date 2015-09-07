SQL Standard
CREATE TABLE kio_subpages
(
	page_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	page_title VARCHAR(100) NOT NULL,
	page_author VARCHAR(100) NOT NULL,
	page_author_id INT(10) UNSIGNED NOT NULL,
	page_created INT(11) NOT NULL,
	page_modified INT(11) NOT NULL,
	page_lang CHAR(2) NOT NULL,
	page_access VARCHAR(100),
	page_password VARCHAR(100),
	page_content TEXT NOT NULL,
	page_description VARCHAR(250),
	page_keywords VARCHAR(150),
	page_head TEXT,
	page_other_languages TEXT,
	page_views INT(10) DEFAULT 0,
	page_comments TINYINT(3) DEFAULT 0
);

page_access:
-2 - strona jest nieaktywna
-1 - dostęp dla zalogowanych
0 - dostęp dla wszystkich
>=1 lub 1, 2, 3 - dostęp dla grup o tym samym id