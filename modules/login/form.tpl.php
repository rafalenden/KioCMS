<?php
 // KioCMS - Kiofol Content Managment System
// includes/login/form.tpl.php

defined('KioCMS') || exit;
echo '<form action="#" method="post"><table class="form"><tbody>
<tr class="top"><th><label'.($errors[0] || $errors[1] ? ' class="error"' : '').'><span class="required">*</span>'.$lang_login['USERNAME'].' <input type="radio" name="ss_by_email" value="0" '.(!$form['by_email'] ? 'checked="checked" ' : '').'/></label></th>
<td rowspan="2"><input class="big" type="text" name="ss_identify" value="'.$form['identify'].'" /><div class="tip"><a href="'.local_url.'registration">Przejdź do rejestracji</a></div></td></tr>
<tr><th><label'.($errors[2] || $errors[3] ? ' class="error"' : '').'><span class="required">*</span>'.$lang_login['EMAIL'].' <input type="radio" name="ss_by_email" value="1" '.($form['by_email'] ? 'checked="checked" ' : '').'/></label></th></tr>
                                                            
<tr><th><label for="f_ss_password"'.($errors[4] || $errors[5] ? ' class="error"' : '').'><span class="required">*</span>'.$lang_login['PASSWORD'].'</label></th><td><input class="big" type="password" name="ss_password" id="f_ss_password" value="'.$form['password'].'" /><div class="tip"><a href="'.local_url.'password">Przywróć hasło</a></div></td></tr>

<tr class="bottom"><th>&nbsp;</th><td><input class="button submit" type="submit" name="login" value="'.$lang_login['LOGIN'].'" /><input type="reset" value="'.$GLOBALS['lang_system']['RESET'].'" class="button reset" title="'.$GLOBALS['lang_system']['RESET_TITLE'].'" /></td></tr></tbody></table></form>';  
?>