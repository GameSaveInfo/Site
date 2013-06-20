<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/* 
 * Description of Games
 *
 * @author Matthew Barbour
 */
class Games {
    private static $document;
    public static $games = array();

    public static $timestamp;
    function __construct() {
    }
    
    public static function GameCount() {
        return (sizeof(self::$games));
    }
    
    
    
    
    private static $game_criteria = "type NOT IN ('system') AND deprecated = 0";

    public static function getGamesForLetter($letter,$db) {
        if($letter=="numeric") {
            $criteria = "name REGEXP '^[0-9]'";
        } else {
            $criteria = "name like '".$letter."%'";
        }
        $criteria .= " AND ".self::$game_criteria;
        
        return $db->Select("games",null,$criteria,"name");
    }

    public static function getGameLetters($db) {
        $letters = $db->RunStatement("SELECT substr(name,1,1) as letter, COUNT(name) as count FROM games ".
                                    "WHERE ".self::$game_criteria." GROUP BY letter ORDER BY letter ASC");
        $return = array("numeric"=>0);
        foreach($letters as $letter) {
            if(!is_numeric($letter->letter)) {
                $return[strtoupper($letter->letter)] = $letter->count;
            } else {
                $return["numeric"] += $letter->count;                
            }
        }
        return $return;
    }

    public static function printGameCounts($db) {
        $data = $db->RunStatement("SELECT COUNT(DISTINCT games.name) AS count, type FROM games ".
                                    "WHERE ".self::$game_criteria." GROUP BY type"); 
       
       $counts = array();
        $i = 0;
        $count = 0;
        $game_counts = array();
        foreach($data as $row) {
            $counts[$row->type] = $row->count;
            $game_counts[$i] = number_format($row->count);
            if ($row->count == 1)
                $game_counts[$i] .= ' ' . $row->type;
            else
                $game_counts[$i] .= ' ' . $row->type . 's';
            $count += $row->count;
            $i++;
        }

//        $data = $db->RunStatement("SELECT COUNT(*) AS count, type FROM game_versions ver LEFT JOIN games gam ON ver.name = gam.name ".
  //                                  "WHERE ".self::$game_criteria); 
        
        $count_string = '';
        if ($count > 0) {
            for ($i = 0; $i < sizeof($game_counts); $i++) {
                $count_string .= $game_counts[$i];
                if ($i < sizeof($game_counts) - 2) {
                    $count_string .= ', ';
                } else if ($i < sizeof($game_counts) - 1) {
                    $count_string .= ' and ';
                }
            }
            if (sizeof($game_counts) > 1) {
                $count_string .= ' (' . number_format($count) . ' total)';
            }
         //   $count_string .= ' and '.$data[0]->count;
        } else {
            $count_string = "no games (for now)";
        }

        echo $count_string;        
    }

    public static function libxml_display_error($error) 
    { 
        $return = "<br/>\n"; 
        switch ($error->level) { 
            case LIBXML_ERR_WARNING: 
                $return .= "<b>Warning $error->code</b>: "; 
                break; 
            case LIBXML_ERR_ERROR: 
                $return .= "<b>Error $error->code</b>: "; 
                break; 
            case LIBXML_ERR_FATAL: 
                $return .= "<b>Fatal Error $error->code</b>: "; 
                break; 
        } 
        $return .= trim($error->message); 
        if ($error->file) { 
            $return .= " in <b>$error->file</b>"; 
        } 
        $return .= " on line <b>$error->line</b>\n"; 
        
        return $return; 
    } 

    public static function printSchemaErrors() {
        $errors = libxml_get_errors(); 
        foreach ($errors as $error) { 
            print self::libxml_display_error($error); 
        } 
        libxml_clear_errors(); 
    }

