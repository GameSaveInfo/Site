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
class SaveFile extends AFile {
    public $type = null;
    
    public $modified_after = null;
	public $excepts = array();

	public static $table_name = "game_files";

	function __construct($parent_id) {
		parent::__construct(self::$table_name,$parent_id);
        $this->type = $parent_id;
	}
    public function getRowsFor($id,$db) {
        return $db->Select(self::$table_name,null,array("type"=>$id,"parent"=>null),$this->generateOrder());
    }
    
    protected function loadDbField($name, $value) {
        if($name=="modified_after") {
            if($value!=null)
                $this->$name = self::formatDate($value);
        } else {
            parent::loadDbField($name,$value);   
        }
    }

    protected function getId() {
        return $this->generateHash();
    }
    protected function getSubFields() {
        return array("type"=>array("string","type",true),
                    "modified_after"=>array("string",'modified_after',true));
    }
    protected function getSubObjects() {
        return array("excepts"=>"ExceptFile");    
    }
    protected function getNodes() {}

    protected function loadDbSubObject($id, $key, $con) {
        $subs = $this->getSubObjects();
        $class = $subs[$key];
        require_once $class.'.php';
        $dummy = new $class(null,null);
        $data = $dummy->getRowsFor($id,$con);
        foreach ($data as $row) {
            $obj = new $class($id,$this->type);
            $obj->loadFromDb($row->id,$row,$con);
            array_push($this->$key,$obj);
        }
    }

    protected function loadSubNode($node) {
        $name = $node->localName;
        if($name=='except') {
            include_once 'ExceptFile.php';
            $except = new ExceptFile($this->getId(), $this->type);
    		$except->loadXml($node);
    		array_push($this->excepts,$except);
            return $except;
        } else {
            parent::loadSubNode($node);
        }        
    }
}

?>
