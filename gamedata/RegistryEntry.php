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
class RegistryEntry extends AXmlData {
    public $root;
	public $key;
    public $value = null;
    

    public $type;

	public static $table_name = "game_registry_entries";

	function __construct($parent_id) {
		parent::__construct(self::$table_name,$parent_id);
        $this->type = $parent_id;
	}

    public function getId() {
        return null;
    }
    public function getFields() {
        return array("type"=>array("string","type",true),
                    "root"=>array("string","root",true),
                    "key"=>array("string","key",true),
                    "value"=>array("string","value",true));    
    }
    protected function getSubObjects() {
        return array();
    }
    protected function getNodes() {
        return array();
    }
    public function getRowsFor($id,$db) {
        return $db->Select(self::$table_name,null,array("type"=>$id),$this->generateOrder());
    }

    
}

?>
