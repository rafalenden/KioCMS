SQL Standard
CREATE TABLE kio_partners
(
	partner_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	partner_created INT(11) UNSIGNED NOT NULL,
	partner_name VARCHAR(100) NOT NULL,
	partner_type TINYINT(1) UNSIGNED NOT NULL,
	partner_order TINYINT(3) UNSIGNED NOT NULL,
	partner_url VARCHAR(100) NOT NULL,
	partner_src TEXT NOT NULL,
);

partner_type:
0 - nieaktywny
1 - tekst
2 - obrazek
3 - flash