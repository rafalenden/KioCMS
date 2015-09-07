SQL Standard
CREATE TABLE blocks
(
	block_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	block_name VARCHAR(100) NOT NULL,
	block_codename VARCHAR(50) NOT NULL,
	block_type TINYINT(1) UNSIGNED NOT NULL,
	block_side VARCHAR(1) NOT NULL,
	block_order TINYINT(3) UNSIGNED NOT NULL,
	block_header TINYINT(1) UNSIGNED NOT NULL,
	block_content TEXT NOT NULL,
	block_css_file TINYINT(1) UNSIGNED NOT NULL,
	block_params TEXT NOT NULL,
	UNIQUE KEY (block_codename)
);

permit:
0 - blok jest nieaktywny
1 - widoczny dla wszystkich
2 - widoczny tylko dla gości
3 - widoczny tylko dla zalogowanych


INSERT INTO scms_blocks (id,name,codename,type,side,position,header,content,parameters) VALUES
(1,'Nawigacja','navigation',1,'L',1,1,'',1),
(3,'Partnerzy','partners',1,'L',3,1,'',''),
(4,'Newsletter','newsletter',1,'L',4,1,'',''),
(5,'Wyszukiwarka','search',1,'L',5,1,'',''),
(6,'Popularne pliki','top-downloads',1,'L',6,1,'',''),
(7,'Pobieralnia','downloads',1,'L',7,1,'',''),
(8,'','shoutbox',1,'L',8,1,'','150
20'),
(9,'Panel użytkownika','user-panel',1,'R',9,1,'',''),
(10,'Nowi użytkownicy','new-users',1,'R',10,1,'',''),
(11,'Ogloszenia','annoucements',1,'R',11,1,'',''),
(12,'Ostatnie ogloszenia','last-annoucements',1,'P',12,1,'',''),
(13,'Statystyka strony','page-stats',1,'R',13,1,'',''),
(14,'Strony statyczne','static-pages',1,'R',14,1,'',''),
(15,'Ostatnie nowości','last-news',1,'R',15,1,'',''),
(16,'Ostatnie artykuly','last-articles',1,'R',16,1,'',''),
(20,'','poll',1,'R',20,1,'',''),
(22,'','calendar',1,'R',22,1,'',''),
(23,'Chat','chat',0,'L',23,1,'','');