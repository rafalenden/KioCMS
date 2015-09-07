SQL Standard
CREATE TABLE kio_config
(
	config_holder VARCHAR(50) NOT NULL,
	config_key VARCHAR(50) NOT NULL,
	config_value TEXT
);

filter jest zajęty (filters)

TINYINT (1-3)
SMALLINT (4-5)
MEDIUMINT (6-7)
INT (8-10)

INSERT INTO scms_config (holder, name, content) VALUES
('system', 'lang', 'pl'),
('system', 'gzip', 1),
('system', 'new_window', 1),
	('system', 'bbcode_off', 1),
	('system', 'censure_off', 1),
	('system', 'emoticons_off', 1),
('system', 'flood_interval', 1),
('system', 'columns', 3),
('system', 'blocks', 'SELECT * FROM `scms_blocks` WHERE `side` = 'L' AND `type` != 0 ORDER BY `position`
SELECT * FROM `scms_blocks` WHERE `side` = 'R' AND `type` != 0 ORDER BY `position`'),
	('system', 'template_type', 'a'),
('system', 'title', 'KioCMS'),
('system', 'separator', '-'),
('system', 'description', 'Profesjonalny, prosty w obsłudze i modyfikowalny CMS'),
('system', 'keywords', 'kiocms, cms, kiofol, software, system, zarządzania, treścią, php, mysql'),
('system', 'template', 'Kiofol'),
('system', 'date_format', 'd-m-Y, H:i'),
('system', 'date_format_short', 'd-m-Y'),
('system', 'time_zone', 0),
('system', 'front_page', 'news'),
	('system', 'front_page_title', 'Witaj!'),
('system', 'smtp_host', ''),
('system', 'smtp_username', ''),
('system', 'smtp_password', ''),
('system', 'spam_filter', 1),
('system', 'mail_from', 'endzio@o2.pl'),
('system', 'offline', 0),
	('system', 'pagination_type', 1),
('system', 'blocks_headers', 1),
('system', 'session_time', 0),
	('system', 'deleted_username', '?'),
('system', 'communicator_name', 'Gadu-Gadu'),
('system', 'communicator_image', 'http://status.gadu-gadu.pl/users/status.asp?id=%s&amp;styl=1'),
('system', 'communicator_url', 'gg://{id}'),
('system', 'chars_input', 'ą'),
('system', 'chars_output', 'a'),
('system', 'reserved_usernames', 'Admin
Webmaster
Guest
root
Gość
Administrator
Anonim'),
('system', 'nicknames_inline', 1),
('system', 'url_type', 0),
('system', 'multilang', 1),
('system', 'detect_language', 0),
('system', 'date_relative', 1), /* scxs */
('system', 'translate_date', 1),
('system', 'chars', 'a:18:{s:2:"ę";s:1:"e";s:2:"Ę";s:1:"E";s:2:"ś";s:1:"s";s:2:"Ś";s:1:"S";s:2:"ż";s:1:"z";s:2:"Ż";s:1:"Z";s:2:"ź";s:1:"z";s:2:"Ź";s:1:"Z";s:2:"ć";s:1:"c";s:2:"Ć";s:1:"C";s:2:"ł";s:1:"l";s:2:"Ł";s:1:"L";s:2:"ń";s:1:"n";s:2:"Ń";s:1:"N";s:2:"ą";s:1:"a";s:2:"Ą";s:1:"A";s:2:"ó";s:1:"o";s:2:"Ó";s:1:"O";} '),
('system', 'spam_words', 'viagra, sex, penis, cheap'),
('system', 'rights', 'array('Guestbook' => array('Moderator' => 'm_guestbook', 'Administration' => 'a_guestbook', 'Configuration' => 'c_guestbook'))'),
('system', 'filters', 123),
('system', 'parsers', 12345),
('system', 'bbcode_parser', 1),
('system', 'emoticons_parser', 1),
('system', 'censure_parser', 1),
('system', 'javascript_sort', 1),
('system', 'date_format_relative', '%s, H:i'),
('system', 'custom_english_lang', 0),
------------------------------------------------------------------------
('registration', 'type', 1),
('registration', 'welcome_email', 1),
('registration', 'show_rules', 1),
('registration', 'nickname_max', 20),
('registration', 'notify', 0),
('registration', 'welcome_pm', 'Witaj, zedytuj swój profil wzbogacając stronę o dodatkową treść.'),
------------------------------------------------------------------------
('contact', 'receivers', '1'),
('contact', 'informations', 'Wrocław, ul. Mickiewicza 34/12
Telefon komórkowy: 659-551-348
Telefon stacjonarny: (077) 4854-524'),
------------------------------------------------------------------------
('edit_profile', 'allow_change_nick', 0),
------------------------------------------------------------------------
('profile', 'columns', 2),
------------------------------------------------------------------------
('news', 'limit', 5),
('news', 'sort', 'added DESC'),
------------------------------------------------------------------------
('guestbook', 'message_max', 250),
('guestbook', 'limit', 11),
('guestbook', 'sort', 'DESC'),
('guestbook', 'flood_interval', 5),
('guestbook', 'parser', 12345),
('guestbook', 'signatures', 1),
------------------------------------------------------------------------
('users', 'limit', 10),
('users', 'template_type', 2),
------------------------------------------------------------------------
('gallery', 'limit', 10),
------------------------------------------------------------------------
('pm', 'limit', 20),
('pm', 'message_max', 250),
('pm', 'inbox_max', 50),
('pm', 'outbox_max', 50),
('pm', 'columns', 2),
('pm', 'parsers', 12345),
('pm', 'reply_with_quote', 1),
------------------------------------------------------------------------
('comments', 'content_max', 250),
('comments', 'sort', 'ASC'),
------------------------------------------------------------------------
/*('system', 'flags', 0),*/
('system', '', ''),
('system', '', ''),
('system', '', ''),
('system', '', ''),
('system', '', ''),

IN ('')
LIKE '%'

ą
ć
ę
ł
ń
ó
ś
ź
ż
Ą
Ć
Ę
Ł
Ń
Ó
Ś
Ź
Ż
 
/
------------
a
c
e
l
n
o
s
z
z
A
C
E
L
N
O
S
Z
Z
_
