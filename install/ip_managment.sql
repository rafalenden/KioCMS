CREATE TABLE `scms_ip_managment`
(
	`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`ip` VARCHAR(12) NOT NULL
	`time` INT(11) UNSIGNED NOT NULL,
	`last_visit` INT(11) UNSIGNED NOT NULL,
	`visits` INT(15) UNSIGNED NOT NULL DEFAULT 0,
	`user_agent` VARCHAR(250),
	`type` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
	`note` VARCHAR(250)
);

type:
-1 - zbanowany permanentnie
0 - bot - wgrywa lekki szablon
1 < - ban czasowy