<?php 

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of File
 *
 * @author TKMAYN9
 */
 include_once 'AXmlData.php';
include_once 'RegistryEntry.php';
class RegistryType extends AXmlData {
    public $type;
	public $entries = array();
    public $game_version;

    protected function getDescription() {
        return get_class($this).' ('.$this->type.')';
    }


	public static $table_name = "game_registry_types";

	function __construct($parent_id) {
		parent::__construct(self::$table_name,$parent_id);
        $this->game_version = $parent_id;
	}

    public function getId() {
        return $this->generateHash();
    }
    public function getFields() {
        return array("game_version"=>array("string","game_version",true),
                    "type"=>array("string","type",true));    
    }
    protected function getSubObjects() {
        return array("entries"=>"RegistryEntry");
    }
    protected function getNodes() {
        return array("entry"=>array("RegistryEntry","entries"));
    }
    public function getRowsFor($id,$db) {
        return $db->Select(self::$table_name,null,array("game_version"=>$id),$this->generateOrder());
    }

    
}

?>
