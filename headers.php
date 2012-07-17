<?php
    // Includes 
    $folder =  dirname(__FILE__);
        if(!is_dir($folder.'/libs/')) {
        if(is_dir($folder.'/../libs/')) {
            // Means there is a lib folder up above!
            symlink($folder.'/../libs/',$folder.'/libs');
        } else {
            throw new Exception("Lib folder not locatable!");
        }
    }
    require_once $folder.'/libs/geshi/geshi.php';
    include_once $folder.'/../config.php';

    global $gdb;
    $gdb = Databases::$gamesaveinfo;
    $gdb->connect();
    
    global $db;
    $db = $gdb;
?>
