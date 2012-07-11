<?php
include_once "../headers.php";

include_once "../../shared/exporters/ExportController.php";

$export = new ExportController($db);

$exporter = null;
if (isset($_GET['exporter'])) {
        $exporter = $_GET['exporter'];
}
$file = null;
if (isset($_GET['file'])) {
        $file = $_GET['file'];
}

$export->drawPage($exporter,$file);

?>
