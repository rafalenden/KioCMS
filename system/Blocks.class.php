<?php
 // KioCMS - Kiofol Content Managment System
// includes/blocks.php

if ($output['columns'] > 1)
{
	$get_blocks = sql_query('
		SELECT * FROM '.DB_PREFIX.'blocks
		WHERE '.($output['blocks'] ? 'id NOT IN('.$output['blocks'].') AND ' : '').($output['columns'] == 2
			? 'side = "L" AND type != 0'
			: 'type != 0').'
		ORDER BY position');
	while ($block = sql_fetch_assoc($get_blocks))
	{
		ob_start();
		if ($block['content'])
			echo $block['content'];
		else
		{
			$GLOBALS[$block['codename']] = $block;
			include ROOT.'blocks/'.$block['codename'].'/index.php';
		}
		$output[($block['side'] == 'R' ? 'right' : 'left')][$block['codename']] = array(
			'name' => $GLOBALS[$block['codename']]['name'] ? $GLOBALS[$block['codename']]['name'] : $block['name'],
			'codename' => $block['codename'],
			'subcodename' => $GLOBALS[$block['codename']]['subcodename'],
			'header' => $block['header'],
			'content' => ob_get_contents());
		ob_end_clean();
	}
}
$output['blocks'] = ''; // Aby nie mieszało przy dodawaniu modułu, gdy ten sam moduł jest ostatnim blokiem po lewej stronie