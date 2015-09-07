SQL Standard
CREATE TABLE kio_comments
(
	comment_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	comment_holder VARCHAR(50) NOT NULL,
	comment_connector_id INT(10) NOT NULL,
	comment_author VARCHAR(100),
	comment_author_id INT(10) UNSIGNED,
	comment_author_ip CHAR(15) NOT NULL,
	comment_time INT(11) UNSIGNED NOT NULL,
	comment_message TEXT NOT NULL,
	comment_backlink VARCHAR(255) NOT NULL
);