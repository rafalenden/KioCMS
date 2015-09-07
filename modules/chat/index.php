<?php
    /// KioCMS 1.0.0 - Kiofol Content Managment System
   /// Copyright © 2008 by Kiofol Software
  /// License: GNU General Public License
 /// Author: Rafał "Endzio" Enden
/// modules/chat/index.php

defined('KioCMS') || exit;

$lang = include_lang('modules/chat');
$chat = get_config('chat');

$title[] = 'Chat';
$module['columns'] = 2;

//file_exists(root_dir.'modules/chat/data/private/cache/'.md5(__FILE__).'.php') ? unlink(root_dir.'modules/chat/data/private/cache/'.md5(__FILE__).'.php') : '';

require_once root_dir.'modules/chat/src/phpfreechat.class.php';
$params = array();
$params["title"] = "Quick chat";

$params["isadmin"] = strtolower($user['username']) == 'test' ? true : false; // do not use it on production servers ;)
$params["serverid"] = md5(__FILE__); // calculate a unique id for this chat
$params["nick"]     = defined('LOGGED') ? $user['username'] : 'guest'.rand();  // setup the intitial nickname
$params["debug"] = false;
$params["max_msg"] = 0;
$params["frozen_nick"] = true;

$params["theme_url"] = local_dir."modules/chat/data/public/themes";
$params["theme_default_url"] = local_dir."modules/chat/data/public/themes";
$params["server_script_url"] = local_url."chat/"; // Must ending with slash (/)
$params["client_script_path"] = root_dir.'modules/chat/index.php';
$params["data_public_url"] = local_dir."modules/chat/data/public";
$params["prototypejs_url"] = local_dir."modules/chat/data/public/js/prototype.js";



$pfc = new phpFreeChat( $params );

echo '<div style="text-align: left;">';
$pfc->printChat();
echo '</div>';
?>