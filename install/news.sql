SQL Standard
CREATE TABLE news
(
	news_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	news_title VARCHAR(255) NOT NULL,
	news_parent_id INT(10) UNSIGNED,
	news_author VARCHAR(255) NOT NULL,
	news_author_id INT(10) UNSIGNED,
	news_created INT(11) NOT NULL,
	news_access TINYINT(1) UNSIGNED,
	news_description TEXT,
	news_keywords VARCHAR(255) NOT NULL,
	news_content TEXT NOT NULL,
	news_extended_content TEXT,
	news_category_id SMALLINT(5),
	news_lang CHAR(2),
	news_parser TINYINT(1),
	news_comments TINYINT(3),
);

publication:
0 - nieopublikowany
1 - widoczny dla wszystkich
2 - widoczny tylko dla zarejestrowanych

comments:
-1 - komentarze wyłączone
> 0 - ilość komentarzy

other_languages:
pl=2 - kod jezyka|id newsa o tej samej tresci, ale w innym jezyku

parent_id:
ID newsa źródłowego, z którego pochodzi tłumaczenie

ratings
comments
text_transform
filters