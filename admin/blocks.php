<?php
 // KioCMS - Kiofol Content Managment System
// admin/functions.php

defined('KioCMS') || exit;

function admin_navigation()
{
	
}

function save_config($config, $holder, $redirect, $save_blocks = true)
{
	global $form, $lang_system, $lang_admin;
	if ($save_blocks)
		$form['blocks'] = $_POST['blocks']
			? (count($_POST['blocks']) == $_POST['blocks_count'] ? '' : implode(', ', $_POST['blocks']))
			: 'NULL';
	foreach ($form as $key => $value)
	{
		if (isset($config[$key]) && $config[$key] != $value)
			$value
				? sql_query('
					UPDATE '.DB_PREFIX.'config
					SET content = '.(is_numeric($value) ? $value : '"'.$value.'"').'
					WHERE name = "'.$key.'" AND holder = "'.$holder.'"')
				: sql_query('DELETE FROM '.DB_PREFIX.'config WHERE name = "'.$key.'" AND holder = "'.$holder.'"');
		elseif (!isset($config[$key]) && $value)
			sql_query('
				INSERT INTO '.DB_PREFIX.'config (holder, name, content)
				VALUES ("'.$holder.'", "'.$key.'", '.(is_numeric($value) ? (int)$value : '"'.$value.'"').')');
	}
	sql_error()
		? negative(array($lang_system['ERROR_SQL'], sql_error()))
		: positive($lang_admin['SAVED_SUCCESSFUL']).
		  redirect($redirect);

}

function form_begin($action)
{
	echo '<form action="'.$action.'" method="post"><table class="form"><tr class="top title"><th>&nbsp;</th><td>'.$GLOBALS['lang_admin']['personalization'].'</td></tr><tr><th><label for="f_columns">'.$GLOBALS['lang_admin']['COLUMNS'].'</label></th><td><select name="form[columns]" id="f_columns"><option value=""'.(!$GLOBALS['form']['columns'] ? ' selected="selected"' : '').'>'.$GLOBALS['lang_admin']['DEFAULT_COLUMNS'].' ('.$GLOBALS['system']['columns'].')</option><option value="1"'.($GLOBALS['form']['columns'] == '1' ? ' selected="selected"' : '').'>'.$GLOBALS['lang_admin']['ONE'].'</option><option value="2"'.($GLOBALS['form']['columns'] == '2' ? ' selected="selected"' : '').'>'.$GLOBALS['lang_admin']['TWO'].'</option><option value="3"'.($GLOBALS['form']['columns'] == '3' ? ' selected="selected"' : '').'>'.$GLOBALS['lang_admin']['THREE'].'</option></select></td></tr>';
}

function form_end($codename = 'module')
{
	if ($codename)
	{
		echo '<tr class="title"><th>&nbsp;</th><td>'.$GLOBALS['lang_admin']['blocks'].'</td></tr><tr><th><label>Lewa strona</label></th><td class="blocks">';
		$query = sql_query('SELECT id, codename FROM '.DB_PREFIX.'blocks WHERE side = "L" AND type != 0 ORDER BY position');
		while ($db = sql_fetch_assoc($query))
		{
			echo '<label><input type="checkbox" name="blocks[]" value="'.$db['id'].'"'.(!$GLOBALS[$codename]['blocks'] || in_array($db['id'], (array)$GLOBALS['form']['blocks']) ? ' checked="checked"' : '').' /> '.$db['codename'].'</label>&nbsp; ';
			$x++;
		}
		echo '</td></tr>
		<tr><th><label>Prawa strona</label></th><td class="blocks">';
		$query = sql_query('SELECT id, codename FROM '.DB_PREFIX.'blocks WHERE side = "R" AND type != 0 ORDER BY position');
		while ($db = sql_fetch_assoc($query))
		{
			echo '<label><input type="checkbox" name="blocks[]" value="'.$db['id'].'"'.(!$GLOBALS[$codename]['blocks'] || in_array($db['id'], (array)$GLOBALS['form']['blocks']) ? ' checked="checked"' : '').' /> '.$db['codename'].'</label>&nbsp; ';
			$x++;
		}
		echo '<input type="hidden" name="blocks_count" value="'.$x.'" /></td></tr>';
	}
	echo '<tr class="bottom"><th>&nbsp;</th><td><input type="submit" name="save" value=" '.$GLOBALS['lang_admin']['save'].' " class="button submit" /><input type="reset" value="'.$GLOBALS['lang_system']['RESET'].'" title="'.$GLOBALS['lang_system']['RESET_TITLE'].'" class="button reset" /></td></tr>
</table></form>';
}

function form_columns()
{
	return '<select name="form[columns]" id="form-columns"><option value=""'.(!$GLOBALS['form']['columns'] ? ' selected="selected"' : '').'>'.$GLOBALS['lang_admin']['DEFAULT_COLUMNS'].' ('.$GLOBALS['system']['columns'].')</option><option value="1"'.($GLOBALS['form']['columns'] == '1' ? ' selected="selected"' : '').'>'.$GLOBALS['lang_admin']['ONE'].'</option><option value="2"'.($GLOBALS['form']['columns'] == '2' ? ' selected="selected"' : '').'>'.$GLOBALS['lang_admin']['TWO'].'</option><option value="3"'.($GLOBALS['form']['columns'] == '3' ? ' selected="selected"' : '').'>'.$GLOBALS['lang_admin']['THREE'].'</option></select>';
}

function form_blocks($codename = 'module')
{
	if ($codename)
	{
		$query = sql_query('SELECT id, codename FROM '.DB_PREFIX.'blocks WHERE side = "L" AND type != 0 ORDER BY position');
		while ($db = sql_fetch_assoc($query))
		{
			$left .= '<label><input type="checkbox" name="blocks[]" value="'.$db['id'].'"'.(!$GLOBALS[$codename]['blocks'] || in_array($db['id'], (array)$GLOBALS['form']['blocks']) ? ' checked="checked"' : '').' /> '.$db['codename'].'</label>&nbsp; ';
			$x++;
		}
		$query = sql_query('SELECT id, codename FROM '.DB_PREFIX.'blocks WHERE side = "R" AND type != 0 ORDER BY position');
		while ($db = sql_fetch_assoc($query))
		{
			$right .= '<label><input type="checkbox" name="blocks[]" value="'.$db['id'].'"'.(!$GLOBALS[$codename]['blocks'] || in_array($db['id'], (array)$GLOBALS['form']['blocks']) ? ' checked="checked"' : '').' /> '.$db['codename'].'</label>&nbsp; ';
			$x++;
		}
		$right .= '<input type="hidden" name="blocks_count" value="'.$x.'" />';
		return array('left' => $left, 'right' => $right);
	}
}

function form_limit2($limit_name)
{
	(int)$_POST[$limit_name] && setcookie('KioCMS-'.COOKIE.'-'.$limit_name, (int)$_POST[$limit_name], TIMESTAMP + 31536000, '/', '', false, true).redirect(CURRENT_URL);
	$GLOBALS['lol'] = (int)$_COOKIE['KioCMS-'.COOKIE.'-'.$limit_name] ? (int)$_COOKIE['KioCMS-'.COOKIE.'-'.$limit_name] : 30;
	echo '<input type="text" class="auto" size="2" name="'.$limit_name.'" value="'.$GLOBALS[$limit_name].'" /> na stronę <input type="submit" class="button" value="Pokaż" />';
}
function form_limit($limit_name)
{
	(int)$_POST[$limit_name] && setcookie('KioCMS-'.COOKIE.'-'.$limit_name, (int)$_POST[$limit_name], TIMESTAMP + 31536000, '/', '', false, true).redirect(CURRENT_URL);
	$GLOBALS[$limit_name] = (int)$_COOKIE['KioCMS-'.COOKIE.'-'.$limit_name] ? (int)$_COOKIE['KioCMS-'.COOKIE.'-'.$limit_name] : 30;
	return '<label>'.sprintf('%s na stronę', '<input type="text" class="auto" size="2" name="'.$limit_name.'" value="'.$GLOBALS[$limit_name].'" /> ').'</label> <input type="submit" class="button" value="Pokaż" />';
}