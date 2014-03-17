<?php
require_once 'libs/smj/ASiteMap.php';
require_once 'headers.php';
class GSISiteMap extends ASiteMap {
    public function __construct() {
        parent::__construct("http://gamesave.info/");
        $this->addURL($this->root_url, new DateTime(), "monthly", 0.2);
        
        $this->addURL($this->root_url."xml_format.php", new DateTime(), "monthly", 0.2);
        $this->addURL($this->root_url."api/", new DateTime(), "monthly", 0.2);
        
        global $db;
        $data = $db->Select("games",array("name","title"),null,array("name"));
        foreach($data as $row) {
        $this->addURL($this->root_url.$row->name."/", new DateTime(), "monthly", 0.5);
            
        }

    }

}

?>
