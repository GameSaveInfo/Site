<?php

require_once 'Location.php';
class GameLocation extends Location {
    // Identifiers
    public $name;
    public $os = null;
    public $platform = null;
    public $media = null;
    public $region = null;
    public $release = null;
    //public $episode = null;
        
    public $parent_game_version = null;
    
	public static $table_name = "game_location_parents";
	function __construct($parent_id) {
		parent::__construct(self::$table_name,$parent_id);
	}
    
    private function generateCriteria() {
        $criteria = array();
        foreach($this->getSubFields() as $field) {
            $name = $field[1];
            if($this->$name != null)
               $criteria[$name] = $this->$name;
        }
        return $criteria;
    }
    
    private function getParentGame($db,$criteria,$message = null) {
        
        $result = $db->Select(GameVersion::$table_name,null,$criteria,null,$message);
        if(sizeof($result)!=1)
            throw new Exception("BAD NUMBER OF PARENT CANDIDATES, NEEDS TO BE ONE, GOT ".sizeof($result));
        $row = $result[0];
        $this->parent_game_version = $row->id;
        return $row;
        
    }
    
    private function setGameId($con) {
        Games::writeGameToDb($this->name,$con);
        $this->getParentGame($con,$this->generateCriteria(),"Getting Parent ID");
    }
        
    public function getRowsFor($id,$con) {
        return $this->getConcatRowsFor(self::$table_name,$id,$con);
    }

    protected function getSubFields() {
        return array("name"=>array("string","name",true),
                    "os"=>array("string","os",true),
                    "platform"=>array("string","platform", true),
                    "media"=>array("string","media",true),
                    "region"=>array("string","region",true),
                    "release"=>array("string","release",true),
                    //"episode"=>array("string","episode",true)
                    );   
    }
    

    public function generateOrder() {
        return array("parent_game_version"=>"ASC");
    }    
    
    public function loadFromDb($id, $row, $con) {
        $parent = $this->getParentGame($con,array('id'=>$row->parent_game_version));
               
        $row =  self::combine($parent,$row);
                
        parent::loadFromDb($id, $row,$con);
             
    }

    
    public function newWriteToDb($con) {
        $this->setGameId($con);
        self::writeDataToDb($this,$this->loc_table,self::$loc_fields,null,$con, 'Writing Common Location information');
        self::writeDataToDb($this, $this->table,array(
            "parent_game_version"=>array("string","parent_game_version")),
        null,$con, 'Writing '.get_class($this).' to database');        
    }

}

?>
