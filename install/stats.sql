SQL Standard
CREATE TABLE kio_stats
(
	stat_key VARCHAR(50) NOT NULL PRIMARY KEY,
	stat_value TEXT NOT NULL
);

INSERT INTO scms_stats (name, content) VALUES
("guestbook_entries", 0),
("users", 0),
("news_entries", 0);