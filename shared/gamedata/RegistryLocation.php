<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RegistryLocation
 *
 * @author TKMAYN9
 */
require_once 'Location.php';
class RegistryLocation extends Location {
    //put your code here
    
    public $root;
    public $key;
    public $value = null;

	public static $table_name = "game_location_registry_keys";
    
	function __construct($parent_id) {
		parent::__construct(self::$table_name,$parent_id);
	}
        protected function getIdCriteria() {}

        protected function getSubFields() {
            return array("root"=>array("string","root",true),
                        "key"=>array("string","key",true),
                        "value"=>array("string","value",true));
        }
    protected function getSubObjects() {}
    protected function getNodes() {}

    public function getRowsFor($id,$con) {
        return $this->getConcatRowsFor(self::$table_name,$id,$con);
    }


  
}

?>
