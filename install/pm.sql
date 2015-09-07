SQL Standard
CREATE TABLE kio_pm
(
	pm_id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	pm_time INT(11) UNSIGNED NOT NULL,
	pm_owner_id INT(10) UNSIGNED NOT NULL,
	pm_connector_id INT(10) UNSIGNED NOT NULL,
	pm_subject VARCHAR(100) NOT NULL,
	pm_message TEXT NOT NULL,
	pm_folder INT(11) UNSIGNED NOT NULL,
	pm_is_read TINYINT(1) UNSIGNED NOT NULL,
	pm_parsers SMALLINT(5) UNSIGNED NOT NULL,
);

pm_folder:
0 - odebrane
1 - wysłane
-- 2 - kopie robocze

pm_is_read:
0 - nieprzeczytanie
1 - przeczytane