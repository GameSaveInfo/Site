<?php
ini_set('default_charset', 'UTF-8');
$folder =  dirname(__FILE__);
include_once $folder.'/../libs/gsi/AXmlData.php';
include_once $folder.'/../libs/gsi/Games.php';
require_once $folder.'/../libs/gsi/Game.php';

class APIController {
    
    protected $link;
    public function __construct($link) {
        $this->link = $link;
    }
    private $oss = null;
    private $medias = null;
    private $platforms = null;
    private $regions = null;
    
    protected $exporters = array();
    
               protected function loadFieldIntoArray($data,$name) {
                $output = array();
                foreach($data as $row) {
                    array_push($output,$row->$name);                
                }
                return $output;
            }

    
    public function drawPage($exporter = null, $criteria = null) {

        $data = $this->link->Select("version_operating_systems","name",null,"name");
        $this->oss = $this->loadFieldIntoArray($data,"name");
        $data = $this->link->Select("version_medias","name",null,"name");
        $this->medias = $this->loadFieldIntoArray($data,"name");
        $data = $this->link->Select("version_platforms","name",null,"name");
        $this->platforms = $this->loadFieldIntoArray($data,"name");
        $data = $this->link->Select("version_regions","name",null,"name");
        $this->regions = $this->loadFieldIntoArray($data,"name");
        
        $data = $this->link->Select("exporters",null,null,"name");
        foreach($data as $item) {
            $this->exporters[$item->name] = $item;
        }
        
        if(is_null($exporter)||!array_key_exists($exporter,$this->exporters)) {
            $this->drawExporterList();
        } else {
            echo $this->export($exporter,$criteria);
        }
    }
    
    
    
    protected function drawExporterList() {
        echo '<html><head>';
        echo '<META NAME="robots" CONTENT="noindex,nofollow,noarchive">';
        echo '<title>GameSave.Info API</title>';
        echo '</head><body>';
        function linkHere($address, $newline = true, $text = null) {                
            global $_SERVER;
            $address = "http://".$_SERVER["SERVER_NAME"].'/api/'.$address;
            if(is_null($text))
                $text = $address;
            echo '<a href="'.$address.'">'.$text.'</a>';
            if($newline)
                echo "<br/>";
        }
        echo '<h1>GameSave.Info API Instructions</h2>';
        
        echo "This API business is pretty easy. It all starts with ";
        linkHere("",false);
        echo ", which is what you're looking at right now.<br/>";
        echo "The first step is to pick an output format. These are the currently supported ones:<br />";
        
        echo "<ul>";
        foreach($this->exporters as $row) {
          echo '<li>';
          linkHere($row->name.'/',false,$row->name.' - '.$row->title);
          echo '</li>';
        }
        echo "</ul>";

        echo "To access all the data in a particular format, just append the name of the exporter to the url, like this:<br/>";
        linkHere("GameSaveInfo202/");
        echo "<br/>";
        
        echo "Odds are though that you probably don't want all the data at once.<br/>";

        echo "If you just want a specific game, you can just name it:<br/>";
        linkHere("GameSaveInfo202/DeusEx/");
        echo "<br/>";
        echo "To filter the output, you can add criteria to the end of the url. For instance, to filter to only expansions:<br/>";
        linkHere("GameSaveInfo202/expansion/");
        echo "<br/>";
        echo "Or to filter to only PS3 games:<br/>";
        linkHere("GameSaveInfo202/PS3/");
        echo "<br/>";
        echo "You can filter by game names with wildcards too. Actually it's just the asterisk, but it works:<br/>";
        linkHere("GameSaveInfo202/D*/");
        echo "<br/>";
        echo "You can also output only games updated since a certain date (this works with almost any PHP parseable date string):<br/>";
        linkHere("GameSaveInfo202/2012-01-01/");
        echo "<br/>";
        echo "You can combine criteria by adding more to the end of the URL, for instance this gets all the PS1 games in the USA region:<br/>";
        linkHere("GameSaveInfo202/PS1/USA/");
        echo "<br/>";
        echo "You can specify an excluding criteria by placing an exclamation mark before the criteria. This will output only the games that are NOT for Windows:<br/>";
        linkHere("GameSaveInfo202/!Windows/");
        echo "<br/>";
        echo "There are 6 different kind of criteria: game name,  game type, os, platform, media and region.<br/>";
        echo "If you use multiple criteria in the same category, they are treated in an OR fashion (for the SQL-savy, it's using a WHERE IN). <br/>";
        echo "This example gets all the game that are PS1 or PS2 or PS3:<br/>";
        linkHere("GameSaveInfo202/PS1/PS2/PS3/");
        echo "<br/>";
        echo "<table border=1>";
        echo "<tr><th colspan='5'>Here's a table of the criteria categories, and the usable values</th></tr>";
        echo "<tr><th>Type</th><th>OS</th><th>Platform</th><th>Media</th><th>Region (Most games don't use these)</th></tr>";
        echo "<tr>";
        function printIt($array) {
            echo "<td><ul>";
            foreach($array as $item) {
                echo "<li>".$item."</li>";
            }
            echo "</ul></td>";            
        }
        printIt(Game::$types);
        printIt($this->oss);
        printIt($this->platforms);
        printIt($this->medias);
        echo "<td>";
        foreach($this->regions as $region) {
            echo $region." ";
        }
        echo "</td>";
        echo "</tr>";
        echo "</table>";
        echo "<br/>";
        echo "There is also deprecated data in the database, it's filtered out of the API output by default, but if you really want it just add \"deprecated\" to the URL<br/>";
        linkHere("GameSaveInfo202/deprecated/");
        echo "<br/>";
        
        echo "Here's the top ten queries against the database!";
        echo "<ol>";
        $queries = $this->link->RunStatement("SELECT exporter, criteria, SUM(count) as sum FROM export_statistics GROUP BY criteria ORDER BY sum DESC");
        for($i = 0; $i < sizeof($queries) && $i < 10; $i++) {
            echo "<li>";
            $path = $queries[$i]->exporter."/";
            if($queries[$i]->criteria!="")
                $path .= $queries[$i]->criteria."/";
            
            linkHere($path);

            echo "(Queried ".$queries[$i]->sum." Times)</li>";
        }
        
        echo "</ol>";
        echo '</body></html>';
    }        
    
