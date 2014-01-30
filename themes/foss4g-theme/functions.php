<?php
/**
 * Foss4G-PDX Theme FUNctions
 *
 * These are built for fun. So ... have some!
 * Don't edit this file unless you know what you are doing.
 * Please use the .php files within the /functions directory to add functionality.
**/

/* Defining the functions directory */
define("FUN", get_template_directory() . "/functions");

/* Bring those files into this file, which is automaticall read by Wordpress */
require_once FUN . "/wp_bootstrap_navwalker.php";
require_once FUN . "/general.php";
require_once FUN . "/widgets.php";
require_once FUN . "/customizer.php";
require_once FUN . "/posts.php";