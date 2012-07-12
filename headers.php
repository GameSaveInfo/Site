<?php
    // Includes 
    $folder =  dirname(__FILE__);
    require_once $folder.'/../libs/geshi/geshi.php';
    include_once $folder.'/../config.php';

    global $gdb;
    $gdb = Databases::$gamesaveinfo;
    $gdb->connect();
    
    global $db;
    $db = $gdb;
?>
