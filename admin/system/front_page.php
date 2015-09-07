<?php
 // KioCMS - Kiofol Content Managment System
// admin/settings/front_page.php

defined('KioCMS') || include_once '../ajax.php';

echo '<form action="" method="post"><table class="form">
<tr class="form"><th><label for="f_start_page">Strona startowa</label></th><td><input type="text" name="start_page" value="'.$cfg->system['start_page'].'" class="big" id="f_start_page" /></td></tr><tr class="form2"><th>&nbsp;</th><td><input type="submit" name="save" value="Zapisz" class="button" /><input type="reset" value="R" class="button2" /></td></tr></table></form>';
?>