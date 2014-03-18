<?php
include_once "../headers.php";

include_once "../libs/gsi/api/APIController.php";

$export = new APIController($db);

$format = null;
if (isset($_GET['format'])) {
        $format = $_GET['format'];
}
$criteria = null;
if (isset($_GET['criteria'])) {
        $criteria = $_GET['criteria'];
}

$export->drawPage($format,$criteria);

?>
