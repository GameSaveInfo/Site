<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Location
 *
 * @author TKMAYN9
 */
 include_once 'AXmlData.php';
abstract class Location extends AXmlData {
    public $append = null;
    public $detract = null;
    public $only_for = null;
    public $deprecated = false;
    public $game_version = null;
	protected $loc_table;   
    public static $table_name = "game_locations";    
    protected static $loc_fields = array("game_version"=>array("string","game_version",true),
                                        "detract"=>array("string","detract",true),
                                        "append"=>array("string","append",true),
                                        "only_for"=>array("string","only_for",true),
                                        "deprecated"=>array("boolean","deprecated",true));

    protected function getId() {
        return $this->generateHash();
    }

    protected abstract function getSubFields();
    public function getFields() {
        return self::combine($this->getSubFields(),self::$loc_fields);
    }
    protected function getSubObjects() {}
    protected function getNodes() {}

    public function getConcatRowsFor($table,$id,$db) {
        return $db->Select(array($table=>'sub',self::$table_name=>'loc'),null,
        "game_version = '$id' AND sub.id = loc.id",$this->generateOrder());
    }

    function __construct($table,$parent_id) {
    	parent::__construct($table,$parent_id);
        $this->game_version = $parent_id;
	    $this->loc_table = self::$table_name;
    }
    
    public function newWriteToDb($con) {
        self::writeDataToDb($this,$this->loc_table,self::$loc_fields,null,$con, 'Writing Common Location information');
        self::writeDataToDb($this,$this->table,$this->getSubFields(),null,$con, 'Writing '.get_class($this).' to database');
    }
    
    
    private static $ev = null;
    public static function getEvDescription($name,$db) {
        if(is_null(self::$ev)) {
            self::$ev = array();
            $data = $db->Select("game_environment_variables",null,null,null);
            foreach($data as $row) {
                $desc =  $row->description."<br />\n";
                $paths = $db->Select("ev_paths",null,array("ev"=>$row->name),null);
                if(sizeof($paths)>0) {
                    $desc .= "Here are some common examples:<br/>";
                    $desc .= "<dl class=\"ev_example\">\n";
                    foreach($paths as $path) {
                        $desc .= "<dt>".GameVersion::getOsDescription($path->os,$db)."</dt>";
                        $desc .= "<dd>";
                        $desc .= $path->paths;
                        $desc .= "</dd>\n";
                    }                 
                    $desc .= "</dl>\n";
                }
                self::$ev[$row->name] = $desc;
            }
        }
        return self::$ev[$name];
    }
}

?>
