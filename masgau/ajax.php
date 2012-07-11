<?php
	include_once '../config.php';
    include_once 'headers.php';    
    if(isset($module)) {
        echo $module->ajax();
    }


?>
