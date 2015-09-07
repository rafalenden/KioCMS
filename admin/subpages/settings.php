<?php
 // KioCMS - Kiofol Content Managment System
// admin/news/settings.php

defined('KioCMS') || include_once '../ajax.php';

$save = $_POST['save'] ? true : false;
$form = $save ? $_POST['form'] : $module;
$form['bbcode'] = ($save ? $_POST['bbcode'] : $module['bbcode']) ? 1 : 0;
$form['allow_signatures'] = ($save ? $_POST['allow_signatures'] : $module['allow_signatures']) ? 1 : 0;
$form['blocks'] = $save ? $_POST['blocks'] : ($module['blocks'] ? explode(', ', $module['blocks']) : '');

if ($save)
{
	$errors[] = !$form['entries_per_page'] ? 'Pole <strong>wpisów na stronę</strong> nie może zostać puste.' : '';
	$errors[] = !$form['order_by'] ? 'Należy określić <strong>sortowanie wpisów</strong>.' : '';

	!in_array(true, $errors)
		? save_config($module, 'news', local_url.'admin/news/settings')
		: negative($errors);
}
else
	neutral(array($lang_admin['MODULE_SETTINGS'], $lang_system['REQUIRED']));

// Form
form_begin(local_url.'admin/news/settings');

echo '<tr><th><label for="f_message_max">Limit znaków krótkiej treści</label></th><td><input type="text" name="form[message_max]" id="f_message_max" value="'.$form['message_max'].'" class="auto" size="3" /></td></tr>
<tr><th><label for="f_entries_per_page"><span class="required">*</span>Wpisów na stronę</label></th><td><input type="text" name="form[entries_per_page]" id="f_entries_per_page" value="'.$form['entries_per_page'].'" class="auto" size="2" /></td></tr>
<tr><th><label for="f_order_by"><span class="required">*</span>Sortowanie wpisów</label></th><td><select name="form[order_by]" id="f_order_by"><option value="ASC"'.($form['order_by'] == 'ASC' ? ' selected="selected"' : '').'>Rosnąco</option><option value="DESC"'.($form['order_by'] == 'DESC' ? ' selected="selected"' : '').'>Malejąco</option></select></td></tr>

<tr><th rowspan="2"><label>Opcje</label></th><td><label><input name="bbcode" type="checkbox" '.($form['bbcode'] ? ' checked="checked"' : '').'/> BBCode</label><div class="description">Formatuje tekst (np. [b]pogrubienie[/b])<br />Zaznaczenie nie odniesie skutku jeśli BBCode zostanie wyłączony w konfiguracji systemowej</div></td></tr>
<tr><td><label><input name="allow_signatures" type="checkbox" '.($form['allow_signatures'] ? ' checked="checked"' : '').'/> Pokaż podpis użytkownika pod wpisem</label></td></tr>';

form_end();
?>