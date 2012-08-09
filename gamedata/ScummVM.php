<?php
require_once 'Location.php';
class ScummVM extends AXmlData {
    //put your code here
    public $game_version;
    public $name;
    
	public static $table_name = "game_scummvm";   
	function __construct($parent_id) {
		parent::__construct(self::$table_name,$parent_id);
        $this->game_version = $parent_id;
	} 
    public function getId() {}
    public function getFields() {
        return array("game_version"=>array("string","game_version",true),
                    "name"=>array("string","name",true));
    }
    protected function getSubObjects() {}
    protected function getNodes() {}
    
    public function getRowsFor($id,$db) {
        return $db->Select(self::$table_name,null,array("game_version"=>$id),$this->generateOrder());
    }
    
}

?>
