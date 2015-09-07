<?php

// KioCMS - Kiofol Content Managment System
// blocks/searcher/index.php

class Searcher extends Block
{

	public function getContent()
	{
		$tpl = new PHPTAL('blocks/searcher/search_form.html');
		return $tpl->execute();
	}
}