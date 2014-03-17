<?php
    // Includes 
    require_once 'libs/geshi/src/geshi.php';

    $folder =  dirname(__FILE__);
    require_once $folder.'/../DBSettings.php';
    
    ini_set('default_charset', 'UTF-8');
	global $settings;
	$settings = new stdClass();
    $settings->game_db = "masgau_gamesave";
    $settings->masgau_db = "masgau_site";
    global $test_mode;
    
    $url = $_SERVER["SERVER_NAME"];
    if(strstr($url,"tardis")) {
            $test_mode = true;
    } else {
            $test_mode = false;
    }
    

    global $gdb;
    $gdb = Databases::$gamesaveinfo;
    $gdb->connect();
    
    global $db;
    $db = $gdb;
?>
