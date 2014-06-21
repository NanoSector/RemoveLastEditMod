<?php

if (!defined('SMF') && file_exists(dirname(__FILE__) . '/SSI.php'))
    require_once(dirname(__FILE__) . '/SSI.php');
elseif (!defined('SMF'))
    die('<b>Error:</b> Cannot install - please verify you put this in the same place as SMF\'s index.php.');
    
remove_integration_function('integrate_pre_include', '$sourcedir/Subs-RemoveLastEditMod.php');
remove_integration_function('integrate_actions', 'rlem_actions');
remove_integration_function('integrate_load_permissions', 'rlem_permissions');

?>