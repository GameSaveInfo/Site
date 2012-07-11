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
 include_once 'AFile.php';
class IdentifyingFile extends AFile {
    public $game_version = null;

	public static $table_name = "game_identifiers";

	function __construct($parent_id) {
		parent::__construct(self::$table_name,$parent_id);
        $this->game_version = $parent_id;
	}
    protected function getSubFields() {
        return array("game_version"=>array("string","game_version",true));
    }
    protected function getNodes() {
           
    }
    public function getRowsFor($id,$db) {
        return $db->Select(self::$table_name,null,array("game_version"=>$id),$this->generateOrder());
    }

}

?>
