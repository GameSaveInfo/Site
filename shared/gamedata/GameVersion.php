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

    public $file_types = array();
    public $identifiers = array();
        
    public $contributors = array();

	public static $table_name = "game_versions";


    function __construct($parent_id) {
    	parent::__construct(self::$table_name,$parent_id);
	$this->name = $parent_id;
    }
    
    protected function getId() {
        return $this->generateHash();
    }
    public function getFields() {
        return array("name"=>   array("string","name",true),
                    "os"=>      array("string","os",true),
                    "platform"=>array("string","platform",true),
                    "region"=>  array("string","region",true),
                    "media"=>   array("string","media",true),
                    "release"=> array("string","release",true),
                    //"episode"=> array("string","episode",true),
                    "title"=>   array("string","title",false),
                    "comment"=> array("string","comment",false),
                    "restore_comment"=>array("string","restore_comment",false),
                    "virtualstore"=>array("string","virtualstore",false),
                    "detect"=>  array("string","detect",false));    
    }
    protected function getSubObjects() {
        return array(   "locations"=>null,
                        "scumm_vm"=>"ScummVM",
                        "ps_codes"=>"PlayStationCode",
                        "identifiers"=>"IdentifyingFile",
                        "file_types"=>"FileType");    
    }
    protected function getNodes() {
        return array(   "scummvm"=>     array("ScummVM","scumm_vm"),
                        "ps_code"=>     array("PlayStationCode","ps_codes"),
                        "locations"=>   array("collection","locations"),
                        "path"=>        array("PathLocation","path_locations"),
                        "parent"=>      array("GameLocation","game_locations"),
                        "registry"=>    array("RegistryLocation","registry_locations"),
                        "shortcut"=>    array("ShortcutLocation","shortcut_locations"),
                        "files"=>       array("FileType","file_types"),
                        "identifier"=>  array("IdentifyingFile","identifiers"),
                        "contributor"=> array("string","contributors"));
    }
    
    public static $version_criteria = null;
    
    public function getRowsFor($id,$db) {
        $crit = "`name` = '".$id."'";
        if(!is_null(self::$version_criteria))
            $crit .= " AND " . self::$version_criteria;
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
    
    public function newWriteToDb($con) {
        parent::newWriteToDb($con);
        
        foreach($this->contributors as $contributor) {
            $data = $con->Select("game_contributors",null,array("name"=>$contributor),null);
                            
            if(sizeof($data)==0) {
                $con->Insert('game_contributors', array('name'=>$contributor),'Contributor is new, adding');
            }
            $con->Insert('game_contributions', 
                    array('game_version'=>$this->getId(),
                        'contributor'=>$contributor),"Writing contribution by " . $contributor . " to database");
        }
    }


    private static $id_fields = array("os","platform","region","media");
    public function getVersionTitle() {
        $header = "";
        foreach(self::$id_fields as $field) {
            if($this->$field!=null) {
                $header .= $this->$field." ";
            }
        }
        

        $header .=' Version';

        if ($this->title != null)
            $header .= ' (' . $this->title . ')';
        return $header;
    }


    public function getVersionString() {
        $return_me = $this->concatHelper(array($this->name,$this->os,$this->media,$this->platform,
                                        $this->region,$this->release));
            
        return $return_me;
    }

}

?>
