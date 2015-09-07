SQL Standard
CREATE TABLE kio_guestbook
(
	entry_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	entry_time INT(11) UNSIGNED NOT NULL,
	entry_author VARCHAR(100),
	entry_author_id INT(10) UNSIGNED,
	entry_author_ip VARCHAR(15) NOT NULL,
	entry_email VARCHAR(100),
	entry_website VARCHAR(100),
	entry_message TEXT NOT NULL,
	PRIMARY KEY (entry_id)
);