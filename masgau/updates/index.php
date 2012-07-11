<?php
include_once "../headers.php";

include_once "MASGAUExport.php";

$export = new MASGAUExport($db,$gdb);

$exporter = null;
if (isset($_GET['version'])) {
    switch($_GET['version']) {
        case "1.1":
            $exporter = "MASGAU11";
            break;
        default:
            throw new Exception($_GET['version']." not known");
    }
}

if (isset($_GET['exporter'])) {
        $exporter = $_GET['exporter'];
}
$file = null;
if (isset($_GET['file'])) {
        $file = $_GET['file'];
}

$export->drawPage($exporter,$file);

?>
