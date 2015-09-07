SQL Standard
CREATE TABLE kio_users_permissions
(
	perm_id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	perm_holder VARCHAR(50) NOT NULL,
	perm_codename VARCHAR(100) NOT NULL
);