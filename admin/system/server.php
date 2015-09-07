<?php
 // KioCMS - Kiofol Content Managment System
// admin/configuration/server.php

defined('KioCMS') || include_once '../ajax.php';

$save = $_POST['save'] ? true : false;
$form = array(
	'db_host'     => $save ? $_POST['db_host'] : db_host,
	'db_name'     => $save ? $_POST['db_name'] : db_name,
	'db_user'     => $save ? $_POST['db_user'] : db_user,
	'db_pass'     => $save ? $_POST['db_pass'] : db_pass,
	'db_prefix'   => $save ? $_POST['db_prefix'] : db_prefix,
	'db_type'     => $save ? $_POST['db_type'] : db_type,
	'local_dir'   => $save ? $_POST['local_dir'] : local_dir,
	'site_url'    => $save ? $_POST['site_url'] : site_url,
	'cookie_name' => $save ? $_POST['cookie_name'] : cookie_name,
	'logs'        => ($save ? $_POST['logs'] : logs) ? 1 : 0,
	'errors'      => $save ? $_POST['errors'] : errors,
	'lock_config' => is_writable(root_dir.'config.php') ? ($save ? $_POST['lock_config'] : false) : true);

if ($save)
{
	$errors = array(
		!is_writable(root_dir.'config.php') ? 'Plik <strong>config.php</strong> jest chroniony przed zapisem.' : '',
		!$form['db_host'] ? 'Pole <strong>serwer</strong> nie może zostać puste.' : '',
		!$form['db_name'] ? 'Pole <strong>nazwa</strong> nie może zostać puste.' : '',
		!$form['db_user'] ? 'Pole <strong>użytkownik</strong> nie może zostać puste.' : '',
		!$form['db_pass'] ? 'Pole <strong>hasło</strong> nie może zostać puste.' : '',
		!$form['db_prefix'] ? 'Pole <strong>prefix tabel<</strong> nie może zostać puste.' : '',
		!$form['db_type'] ? 'Pole <strong>typ</strong> nie może zostać puste.' : '',
		!$form['site_url'] ? 'Pole <strong>adres strony</strong> nie może zostać puste.' : '',
		!$form['local_dir'] ? 'Pole <strong>folder zawierający skrypt</strong> nie może zostać puste.' : '',
		!is_numeric($form['errors']) ? 'Typ <strong>raportowania błędów</strong> jest nieprawidłowy.' : '',
		!$form['cookie_name'] ? 'Pole <strong>nazwa ciasteczek</strong> nie może zostać puste.' : '');

	if (!in_array(true, $errors))
	{
		$config_file = fopen(root_dir.'config.php', 'w');
		fwrite($config_file, "<?php
 // KioCMS - Kiofol Content Managment System
// config.php

defined('KioCMS') || exit;
\$constants = array(
	'db_host'     => '".$form['db_host']."', // Database host
	'db_name'     => '".$form['db_name']."', // Database name
	'db_user'     => '".$form['db_user']."', // Database user
	'db_pass'     => '".$form['db_pass']."', // Database password
	'db_prefix'   => '".$form['db_prefix']."', // Tables prefix
	'db_type'     => '".$form['db_type']."', // Type of database
	'site_url'    => '".$form['site_url']."', // Website address
	'local_dir'   => '".$form['local_dir']."', // Local directory
	'cookie_name' => '".$form['cookie_name']."', // Cookie name
	'logs'        => ".$form['logs'].", // Saving logs
	'errors'      => ".$form['errors']."); // Errors reporting
array_map('define', array_keys(\$constants), array_values(\$constants));
?>");
		$form['lock_config'] && flock($config_file, 2);
		fclose($config_file);
		positive($lang_admin['SAVED_SUCCESSFUL']);
		redirect(local_url.'admin/system/server');
	}
	else
		negative($errors);
}
else
	neutral(array('<strong>Zmiana tych parametrów może sparaliżować całą witrynę!</strong>', 'Poniższe ustawienia są zapisane w pliku config.php', $lang_system['REQUIRED']));

// Form
echo '<form action="'.local_url.'admin/system/server" method="post"><table class="form">
<tr class="top title"><th>&nbsp;</th><td class="title">Baza danych</td></tr>
<tr><th><label for="f_db_host"><span class="required">*</span> Serwer</label></th><td><input type="text" name="db_host" value="'.$form['db_host'].'" class="big'.($errors[1] ? ' error' : '').'" id="f_db_host" /></td></tr>
<tr><th><label for="f_db_name"><span class="required">*</span> Nazwa</label></th><td><input type="text" name="db_name" value="'.$form['db_name'].'" class="big'.($errors[2] ? ' error' : '').'" id="f_db_name" /></td></tr>
<tr><th><label for="f_db_user"><span class="required">*</span> Użytkownik</label></th><td><input type="text" name="db_user" value="'.$form['db_user'].'" class="big'.($errors[3] ? ' error' : '').'" id="f_db_user" /></td></tr>
<tr><th><label for="f_db_pass"><span class="required">*</span> Hasło</label></th><td><input type="text" name="db_pass" value="'.$form['db_pass'].'" class="big'.($errors[4] ? ' error' : '').'" id="f_db_pass" /></td></tr>
<tr><th><label for="f_db_prefix"><span class="required">*</span> Prefix tabel</label></th><td><input type="text" name="db_prefix" value="'.$form['db_prefix'].'" class="big'.($errors[5] ? ' error' : '').'" id="f_db_prefix" /></td></tr>
<tr><th><label for="f_db_type"><span class="required">*</span> Typ</label></th><td><select name="db_type" id="f_db_type"><option value="mysql">MySQL</option></select></td></tr>
<tr class="title"><th>&nbsp;</th><td class="title">Pozostałe</td></tr>
<tr><th><label for="f_site_url"><span class="required">*</span> Adres strony</label></th><td><input type="text" name="site_url" value="'.$form['site_url'].'" class="big" id="f_site_url" /></td></tr>
<tr><th><label for="f_local_dir"><span class="required">*</span> Folder główny</label></th><td><input type="text" name="local_dir" value="'.$form['local_dir'].'" class="big" id="f_local_dir" /></td></tr>
<tr><th><label for="f_cookie_name"><span class="required">*</span> Nazwa ciasteczek</label></th><td><input type="text" name="cookie_name" value="'.$form['cookie_name'].'" class="big" id="f_cookie_name" /></td></tr>

<tr><th rowspan="2"><label for="f_errors"><span class="required">*</span> Raportowanie błędów</label></th><td><select onchange="document.getElementById(\'f_errors\').value = this.options[this.selectedIndex].value;">';
$reporting_types = array(
	'0'    => 'Wyłaczone (Zalecane)',
	'1'    => 'E_ERROR',
	'2'    => 'E_WARNING',
	'4'    => 'E_PARSE',
	'7'    => 'E_ERROR + E_WARNING + E_PARSE',
	'8'    => 'E_NOTICE',
	'2048' => 'E_STRICT',
	'6143' => 'E_ALL');

foreach ($reporting_types as $reporting_value => $reporting_constant)
	echo '<option value="'.$reporting_value.'"'.($form['errors'] == $reporting_value ? ' selected="selected"' : '').'>'.$reporting_constant.'</option>';

echo '<option value="'.$form['errors'].'"'.(!in_array($form['errors'], array_keys($reporting_types)) ? ' selected="selected"' : '').'>Inne - wpisz niżej</option></select></td></tr>
<tr><td><input type="text" name="errors" id="f_errors" value="'.$form['errors'].'" class="auto" size="4" /><div class="description">Aby dowiedzieć się więcej przeczytaj <a href="http://www.php.net/error_reporting" target="_blank">opis funkcji &quot;error_reporting&quot;</a></div></td></tr>

<tr><th rowspan="2"><label>Opcje</label></th><td><label><input type="checkbox" name="logs"'.($form['logs'] ? ' checked="checked"' : '').' /> Zapisuj logi</label></td></tr>
<tr><td><label><input type="checkbox" name="lock_config"'.(!is_writable(root_dir.'config.php') ? ' disabled="disabled"' : '').($form['lock_config'] ? ' checked="checked"' : '').' /> Zablokuj możliwość edycji tych ustawień</label></td></tr>';

form_end(false);
?>