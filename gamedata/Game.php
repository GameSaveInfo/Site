<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Game
 *
 * @author Matthew Barbour
 */
 include_once 'AXmlData.php';
class Game extends AXmlData {
    // Properties
    public $name = null;
    public $title;
    public $type;
    public $for = null;
    public $follows = null;
    public $deprecated = null;
	public $comment = null;

    public $added;
    public $updated;
    
    // Sub-objects
    public $versions = array();
    
    function __construct() {
    	parent::__construct("games",null);
    }
    
    
    public static $types = array("game","mod","system","expansion");
    
    public function writeToDb($con) {        
        if($this->for!=null) {
            Games::writeGameToDb($this->for,$con); 
        }
        if($this->follows!=null) {
            Games::writeGameToDb($this->follows,$con);                
        }

        return parent::writeToDb($con);
    }


    public function getId() {}
    public function getFields() {
        return array(   "name"=>        array("string", "name",true),
                        "title"=>       array("string", "title", false),
                        "type"=>        array("string", "type", false),
                        "for"=>         array("string", "for", false),
                        "follows"=>     array("string", "follows", false),
                        "added"=>       array("timestamp", "added", false),
                        "updated"=>     array("timestamp", "updated", false),
                        "deprecated"=>  array("boolean", "deprecated", false),
                        "comment"=>     array("string", "comment", false ));
    }
    protected function getSubObjects() {
        return array("versions"=>"GameVersion");
    }
    protected function getNodes() {
        return array("title"=>array("string","title"));   
    }
    
    public function loadXml($node) {
        $this->type = $node->localName;
        
        parent::loadXml($node);
        
        if(is_null($this->added)) {
            $this->added = Games::$timestamp;
        }
        if(is_null($this->updated)) {
            $this->updated = Games::$timestamp;
        }
    }    
    
    protected function existsInDb($con) {
        $data = $con->Select($this->table,"count(*) as count",array("name"=>$this->name),null);
        $data = $data[0];
        if($data->count>0) {
            return true;
        }
        return false;
    }
    public function getExtendedTitle() {
        $output = $this->title;
        if($this->type == 'mod' ) {
            $output .= " (". $this->for ." Mod)";
        }
        return $output;
    }
    public static function getExtendedTitleFor($row) {
        $output = $row->title;
        if($row->type == 'mod' ) {
            $output .= " (". $row->for ." Mod)";
        }
        return $output;
    }
    
    protected function getDescription() {
        return $this->title.' ('.$this->name.') ('.$this->type.')';
    }
    
    
    public $needs_time_updated = false;
    
    public function updateTime($con) {
        $con->Update($this->table,array("name"=>$this->name),array("updated"=>Games::$timestamp),"Updating Game's Updated Timestamp");
    }
    
    protected function loadSubNode($node, $subs = null) {
        $name = $node->localName;
        if($name=='version') {
            require_once 'GameVersion.php';
            $version = new GameVersion($this->name);
            $version->loadXml($node);
            array_push($this->versions,$version);

        } else {
            parent::loadSubNode($node);   
        }
    }

    public function getForTitle() {
        
    }

    public $was_merged = false;

    public static $total_added = 0;
    public static $total_updated = 0;


    public function newWriteToDb($con, $merge = null) {
        if($merge&&$this->existsInDb($con)) {
                echo '<details open="true">';
                echo '<summary style="color:blue">'.$this->getDescription();
                echo ' (EXISTS, MERGING)</summary>';
//                $this->deleteFromDb($con);
            //    $rv = $this->writeToDb($con, $merge);
                $rv = $this->writeSubToDb($con, $merge);
                echo '</details>';
                $this->was_merged = $rv;
                self::$total_updated += $rv;                
                return $rv;
        } else {
            $rv = parent::newWriteToDb($con,$merge);
            self::$total_added += $rv;
            return $rv;
        }        
    }

    public function getVersion($id) {
        foreach($this->versions as $version) {
            foreach(GameVersion::$id_fields as $field) {
                if($field=="name")
                    continue;
                
                if($id->$field!=$version->$field) {
                    continue;
                }
                return $version;
    
            }
        }
        
        throw new Exception("Specified game version not found");
    }

    public function getTitle() {
        
        $titles = array();
        foreach($this->versions as $version) {
            if(!array_key_exists($version->title,$titles)||$titles[$version->title]==null)
                $titles[$version->title] = 0;
            else
                $titles[$version->title]++;
        }
        $candidate = null;
        foreach(array_keys($titles) as $title) {
            if($titles[$title]>$candidate||$candidate==null) {
                $candidate = $title;
            }
        }
            
        return $candidate;
    }
    
    
}

?>
