<?php
    // Includes 
    $folder =  dirname(__FILE__);
    require_once $folder.'/../shared/libs/geshi/geshi.php';
    include_once $folder.'/../config.php';

    global $db;
    $db = Databases::$masgau;
    $db->connect();
    
    global $gdb;
    $gdb = Databases::$gamesaveinfo;
    $gdb->connect();

    if(isset($_GET["module"])) {
        $module = $_GET["module"];
    }
    
    if(isset($module)) {
        include_once 'modules/AModule.php';
        $module = AModule::LoadModule($module, $db);
    }

?>
