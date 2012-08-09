<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PlayStationCode
 *
 * @author Matthew Barbour
 */
 
include_once 'AXmlData.php';
class PlayStationCode extends AXmlData {
    public $game_version;
    public $suffix;
    public $prefix;
    public $append;
    public $type;
    public $disc;
    
	public static $table_name = "game_playstation_codes";


    function __construct($parent_id) {
	    parent::__construct(self::$table_name,$parent_id);
        $this->game_version = $parent_id;
	}

    public function getRowsFor($id,$db) {
        return $db->Select(self::$table_name,null,array("game_version"=>$id),$this->generateOrder());
    }
    
    protected function loadDbField($name, $value) {
        if($name=="suffix") {
            $this->$name = str_pad($value, 5, "0", STR_PAD_LEFT);            
        } else {
            parent::loadDbField($name,$value);
        }
    }


    public function getId() {}
    public function getFields() {
        return array("game_version"=>array("string","game_version",true),
                    "prefix"=>array("string","prefix",true), 
                    "suffix"=>array("string","suffix",true), 
                    "append"=>array("string","append",true), 
                    "type"=>array("string","type",true), 
                    "disc"=>array("integer","disc",true) );
    }
    protected function getSubObjects() {}
    protected function getNodes() {}
 
}

?>
