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

    public function getId() {
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
    
    public function writeToDb($con) {
        self::writeDataToDb($this,$this->loc_table,self::$loc_fields,$con, 'Writing Common Location information');
        self::writeDataToDb($this,$this->table,$this->getSubFields(),$con, 'Writing '.get_class($this).' to database');
        
    }
    
    
    private static $evs = null;
    public static function getEvDescription($name,$db) {
        if(is_null(self::$evs)) {
            self::$evs = array();
            $data = $db->Select("game_environment_variables",null,null,null);
            foreach($data as $row) {
                $desc =  $row->description."<br />\n";
                $paths = $db->Select("ev_paths",null,array("ev"=>$row->name),null);
                if(sizeof($paths)>0) {
                    $desc .= "Here are some common examples:<br/>";
                    $desc .= "<dl class=\"ev_example\">\n";
                    foreach($paths as $path) {
                        $desc .= "<dt>".GameVersion::getOsDescription($path->os,$db)."</dt>";
                        foreach(explode("\n", $path->paths) as $example) {
                            $desc .= "<dd>";
                            $desc .= $example;
                            $desc .= "</dd>\n";
                        }
                    }                 
                    $desc .= "</dl>\n";
                }
                self::$evs[$row->name] = $desc;
            }
        }
        return self::$evs[$name];
    }
    
    
    public function ajdustLocation($parent_location) {
        return self::staticAdjustLocation($this,$parent_location);
    }
    
    public static function endsWith($haystack,$needle) {
        $substr = substr($haystack,strlen($haystack)-strlen($needle),strlen($needle));
        return $needle = $substr;
    }
    public static function remove($string,$remove) {
        $str = trim(substr($string,0,strlen($string)-strlen($remove)),'\\');
        
        if($str=="")
            return null;
        
        return $str;
    }
    
    public static function staticAdjustLocation($location, $parent_location) {
        if(!is_null($parent_location)) {
            $class = get_class($location);
            $new = new $class($location->parent_id);
            
            foreach(array_keys($location->getFields()) as $field) {
                $new->$field = $location->$field;
            }
            
            
            if(!is_null($parent_location->detract)) {
                if(get_class($new)=="PathLocation") {
                    $new->path = self::remove($new->path,$parent_location->detract);
            $new->path = trim($new->path,'\\');
                } else {
                    if(!is_null($new->append)) {
                        if(self::endsWith($new->append,$parent_location->detract)) {
                            $new->append = self::remove($new->append,$parent_location->detract);
                        } 
                    } else if(is_null($new->detract)) {
                        $new->detract = $parent_location->detract;
                    }
                    
                }
            }
            if(!is_null($parent_location->append)) {
                if(get_class($new)=="PathLocation") {
                    $new->path .= "\\".$parent_location->append;
                    $new->path = trim($new->path,'\\');
                } else {
                    if(is_null($new->append)) {
                        $new->append = $parent_location->append;
                    }
                }
    
            }
            if(!is_null($new->append))
                $new->append = trim($new->append,'\\');
            if(!is_null($new->detract))
                $new->detract = trim($new->detract,'\\');
        }
        return $new;
    }


    
}

?>
