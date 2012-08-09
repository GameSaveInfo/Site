<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Location
 *
 * @author TKMAYN9
 */
 include_once 'AXmlData.php';
class LinkLocation extends AXmlData {
    public $path = null;
    public $game_version = null;
    
    public static $table_name = "game_link_locations";    
    
    
    function __construct($parent_id) {
		parent::__construct(self::$table_name,$parent_id);
        $this->game_version = $parent_id;
    }
    
    public function getId() {
    }
    
    public function getFields() {
        return array("game_version"=>array("string","game_version",true),
                    "path"=>array("string","path",true));    
    }
    
    protected function getSubObjects() {
    }
    
    protected function getNodes() {
    }
    
    public function getRowsFor($id,$db) {
        return $db->Select(self::$table_name,null,array("game_version"=>$id),$this->generateOrder());
    }

    
}

?>
