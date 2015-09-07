SQL Standard
CREATE TABLE lang_translations
(
	translation_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	translation_lang CHAR(2) NOT NULL,
	translation_holder VARCHAR(50),
	translation_key CHAR(16) NOT NULL,
	translation_value TEXT NOT NULL,
	UNIQUE KEY (translation_key)
);

INSERT INTO lang_translations (translation_lang, translation_holder, translation_key, translation_value) VALUES
('pl', 'system', 'Next page', 'Następna strona');

-- Shoutbox
('pl', 'Shoutbox', 'Krzykacz'),
('pl', 'Author', 'Autor'),
'Message' => 'Treść',
'Reply' => 'Odpowiedz',
'No entries' => 'Brak wpisów',

// Komunikaty
'Entry was successfully added.' => 'Wpis został dodany.',

// Błędy
'Field <strong>author</strong> can not be empty.' => 'Pole <strong>autor</strong> nie może zostać puste.',
'Entered <strong>nickname</strong> is registered.' => 'Podana <strong>ksywa</strong> jest zarejestrowana.',
'Field <strong>message</strong> can not be empty.' => 'Pole <strong>treść</strong> nie może zostać puste.');