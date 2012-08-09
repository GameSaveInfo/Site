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
abstract class AFile extends AXmlData {
    public $path;
    public $filename;

    private static $file_fields = array("path"=>array("string","path",true),
                                        "filename"=>array("string","filename",true));

	function __construct($table_name,$parent_id) {
		parent::__construct($table_name,$parent_id);
	}
    
    public function getId() {}
    protected abstract function getSubFields();
    public function getFields() {
        return self::combine(self::$file_fields,$this->getSubFields());
    }
    
    protected function getSubObjects() {}

    public function concat($all_files = null) {
        if(!is_null($this->path)) {
            if(!is_null($this->filename)) {
                return $this->path."\\".$this->filename;
            } else if(!is_null($all_files)) {
                return $this->path."\\".$all_files;
            }
            return $this->path;
        } else if(!is_null($this->filename)) {
            return $this->filename;
        } else if(!is_null($all_files)) {
            return $all_files;
        }
        return null;
    }

}

?>
