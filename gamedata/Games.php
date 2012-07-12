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
                $return[$letter->letter] = $letter->count;
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
        } else {
            $count_string = "no games (for now)";
        }

        echo $count_string;        
    }


    public static function loadFromXml($xml_file,$schema) {
        echo 'Loading From XMl File: '.$xml_file;
        self::$document = new DOMDocument();
        self::$document->load($xml_file);
        
        if(!self::$document->schemaValidate($schema))
            throw new Exception("VALIDATION FAILED!!!!");
        
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
                if(array_key_exists($name,self::$games))
                    throw new Exception("DUPLICAT GAME NAME ".$name);
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

    public static function loadFromDb($file,$exporter,$db) {
        global $settings;
        if($file!=null) {
            $result = $db->Select("xml_export_files",null,
                                    array("file"=>$file,"exporter"=>$exporter),null);
            $row = $result[0];
            $criteria = "games.name = v.name AND ".$row->game_criteria;
            if(!is_null($row->version_criteria)) {
                $criteria .= " AND ".$row->version_criteria;
            }
        }
        //echo "SELECT *, games.name AS name, games.title AS title, games.comment AS comment FROM games, game_versions v WHERE ".$criteria." ORDER BY games.name";
        $result = $db->RunStatement("SELECT *, games.name AS name, games.title AS title, games.comment AS comment FROM games, game_versions v WHERE ".$criteria." ORDER BY games.name");
            
//            array("games","game_versions"=>"v"),
  //                              "*, g.title title, g.comment comment",
    //                            $criteria,
      //                          "g.name");

        require_once('Game.php');
        require_once('GameVersion.php');
        if(!is_null($row->version_criteria)) {
            GameVersion::$version_criteria .= $row->version_criteria;
        }
        

        foreach ($result as $row) {
            $game = new Game();
            $game->loadFromDb($row->name,$row,$db);
            $game->written = true;
            self::$games[$game->name] = $game;
        }
        
    }
    
    private static $replacing = false;
    
    public static function getGame($name,$link) {
        if(!array_key_exists($name,self::$games)) {
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
    
    
    public static function writeGameToDb($name, $link) {
        $game = self::getGame($name,$link);
        
        if($game->written) {
                echo '<details>';
                echo '<summary style="color:green">'.$game->title.' ('.$game->name.') (';
                echo 'ALREADY WRITTEN, SKIPPING)</summary>';
                echo '</details>';
                return;
        }
        
        $data = $link->Select("games",null,array("name"=>$name),null);      
        if(sizeof($data)>0) {
            if(self::$replacing) {
                echo '<details open="true">';
                echo '<summary style="color:red">'.$game->title.' ('.$game->name.') ';
                echo '(EXISTS, REPLACING)</summary>';
                
                $link->Delete("games",array("name"=>$game->name),"Deleting Existing Entry");
            	$game->newWriteToDb($link);
            } else {
                echo '<details>';
                echo '<summary style="color:green">'.$game->title.' ('.$game->name.') (';
                echo 'EXISTS, SKIPPING)</summary>';
            }
        } else {
            echo '<details open="true">';
            echo '<summary style="color:orange">'.$game->title.' ('.$game->name.') ';
            echo '('.$game->type.') ';
            echo '(ADDING)</summary>';
    		$game->newWriteToDb($link);
        }
        echo '</details>';
    }
    
    public static function writeToDb($con,$replace = false) {
        self::$replacing = $replace;
        foreach (self::$games as $game) {
            self::writeGameToDb($game->name,$con,$replace);
		}
    }

}

?>