    protected function incrementExportAccessCount($cache) {
        $criteria = array("exporter"=>$cache->exporter,
                            "criteria"=>$cache->criteria,
                            "timestamp"=>$cache->timestamp);
        
        $result = $this->link->Select("export_statistics",null,$criteria,null);
        
        if(sizeof($result)==0) {
            $this->link->Insert("export_statistics",$criteria);            
        } else {
            $result = $result[0];            
            $this->link->Update("export_statistics",$criteria,array("count"=>$result->count+1));
        }

    }
    
    protected function export($exporter, $criteria = null,$comment = null, $date = null) {
        // If we're on the test server, then caching is disabling
        $url = $_SERVER["SERVER_NAME"];
        if(strstr($url,"tardis")!= -1) {
            $nocache = true;
        } else {
            $nocache = false;
        }
        // Programmatic override for cache dissabling!
        //$nocache = false;
        
        $cache_criteria = array("exporter"=>$exporter,"criteria"=>trim($criteria,'/'));
        
        $cache = $this->link->Select("export_cache",null,$cache_criteria,null);        
        if(!$nocache&&sizeof($cache)==1) {
            $last_date = $this->link->Select("update_history",null,null,"timestamp DESC");
            $last_date = $last_date[0];
	        $last_date = $last_date->timestamp;
        	$tmp_cache = $cache[0];
        	if($last_date>$tmp_cache->timestamp) {
        		$this->link->Delete("export_cache",$cache_criteria);
		        $cache = $this->link->Select("export_cache",null,$cache_criteria,null);        
        	}
        }
            $folder =  dirname(__FILE__);
        require_once $folder.'/exporters/'.$exporter.'.php';
        
        if(!$nocache&&sizeof($cache)==1) {
            $cache = $cache[0];           
            $this->incrementExportAccessCount($cache);
            header("Content-Type:".$exporter::$content_type."; charset=UTF-8'");
             echo $cache->contents;
        } else {
            $result = $this->link->Select('exporters',null,array("name"=>$exporter),array("name"=>'asc'));
            $row = $result[0];    
            
            require_once $folder.'/../libs/gsi/Games.php';
            require_once $folder.'/../libs/gsi/GameVersion.php';
            
            $game_criteria = array("deprecated"=>0);
            $version_criteria = array("deprecated"=>0);
            
            
            function addCriteria($array,$key,$value, $not = false) {
                if($not)
                    $key = "!".$key;
                    
                if(is_null($value)) {
                    $array[$key] = null;
                    return $array;
                }
                
                
                if(!array_key_exists($key,$array)) {
                    $array[$key] = array();
                
                }
                if(!is_array($array[$key])) {
                    $tmp = $array[$key];
                    $array[$key] = array( $tmp );
                }
                
                array_push($array[$key],$value);
                return $array;
            }
            
            
            if(!is_null($criteria)) {
                $args = array_filter(explode("/",$criteria));
                if(sizeof($args)>0) {
                    foreach($args as $arg) {
                        $not = false;
                        if(substr($arg,0,1)=="!") {
                            $arg = substr($arg,1);
                            $not = true;
                        }
                        
                        
                        $time = strtotime($arg);
                        if($time!=false) {
                            $date = AXmlData::formatDate($arg);
                            array_push($game_criteria, "updated >= '".$this->link->escapeString($date)."'");
                            //var_dump($game_criteria);
                            continue;
                        }
                        
                        
                        if($arg=="deprecated") {
                            //$game_criteria['deprecated'] = 1;
                            unset($game_criteria['deprecated']);
                            $version_criteria['deprecated'] = 1;
                        } else if(in_array($arg,Game::$types)) {
                            $game_criteria = addCriteria($game_criteria,'type',$arg,$not);
                        } else if(in_array($arg,$this->oss)) {
                            $version_criteria = addCriteria($version_criteria,'os',$arg,$not);
                            
                        } else if(in_array($arg,$this->medias)) {
                            $version_criteria = addCriteria($version_criteria,'media',$arg,$not);
                            
                        } else if(in_array($arg,$this->platforms)) {
                            $version_criteria = addCriteria($version_criteria,'platform',$arg,$not);
                            
                        } else if(in_array($arg,$this->regions)) {                
                            $version_criteria = addCriteria($version_criteria,'region',$arg,$not);
                            
                        } else if(in_array($arg,GameVersion::$id_fields)) {                
                            $version_criteria = addCriteria($version_criteria,$arg,null,!$not);
                            
                        } else  {
                            if(strstr($arg,"*")) {
                                $arg = str_replace("*","%",$arg);
                            }
                            
                            $game_criteria = addCriteria($game_criteria,'name',$arg,$not);

            //            } else {,'
              //              echo '<pre>';
                //            var_dump(array_filter(explode("/",$criteria)));
                  //          echo '</pre>';
                    //        throw new Exception("Unknown argument provided: " .$arg);
                        }
                    }
                        
                    //var_dump($game_criteria);
                }                    
            }
            
            
            Games::loadFromDb($this->link, $game_criteria, $version_criteria);
            
            if(Games::GameCount()==0) {
                $output = "No entries found for:\nGame Criteria: ".$this->link->buildCriteriaString($game_criteria)."\n".
                                "Version Criteria: ".$this->link->buildCriteriaString($version_criteria);
                return $output;
            }
            
            if(is_null($comment)) {
                $comment = "Game Criteria: ".$this->link->buildCriteriaString($game_criteria)."\n".
                                "Version Criteria: ".$this->link->buildCriteriaString($version_criteria);
            }
            
            $exp= new $exporter($comment,$date);
                        
            $output = $exp->export();
            
            if(!$exp->error_occured) {
                if(sizeof($cache)!=0)
             	    $this->link->Delete("export_cache",$cache_criteria);

                $this->link->Insert("export_cache",array("exporter"=>$exporter,"criteria"=>trim($criteria,'/'),"contents"=>$output));
                                    	        $cache = $this->link->Select("export_cache",null,$cache_criteria,null);        

                $this->incrementExportAccessCount($cache[0]);
            }
                
            header("Content-Type:".$exporter::$content_type."; charset=UTF-8'");

            return $output;
        }
    }



}

?>
