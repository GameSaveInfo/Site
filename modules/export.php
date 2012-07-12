<?php
$game = $_GET['name'];
$exporter = $_GET['exporter'];

$game_data = new Game();
$game_data->loadFromDb($name, null);

$data = $this->runQuery("SELECT * FROM "
            ."xml_exporters ex"
            ." WHERE name = \"".$exporter."\"");

if($row = mysql_fetch_array($data)) {
    require_once 'shared/exporters/'.$row['file'];
    
    $xml = new DOMDocument();
    $xml->encoding = 'utf-8';
    $xml->formatOutput = true;
    $exporter = new Exporter();
    $exporter->xml = $xml;
    $node = $exporter->exportGameVersion($game_data, $version);
    $xml->appendChild($node);
    
    echo $xml->saveXML($node);

}



?>
