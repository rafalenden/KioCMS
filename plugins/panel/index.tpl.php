<?php
 // KioCMS - Kiofol Content Managment System
// includes/panel.tpl.php

// function panel($element, $if)
$codes = array(
	'b' => 0,
	'u' => 0,
	'i' => 0,
	'left' => 0,
	'center' => 0,
	'right' => 0,
	'justify' => 0,
	'pre' => 0,
	'hr' => 1,
	'list' => '[list]'."\n[*]~\n[*]\n".'[/list]',
	'code' => 0,
	'', // <br />
	'quote' => 0,
	'sup' => 0,
	'sub' => 0,
	'img' => 0,
	'url' => 0,
	'email' => 0,
	'size' => 2,
	'color' => 2,
	'font' => 2,
	'emoticons' => 0,
	'preview' => LOCAL.'includes/preview.php');

echo '<div id="preview_'.$target.'"></div><div class="panel" id="panel-'.$target.'">';
foreach ($codes as $key => $value)
{
	if ($key)
	{
		echo '<img ';
		if ($key != 'preview')
		{
			switch ($value)
			{
				case '0': echo 'alt="'.$key.'" class="tag"'; break;
				case '1': echo 'alt="'.$key.'" class="tag"'; break;
				case '2': echo 'alt="'.$key.'" class="tag"'; break;
				default:
					echo 'alt="'.$value.'" class="'.($key == 'preview' ? 'preview' : 'tag').'"';
			}
		}
		else
			echo 'alt="'.$value.'" class="preview"';
		echo ' title="'.$lang_panel[strtoupper($key)].'" src="'.LOCAL.'templates/'.THEME.'/images/panel/'.$key.'.png" />';
	}
	else
		echo '<br />';
}
echo '<span class="panel"><img class="resize decrease" src="'.LOCAL.'templates/'.THEME.'/images/panel/decrease.png" longdesc="'.$target.'" alt="-" title="'.$lang_panel['DECREASE_TEXTAREA'].'" /><img class="resize increase" src="'.LOCAL.'templates/'.THEME.'/images/panel/increase.png" longdesc="'.$target.'" alt="+" title="'.$lang_panel['INCREASE_TEXTAREA'].'" /></span></div>';