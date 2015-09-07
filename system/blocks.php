<?php
 // KioCMS - Kiofol Content Managment System
// includes/blocks.php

if ($kio->columns > 1 && $kio->show_blocks)
{
	$where = 'side = 0 AND';
	if ($kio->left || $kio->right)
	{
		if ($kio->left && !$kio->right)
			$where = 'side = "L" AND ';
		elseif (!$kio->left && $kio->right)
			$where = 'side = "R" AND ';
		else
			$where = '';
	}

	$kio->left = array();
	$kio->right = array();

	$_query = $sql->query('
		SELECT * FROM '.DB_PREFIX.'blocks
		WHERE '.$where.' type != 0
		ORDER BY position');
	while ($_row = $_query->fetch())
	{
		ob_start();
		$block->name = $_row['codename'];

		if ($_row['content']) echo $_row['content'];
		else
		{
			//$kio->blocks[$_row['codename']] = $_row;
			include ROOT.'blocks/'.$_row['codename'].'/'.$_row['codename'].'.block.php';
		}

		$kio->{($_row['side'] == 'R' ? 'right' : 'left')}[$_row['codename']] = array(
			'name' => $_row['name'] ? $_row['name'] : $block->name,
			'codename' => $_row['codename'],
			'subcodename' => $block->subcodename ? $block->subcodename : null,
			'header' => $_row['header'],
			'content' => ob_get_contents());
		ob_end_clean();
	}
}

if ($kio->left && $kio->right)
	$kio->columns = 3;
else if ($kio->left xor $kio->right)
	$kio->columns = 2;
else
	$kio->columns = 1;

$kio->blocks = ''; // Aby nie mieszało przy dodawaniu modułu, gdy ten sam moduł jest ostatnim blokiem po lewej stronie