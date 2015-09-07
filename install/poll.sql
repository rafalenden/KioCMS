SQL Standard
CREATE TABLE scms_poll_topics
(
	topic_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	topic_created INT(11) UNSIGNED NOT NULL,
	topic_title VARCHAR(100) NOT NULL,
	topic_active TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
	topic_votes MEDIUMINT(6) UNSIGNED NOT NULL DEFAULT 0
);

CREATE TABLE scms_poll_options
(
	option_id INT(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,
	option_topic_id INT(10) UNSIGNED NOT NULL,
	option_title VARCHAR(100) NOT NULL,
	option_votes MEDIUMINT(6) UNSIGNED NOT NULL DEFAULT 0
);

CREATE TABLE scms_poll_proposes
(
	propose_id INT(15) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	propose_title TEXT NOT NULL,
	propose_user_id INT(10) UNSIGNED NOT NULL,
	propose_user_ip CHAR(15) NOT NULL,
	propose_time INT(11) NOT NULL
);

CREATE TABLE scms_poll_votes
(
	vote_id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	vote_topic_id INT(10) UNSIGNED NOT NULL,
	vote_option_id INT(15) UNSIGNED NOT NULL,
	vote_user_id INT(10) UNSIGNED NOT NULL,
	vote_user_ip CHAR(15) NOT NULL,
	vote_time INT(11) UNSIGNED NOT NULL
);

multiple