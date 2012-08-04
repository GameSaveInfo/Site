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
class Exclude extends IncludeFile {    
    public $parent = null;

	function __construct($parent_id, $type_id) {
		parent::__construct(self::$table_name,$type_id);
        $this->type = $type_id;
        $this->parent = $parent_id;        
	}
    
    public function getRowsFor($id,$db) {
        return $db->Select(self::$table_name,null,array("parent"=>$id),$this->generateOrder());
    }

    protected function getSubFields() {
        $fields = parent::getSubFields();
        $fields["parent"] = array("string","parent",true);
        return $fields;
    }
    protected function getSubObjects() {
    }


}
?>