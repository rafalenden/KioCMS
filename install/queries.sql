CREATE TABLE `scms_blocks`
(
	`id` int(10) NOT NULL auto_increment,
	`name` varchar(100) NOT NULL,
	`codename` varchar(50) NOT NULL,
	`active` int(1) NOT NULL default '0',
	`side` varchar(1) NOT NULL default 'L',
	`position` int(10) NOT NULL,
	`is_file` int(1) NOT NULL default '1',
	`content` text NOT NULL,
	PRIMARY KEY  (`id`)
	UNIQUE KEY codename (`codename`)
);

CREATE TABLE `scms_blocks`
(
	`id` int(9) unique not null primary key auto_increment,
	`name` varchar(100) NOT NULL,
	`codename` varchar(50) NOT NULL unique key,
	`active` int(1) NOT NULL,
	`side` varchar(1) NOT NULL,
	`position` int(9) NOT NULL,
	`langname` varchar(1) NOT NULL,
	`is_file` int(1) NOT NULL,
	`content` text NOT NULL,
);

CREATE TABLE `scms_poll`
(
	`id` int(10) unique NOT NULL auto_increment,
	`created` int(11) NOT NULL,
	`topic` varchar(100) NOT NULL,
	`options` text NOT NULL,
	`votes` text NOT NULL,
	`voters_ip` text NOT NULL,
	`voters_id` int(10) NOT NULL,
	`multichoice` int(1) NOT NULL,
	`active` int(1) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `scms_shoutbox`
(
	`id` int(10) unique NOT NULL auto_increment,
	`date` int(11) NOT NULL,
	`author` varchar(100) NOT NULL,
	`message` text NOT NULL,
	`registered_user` int(1) NOT NULL,
	`user_ip` varchar(12) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `scms_guestbook`
(
	`id` int(10) unique NOT NULL auto_increment,
	`date` int(11) NOT NULL,
	`author` varchar(100) NOT NULL,
	`email` varchar(100) NOT NULL,
	`www` varchar(100) NOT NULL,
	`message` text NOT NULL,
	`registered_user` int(1) NOT NULL,
	`user_ip` varchar(12) NOT NULL,
	PRIMARY KEY (`id`)
);

INSERT INTO `scms_blocks` (`id`, `name`, `codename`, `active`, `side`, `position`, `fromfile`, `content`) VALUES
(1, 'Nawigacja', 'navigation', '1', 'L', '1', '1', ''),
(2, 'Linki', 'links', '1', 'L', '2', '1', ''),
(3, 'Partnerzy', 'partners', '1', 'L', '3', '1', ''),
(4, 'Newsletter', 'newsletter', '1', 'L', '4', '1', ''),
(5, 'Wyszukiwarka', 'search', '1', 'L', '5', '1', ''),
(6, 'Najcze�ciej pobierane', 'top-downloads', '1', 'L', '6', '1', ''),
(7, 'Pobieralnia', 'downloads', '1', 'L', '7', '1', ''),
(8, 'Krzykacz', 'shoutbox', '1', 'L', '8', '1', ''),
(9, 'Panel użytkownika', 'user-panel', '1', 'P', '9', '1', ''),
(10, 'Nowi użytkownicy', 'new-users', '1', 'P', '10', '1', ''),
(11, 'Og3oszenia', 'annoucements', '1', 'P', '11', '1', ''),
(12, 'Ostatnie ogloszenia', 'last-annoucements', '1', 'P', '12', '1', ''),
(13, 'Statystyka strony', 'page-stats', '1', 'P', '13', '1', ''),
(14, 'Strony statyczne', 'static-pages', '1', 'P', '14', '1', ''),
(15, 'Ostatnie nowości', 'last-news', '1', 'P', '15', '1', ''),
(16, 'Ostatnie artykuly', 'last-articles', '1', 'P', '16', '1', ''),
(17, 'Ostatnie felietony', 'last-felietony', '1', 'P', '17', '1', ''),
(18, 'Ostatnie wywiady', 'last-wywiady', '1', 'P', '17', '1', ''),
(19, 'Propozycje sond', 'poll-proposes', '1', 'P', '19', '1', ''),
(20, 'Sonda', 'poll', '1', 'P', '20', '1', ''),
(21, 'Pogoda', 'pogoda', '1', 'P', '21', '1', ''),
(22, 'Kalendarz', 'calendar', '1', 'P', '22', '1', ''),
(23, 'Chat', 'chat', '0', 'L', '23', '1', '');