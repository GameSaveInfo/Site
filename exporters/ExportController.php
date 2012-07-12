<?php
ini_set('default_charset', 'UTF-8');
$folder =  dirname(__FILE__);
include_once $folder.'/../gamedata/AXmlData.php';
include_once $folder.'/../gamedata/Games.php';

class ExportController {
    
    protected $link;
    public function __construct($link) {
        $this->link = $link;
    }

    public function drawPage($exporter = null, $file = null) {
        if(is_null($exporter)) {
            $this->drawExporterList();
        } else {
            if(is_null($file)) {
                $this->drawFileList($exporter);
            } else {
                $this->exportFile($exporter,$file);
            }
        }
        
    }
    
    protected function drawExporterList() {
        echo "EXPORTER NOT SPECIFIED, DISPLAYING ALL AVAILABLE EXPORTERS:<br />";

        $result = $this->link->Select("xml_exporters",null,null,array("name"=>"asc"));
    
        echo "<ul>";
        foreach($result as $row) {
        echo '<li><a href="?exporter='.$row->name.'">'.$row->title.'</a></li>';
            $this->printFilesForExporter($row->name);
        }
        echo "</ul>";

    }
    
    private function printFilesForExporter($name) {
            $result_file = $this->link->Select("xml_export_files",null,
                                array("exporter"=>$name),
                                array("file"=>"asc"));
    
            echo "<ul>";
            foreach ($result_file as $row_file) {
                echo "<li><a href='?exporter=" . $name . "&file=" . $row_file->file . "'>" . $row_file->file . "</a></li>";
            }
            echo "</ul>";
    
    }
    
    protected function drawFileList($exporter) {
        echo "NO FILE SPECIFIED, DISPLAYING ALL FILES FOR EXPORTER ".$exporter;
                $this->printFilesForExporter($exporter);
    }
    
    
    
    protected function exportFile($exporter, $file) {
        $nocache = true;
        header("Content-Type:text/xml; charset=UTF-8'");
        $file = $_GET['file'];
        $cache = $this->link->Select("xml_cache",null,array("exporter"=>$exporter,"file"=>$file),null);
        if(!$nocache&&sizeof($cache)==1) {
            $cache = $cache[0];
            echo $cache->contents;
        } else {
            $result = $this->link->Select('xml_exporters',null,array("name"=>$exporter),array("name"=>'asc'));
            $row = $result[0];    
            $folder =  dirname(__FILE__);
            require_once $exporter.'.php';
            require_once $folder.'/../gamedata/Games.php';
            Games::loadFromDb($file,$exporter,$this->link);
            $comment = $this->link->Select('xml_export_files',"comment",array("exporter"=>$exporter,"file"=>$file),null);
            $comment = $comment[0];
            $exporter = new $row->name($comment->comment);
            $output = $exporter->export();        
            
            if(!$nocache)
                $db->Insert("xml_cache",array("exporter"=>$exporter,"file"=>$file,"contents"=>$output));
                
            echo $output;
        }



    }



}

?>