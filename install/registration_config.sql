CREATE TABLE scms_registration_config
(
	registration_type int(1) NOT NULL,
	welcome_email int(1) NOT NULL,
	show_rules int(1) NOT NULL,
	login_min int(1) NOT NULL,
	login_max int(2) NOT NULL,
	password_min int(1) NOT NULL,
);

registration_type:
0 - wy³¹czona
1 - w³aczona bez aktywacji e-mail
2 - w³aczona z aktywacj¹ e-mail
2 - w³aczona z aktywacj¹ administratora