SQL Standard
CREATE TABLE lang_phrases
(
	phrase_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	phrase_holder VARCHAR(50),
	phrase_value TEXT NOT NULL,
	UNIQUE KEY (phrase_value)
);