    public static function loadFromXml($xml_file,$schema) {
        include_once 'AXmlData.php';
        self::$timestamp = AXmlData::formatDate();        
        echo "Current timestamp: ".self::$timestamp."<br/>";
        echo 'Loading From XMl File: '.$xml_file;
        self::$document = new DOMDocument();
        self::$document->load($xml_file);
        
        libxml_use_internal_errors(true); 

        
        if(!self::$document->schemaValidate($schema)) {
            self::printSchemaErrors();
            throw new Exception("VALIDATION FAILED!!!!");
        
        
        }
        
        
        $nodes = self::$document->getElementsByTagName('programs')->item(0);

        require_once('Game.php');
        foreach ($nodes->childNodes as $node) {
            if ($node->localName == 'game' ||
        		$node->localName == 'system' ||
        		$node->localName == 'mod' ||
        		$node->localName == 'expansion' ) {
                
                $game = new Game();
                $game->loadXml($node);
                $name = $game->name;
                if(array_key_exists($name,self::$games)) {
                    foreach(self::$games[$name]->versions as $version) {
                        array_push($game->versions,$version);
                    }
                    //                    throw new Exception("DUPLICATE GAME NAME ".$name);

                }
                self::$games[$name] = $game;
		} else if($node->localName == '') {
		continue;
            } else {
		throw new Exception("ELEMENT ".$node->localName." NOT KNOWN");
		}
		echo "<details><summary>".$game->name."</summary><pre>";
		var_dump($game);
		echo "</pre></details>";
        }
    }

    public static function loadFromDb($db,$game_criteria = null,$version_criteria = null) {        
        $result = $db->Select("games",null,$game_criteria,"name");
        
        require_once('Game.php');
        require_once('GameVersion.php');
        
        if(!is_null($version_criteria)) {
            GameVersion::$version_criteria = $version_criteria;
        }
        

        foreach ($result as $row) {
            $game = new Game();
            $game->loadFromDb($row->name,$row,$db);
            $game->written = true;
            self::$games[$game->name] = $game;
        }
        
    }
    
    private static $replacing = false;
    
    public static function getGameVersion($name,$hash,$link) {
        GameVersion::$ignore_version_criteria = true;
        $game = self::getGame($name,$link);
        GameVersion::$ignore_version_criteria = false;

        foreach($game->versions as $version) {
            if($version->getId()==$hash) {
                return $version;
            }
        }

        GameVersion::$ignore_version_criteria = true;
        $game = self::getGame($name,$link,true);
        GameVersion::$ignore_version_criteria = false;
        foreach($game->versions as $version) {
            if($version->getId()==$hash) {
                return $version;
            }
        }
        
        
        throw new Exception($hash." not found");
    }
    
    public static function getGame($name,$link, $force = false) {
        if(!array_key_exists($name,self::$games)||$force) {
            $result = $link->Select('games',null,array("name"=>$name),null);
            
            if(sizeof($result)==0) {
                throw new Exception("GAME DATA FOR ".$name." NOT PRESENT");   
            } else {
                $game = new Game();
                $row = $result[0];
                $game->loadFromDb($row->name,$row,$link);
                $game->written = true;
                return $game;
            }
        }
        
        return self::$games[$name];
    }
    
    public static function writeGameToDb($name, $link, $merge = false) {
        echo '<hr/>';        
        $game = self::getGame($name,$link);
                
    	if($game->newWriteToDb($link,$merge)) {
            
            if($game->was_merged) {
                
                $game->updateTime($link);
                
            }
            return true;
        } else {
            return false;
        }
            
    }
    
    
    public static function writeToDb($con,$max_import,$replace = false) {
        $total_imported = 0;
        self::$replacing = $replace;
        foreach (self::$games as $game) {
            if($total_imported>=$max_import) {
                echo $total_imported." imported, stopping...";
                return;
            }
                echo $total_imported." imported thus far";
            if(self::writeGameToDb($game->name,$con,$replace)) {
                $total_imported++;
                
            }
		}
    }

}

?>
