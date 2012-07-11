<?php
    $folder =  dirname(__FILE__);
    require_once $folder.'/../DBSettings.php';
    
    ini_set('default_charset', 'UTF-8');
	global $settings;
	$settings = new stdClass();
    $settings->game_db = "masgau_gamesave";
    $settings->masgau_db = "masgau_site";
?>
