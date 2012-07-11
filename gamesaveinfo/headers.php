<?php
    // Includes 
    $folder =  dirname(__FILE__);
    require_once $folder.'/../shared/libs/geshi/geshi.php';
    include_once $folder.'/../config.php';

    global $gdb;
    $gdb = Databases::$gamesaveinfo;
    $gdb->connect();
    
    global $db;
    $db = $gdb;
?>
