<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 
/**
 * Description of GameVersion
 *
 * @author Matthew Barbour
 */
include_once 'AXmlData.php';
class GameVersion extends AXmlData {

    // Tag properties
    public $title = null;
    
    // Identifiers
    public $name;
    public $os = null;
    public $platform = null;
    public $media = null;
    public $region = null;
    public $release = null;
    public $episode = null;
    public $type = null;
    public $revision = null;
    
    public $deprecated = 0;
    
    public $virtualstore = null;
    public $detect = null;

    public $comment = null;
    public $restore_comment = null;

    // Location objects
    public $locations = array();
    public $path_locations = array();
    public $registry_locations = array();
    public $shortcut_locations = array();
    public $game_locations = array();
    
    public $scumm_vm = array();
    public $ps_codes = array();

    public $link_locations = array();

    public $file_types = array();
    public $identifiers = array();

    public $registry_types = array();

    public $contributors = array();

	public static $table_name = "game_versions";


    function __construct($parent_id) {
    	parent::__construct(self::$table_name,$parent_id);
	    $this->name = $parent_id;
    }
    
    public function getId() {
        return $this->generateHash();
    }
    
    public function versionMatch($other) {
        if($this->os!=$other->os)
            return false;
        if($this->platform!=$other->platform)
            return false;
        if($this->media!=$other->media)
            return false;
        if($this->region!=$other->region)
            return false;
        if($this->release!=$other->release)
            return false;
        if($this->revision!=$other->revision)
            return false;
        
        return true;
    }
    
    private static $os_names = null;
    public static function getOsDescription($name,$db) {
        if(is_null(self::$os_names)) {
            $result = $db->Select("version_operating_systems",null,null,null);
            self::$os_names = array();
            foreach($result as $os) {
                self::$os_names[$os->name] = $os->description;
            }
        }
        return self::$os_names[$name];
    }
        
    public static $id_fields = array("name","os","platform","media","region","release", "revision", "type");

    public function getFields() {
        $return_me = array();
        foreach(self::$id_fields as $field) {
            switch($field) {
                case "revision":
                    $return_me[$field] = array("integer",$field,true);
                    break;
                default:
                    $return_me[$field] = array("string",$field,true);
                    break;
            }
        }
        //"episode"=> array("string","episode",true),
        $return_me["title"] = array("string","title",false);
        $return_me["comment"] = array("string","comment",false);
        $return_me["restore_comment"] = array("string","restore_comment",false);
        $return_me["virtualstore"] = array("string","virtualstore",false);
        $return_me["detect"] = array("string","detect",false);
        $return_me["deprecated"] = array("boolean","deprecated",false);
        return $return_me;
    }
    
    protected function getSubObjects() {
        return array(   "locations"=>null,
                        "scumm_vm"=>"ScummVM",
                        "ps_codes"=>"PlayStationCode",
                        "file_types"=>"FileType",
                        "registry_types"=>"RegistryType",
                        "link_locations"=>"LinkLocation",
                        "identifiers"=>"IdentifyingFile");    
    }
    
    protected function getNodes() {
        return array(   "scummvm"=>     array("ScummVM","scumm_vm"),
                        "ps_code"=>     array("PlayStationCode","ps_codes"),
                        "locations"=>   array("collection","locations",array(
                            "path"=>        array("PathLocation","path_locations"),
                            "parent"=>      array("GameLocation","game_locations"),
                            "registry"=>    array("RegistryLocation","registry_locations"),
                            "shortcut"=>    array("ShortcutLocation","shortcut_locations"))),
                        "registry"=>    array("RegistryType","registry_types"),
                        "files"=>       array("FileType","file_types"),
                        "linkable"=>    array("LinkLocation","link_locations"),
                        "identifier"=>  array("IdentifyingFile","identifiers"),
                        "contributor"=> array("string","contributors"));
    }
    
    public static $version_criteria = null;
    public static $ignore_version_criteria = false;
    public function getRowsFor($id,$db) {
        if(!is_null(self::$version_criteria)&&!self::$ignore_version_criteria)
            $crit = self::$version_criteria;
        else
            $crit = array();
            
        $crit['name'] = $id;
        return $db->Select(self::$table_name,null,$crit,$this->generateOrder());
    }
    
    
    public function loadFromDb($id, $row, $db) {
        parent::loadFromDb($id,$row,$db);
        
        $data = $db->Select("game_contributions","contributor",array("game_version"=>$row->id),null);
        foreach ($data as $row) {
            array_push($this->contributors,$row->contributor);
        }
        
        
        $loc_types = array("PathLocation"=>"path_locations","RegistryLocation"=>"registry_locations",
                            "ShortcutLocation"=>"shortcut_locations","GameLocation"=>"game_locations");
        foreach(array_keys($loc_types) as $loc_type) {
            require_once $loc_type.'.php';
            $dummy = new $loc_type(null);
            $data = $dummy->getRowsFor($id,$db);
            
            foreach ($data as $row) {
                $location = new $loc_type($id);
                $location->loadFromDb($row->id, $row,$db);
                $var = $loc_types[$loc_type];
                array_push($this->$var,$location);
                array_push($this->locations,$location);
            }
        }
    }
    
    protected function loadDbSubObject($row,$key,$con) {
        if($key=='locations') {
            
        } else {
            parent::loadDbSubObject($row, $key,$con);
        }
    }
    public function shouldBeOpen() {
     return false;   
    }

    
    public function writeToDb($con) {
        echo '<hr/>';

        if(parent::writeToDb($con)) {        
            foreach($this->contributors as $contributor) {
                $data = $con->Select("game_contributors",null,array("name"=>$contributor),null);
                                
                if(sizeof($data)==0) {
                    $con->Insert('game_contributors', array('name'=>$contributor),'Contributor is new, adding');
                }
                $con->Insert('game_contributions', 
                        array('game_version'=>$this->getId(),
                            'contributor'=>$contributor),"Writing contribution by " . $contributor . " to database");
            }
            return true;
        }
        return false;
    }

    public function writeSubToDb($con, $merge = false) {
        return parent::writeSubToDb($con,false);
    }

    private static $title_fields = array("os","platform","region","media");
    public function getVersionTitle() {
        $header = "";
        foreach(self::$title_fields as $field) {
            if($this->$field!=null) {
                $header .= $this->$field." ";
            }
        }
        

        $header .=' Version';

        if ($this->title != null)
            $header .= ' (' . $this->title . ')';
        return $header;
    }

    protected function getDescription() {
        return get_class($this).' ('.$this->getVersionIdFieldString().')';
    }


    public function getVersionIdFieldString() {
        $return_me = $this->concatHelper(array($this->os,$this->media,$this->platform,
                                        $this->region,$this->release));
            
        return $return_me;
    }
    public function getVersionString() {
        $return_me = $this->concatHelper(array($this->name,$this->getVersionIdFieldString()));
            
        return $return_me;
    }






}

?>
