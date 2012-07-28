<?php
    // Includes 
    $folder =  dirname(__FILE__);
    require_once $folder.'/libs/geshi/geshi.php';
    include_once $folder.'/../config.php';

    global $test_mode;
    switch(substr($_SERVER["SERVER_NAME"],0,3)) {
        case "192":
        case "sag":
        case "tes":
            $test_mode = true;
            break;
        default:
            $test_mode = false;
            break;
    }

    global $gdb;
    $gdb = Databases::$gamesaveinfo;
    $gdb->connect();
    
    global $db;
    $db = $gdb;
?>
