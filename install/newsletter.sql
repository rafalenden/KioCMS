SQL Standard
CREATE TABLE kio_newsletter
(
	newsletter_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	newsletter_email VARCHAR(100) NOT NULL,
	newsletter_code SMALLINT(4) UNSIGNED NOT NULL,
	newsletter_time INT(11) UNSIGNED NOT NULL,
	newsletter_user_ip VARCHAR(12) NOT NULL,
	UNIQUE KEY (newsletter_email)
);