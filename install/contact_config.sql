CREATE TABLE scms_registration_config
(
	receivers TEXT NOT NULL,
	message_max SMALLINT(5) NOT NULL,
);

registration_type:
0 - wy��czona
1 - w�aczona bez aktywacji e-mail
2 - w�aczona z aktywacj� e-mail
2 - w�aczona z aktywacj� administratora