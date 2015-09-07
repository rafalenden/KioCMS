<?php
 // KioCMS - Kiofol Content Managment System
// templates/Kiofol/blocks.php

// Main content
function m_begin($name, $codename = false, $subcodename = false)
{
	ob_start();
	echo "\n".'<div'.($codename ? ' id="'.$codename.'"' : '').($subcodename ? ' class="'.$subcodename.'"' : '').'><div class="m_header"><h1>'.$name.'</h1></div><div class="m_content">';
}
function m_end()
{
	global $m_content;
	echo '</div></div>';
	$m_content .= trim(ob_get_contents());
	ob_end_clean();
}

// Block
function b_begin($name, $codename, $header, $subcodename = false)
{
	global $cfg;
	echo "\n".'<div id="'.$codename.'"'.($subcodename ? ' class="'.$subcodename.'"' : '').'>';
	if ($header && $cfg->system['blocks_headers'])
	{
		echo '<div class="b_header"><h4>'.$name.'</h4></div>';
	}
	echo '<div class="b_content">';
}
function b_end()
{
	echo '</div></div>';
}
?>