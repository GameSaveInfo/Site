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
include_once 'SaveFile.php';
class FileType extends AXmlData {
    public $name;
	public $files = array();
    public $game_version;

	public static $table_name = "game_file_types";

	function __construct($parent_id) {
		parent::__construct(self::$table_name,$parent_id);
        $this->game_version = $parent_id;
	}

    protected function getId() {
        return $this->generateHash();
    }
    public function getFields() {
        return array("game_version"=>array("string","game_version",true),
                    "type"=>array("string","name",true));    
    }
    protected function getSubObjects() {
        return array("files"=>"SaveFile");
    }
    protected function getNodes() {
        return array("save"=>array("SaveFile","files"));
    }
    public function getRowsFor($id,$db) {
        return $db->Select(self::$table_name,null,array("game_version"=>$id),$this->generateOrder());
    }

    
}

?>
