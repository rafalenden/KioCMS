<?php
 // KioCMS - Kiofol Content Managment System
// admin/system/settings.php

$save = $_POST['save'] ? 1 : 0;
$form = $save ? $_POST['form'] : $cfg->system;

$chars = unserialize($cfg->system['chars']);

$form['chars_input'] = $save ? filter($_POST['chars_input']) : $chars[0];
$form['chars_output'] = $save ? $_POST['chars_output'] : $chars[1];
$form['blocks_headers'] = ($save ? $_POST['blocks_headers'] : $cfg->system['blocks_headers']) ? 1 : 0;
$form['styled_usernames'] = ($save ? $_POST['styled_usernames'] : $cfg->system['styled_usernames']) ? 1 : 0;
$form['bbcode'] = ($save ? $_POST['bbcode'] : $cfg->system['bbcode']) ? 1 : 0;
$form['multilang'] = ($save ? $_POST['multilang'] : $cfg->system['multilang']) ? 1 : 0;
$form['detect_language'] = ($save ? $_POST['detect_language'] : $cfg->system['detect_language']) ? 1 : 0;
$form['blocks'] = $save ? $_POST['blocks'] : ($cfg->system['blocks'] ? explode(', ', $cfg->system['blocks']) : '');

if ($save)
{
	$errors[] = !$form['title'] ? 'Pole <strong>tytuł</strong> nie może zostać puste.' : '';
	$errors[] = !$form['separator'] ? 'Pole <strong>separator</strong> nie może zostać puste.' : '';
	$errors[] = !$form['language'] ? 'Pole <strong>język</strong> nie może zostać puste.' : '';
	$errors[] = !$form['template'] ? 'Pole <strong>szablon</strong> nie może zostać puste.' : '';
	$errors[] = !$form['columns'] ? 'Pole <strong>ilość kolumn</strong> nie może zostać puste.' : '';
	$errors[] = !$form['date_format'] ? 'Pole <strong>format daty</strong> nie może zostać puste.' : '';
	$errors[] = !$form['short_date_format'] ? 'Pole <strong>format krótkiej daty</strong> nie może zostać puste.' : '';
	$errors[] = !$form['deleted_username'] ? 'Pole <strong>zastepczy login</strong> nie może zostać puste.' : '';
	$errors[] = !$form['communicator_name'] ? 'Pole <strong>nazwa komunikatora</strong> nie może zostać puste.' : '';
	$errors[] = !$form['communicator_url'] ? 'Pole <strong>adres odsyłacza</strong> nie może zostać puste.' : '';
	$errors[] = !$form['communicator_image'] ? 'Pole <strong>adres obrazka statusu</strong> nie może zostać puste.' : '';
	$errors[] = $form['seo_characters2'] && !preg_match('#^[a-Z0-9\n\r]*$#', $form['seo_characters2']) ? 'Zamienniki znaków muszą być alfanumeryczne.' : '';

	if (!in_array(true, $errors))
	{
		//$form['seo_characters'] = serialize(array($_POST['seo_characters'], $_POST['seo_characters2']));
		$form['communicator_image'] = htmlspecialchars($form['communicator_image']);
		$form['communicator_url'] = htmlspecialchars($form['communicator_url']);
		$form['reserved_usernames'] = r2n($form['reserved_usernames']);
		$form['time_zone'] = (float)$form['time_zone'];
		save_config($system, 'system', local_url.'admin/system');
	}
	else
		negative($errors);
}
else
	//neutral(array('<strong>Radzę z rozwagą i świadomością edytować poniższe dane.</strong>', $lang_system['REQUIRED']));

$templates_dir = opendir(ROOT.'templates/');
while (false !== ($template_dir = readdir($templates_dir)))
	if (ctype_alpha($template_dir)) $templates[] = $template_dir;

$tpl = new PHPTAL('admin/system/settings.html');
$tpl->form = $form;
$tpl->errors = $errors;
$tpl->system = $system;
$tpl->lang2 = $lang2;
$tpl->lang_admin = $lang_admin;
$tpl->templates = $templates;
$tpl->date_formats = array('d-m-Y, H:i', 'Y-m-d, H:i', 'd.m.y - H:i', 'j F Y, H:i', 'D j M Y, H:i');
$tpl->short_date_formats = array('d-m-Y', 'Y-m-d', 'd.m.y', 'j F Y', 'D j M Y');
$tpl->time_zones = $cfg->system['time_zones'];
$tpl->languages = get_all_langs();
$tpl->columns = form_columns();
$tpl->blocks = form_blocks();
echo $tpl->execute();