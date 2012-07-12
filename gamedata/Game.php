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
    // Sub-objects
    public $versions = array();
    
    function __construct() {
    	parent::__construct("games",null);
    }
    
    public $written = false;
    
    public function newWriteToDb($con) {
        
        if($this->for!=null) {
            Games::writeGameToDb($this->for,$con); 
        }
        if($this->follows!=null) {
            Games::writeGameToDb($this->follows,$con);                
        }

        parent::newWriteToDb($con);
        $this->written = true; // Yay!
    }


    protected function getId() {}
    public function getFields() {
        return array(   "name"=>        array("string", "name",true),
                        "title"=>       array("string", "title", false),
                        "type"=>        array("string", "type", false),
                        "for"=>         array("string", "for", false),
                        "follows"=>     array("string", "follows", false),
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
    }    
    
    protected function loadSubNode($node) {
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
