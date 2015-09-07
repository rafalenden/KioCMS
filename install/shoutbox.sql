SQL Standard
CREATE TABLE kio_shoutbox
(
	shout_id INT(15) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	shout_time INT(11) UNSIGNED NOT NULL,
	shout_author VARCHAR(100) NOT NULL,
	shout_author_id INT(10) UNSIGNED NOT NULL,
	shout_author_ip VARCHAR(12) NOT NULL
	shout_message TEXT NOT NULL
);