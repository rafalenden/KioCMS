<?php
 // KioCMS - Kiofol Content Managment System
// admin/comments/settings.php

defined('KioCMS') || include_once '../ajax.php';

$save = $_POST['save'] ? true : false;
$form = $save ? $_POST['form'] : $module;
$form['bbcode'] = ($save ? $_POST['bbcode'] : $module['bbcode']) ? 1 : 0;
$form['see_only_logged'] = ($save ? $_POST['see_only_logged'] : $module['see_only_logged']) ? 1 : 0;
$form['add_only_logged'] = ($save ? $_POST['add_only_logged'] : $module['add_only_logged']) ? 1 : 0;

if ($save)
{
	$errors[] = !$form['content_max'] ? 'Pole <strong>limit znaków wpisu</strong> nie może zostać puste.' : '';
	$errors[] = !$form['order_by'] ? 'Należy określić <strong>sortowanie wpisów</strong>.' : '';

	!in_array(true, $errors) 
		? save_config($module, 'comments', local_url.'admin/comments/settings', false)
		: negative($errors);
}
else
	neutral(array('Poniższa konfiguracja dotyczy wyłącznie komentarzy.', $lang_system['REQUIRED']));

// Form
echo '<form action="'.local_url.'admin/comments/settings" method="post"><table class="form">
<tr class="top title"><th>&nbsp;</th><td>Personalizacja</td></tr>
<tr><th><label for="f_content_max"><span class="required">*</span>Limit znaków wpisu</label></th><td><input type="text" name="form[content_max]" id="f_content_max" value="'.$form['content_max'].'" class="auto" size="3" /></td></tr>
<tr><th><label for="f_order_by"><span class="required">*</span>Sortowanie wpisów</label></th><td><select name="form[order_by]" id="f_order_by"><option value="ASC"'.($form['order_by'] == 'ASC' ? ' selected="selected"' : '').'>Rosnąco</option><option value="DESC"'.($form['order_by'] == 'DESC' ? ' selected="selected"' : '').'>Malejąco</option></select></td></tr>
<tr><th rowspan="3"><label>Opcje</label></th><td><label><input name="see_only_logged" type="checkbox" '.($form['see_only_logged'] ? ' checked="checked"' : '').'/> Komentarze widoczne tylko po zalogowaniu</label><div class="description">Właczenie tej opcji automatycznie zablokuje dodawanie komentarzy przez gości</div></td></tr>
<tr><td><label><input name="add_only_logged" type="checkbox" '.($form['add_only_logged'] ? ' checked="checked"' : '').'/> Komentowanie tylko po zalogowaniu</label></td></tr>
<tr><td><label><input name="bbcode" type="checkbox" '.($form['bbcode'] ? ' checked="checked"' : '').'/> BBCode</label><div class="description">Formatuje tekst (np. [b]pogrubienie[/b[)<br />Zaznaczenie nie odniesie skutku jeśli BBCode zostanie wyłączony w konfiguracji systemowej</div></td></tr>';

form_end(false);
?>