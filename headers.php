<?php
    // Includes 
    $folder =  dirname(__FILE__);
    require_once $folder.'/libs/geshi/geshi.php';
    include_once $folder.'/../config.php';

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
