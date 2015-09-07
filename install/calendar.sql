CREATE TABLE `scms_calendar`
(
	`day` int(10) unique NOT NULL auto_increment,
	`created` int(11) NOT NULL,
	`topic` varchar(100) NOT NULL,
	`options` text NOT NULL,
	`votes` text NOT NULL,
	`voters_ip` text NOT NULL,
	`voters_id` int(10) NOT NULL,
	`multichoice` int(1) NOT NULL,
	`event` int(1) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `scms_poll_topics`
(
	`id` int(10) unique NOT NULL auto_increment,
	`created` int(11) NOT NULL,
	`topic` varchar(100) NOT NULL,
	`multichoice` int(1) NOT NULL,
	`active` int(1) NOT NULL default '1',
	PRIMARY KEY (`id`)
);

CREATE TABLE `scms_poll_options`
(
	`id` int(10) unique NOT NULL auto_increment,
	`topic_id` int(10) NOT NULL,
	`option` varchar(100) NOT NULL,
	`count` SMALLINT(5) NOT NULL default '0',
	PRIMARY KEY (`id`)
);

CREATE TABLE `scms_poll_proposes`
(
	`id` int(10) unique NOT NULL auto_increment,
	`propose` text NOT NULL,
	`num_options` int(2) NOT NULL,
	`user_id` int(10) NOT NULL,
	`user_ip` varchar(15) NOT NULL,
	`propose_date` int(11) NOT NULL,
	PRIMARY KEY (`id`)
);

CREATE TABLE `scms_poll_votes`
(
	`id` int(10) unique NOT NULL auto_increment,
	`topic_id` int(10) NOT NULL,
	`option_id` int(100) NOT NULL,
	`voter_id` int(10) NOT NULL,
	`voter_ip` varchar(15) NOT NULL,
	`vote_date` int(11) NOT NULL,
	PRIMARY KEY (`id`)
);