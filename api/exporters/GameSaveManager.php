<?php
require_once 'AExporter.php';
class GameSaveManager extends AExporter {
    public static $content_type = "text/plain";
    
    
    private $titles = array();
    
    private $doc = "";

    public function __construct($comment = null, $date = null) {
        parent::__construct();
        date_default_timezone_set("UTC");
        
        if(!is_null($comment)) {
            foreach(explode("\n",$comment) as $com) {
                echo "## ".$com."\n";
            }
            echo "\n";
        }
//        $this->root->appendChild($this->xml->createAttribute("date"))->
 //               appendChild($this->xml->createTextNode(self::formatDate($date)));

        foreach(Games::$games as $game) {
            if(sizeof($game->versions)==0)
                continue;
            
            $game_element = $this->createGameElement($game);
            if($game_element==null)
                continue;
            
            $this->doc .= $game_element;
        }
    }
    
    public function doExport() {
        
            


        return $this->doc;
    }
    
    private static function cleanUp($string) {
        @$string = htmlspecialchars($string,ENT_COMPAT|ENT_XML1,'UTF-8');
        return $string;
    }
    
    private function titleTest($title,$game) {
        if(in_array($title,$this->titles)) {
            echo "<pre>";
            var_dump($game);
            echo "</pre>";
            throw new Exception("Duplicate title: ".$title);
        }
        
        array_push($this->titles,$title);
        
    }
    
    protected function createGameElement($game) {
        $all = "";
        foreach($game->versions as $version) {
            if(substr($version->os,0,2)=="PS")
                continue;
            
            $text = "Game Title:\n";
            if(is_null($version->title)) {
                $title = $game->title;                

                if(!is_null($version->os)&&$version->os!="Windows")
                    $title .= " ".$version->os;
                    
                if(!is_null($version->media))
                    $title .= " ".$version->media;

                    
                if($game->type=="mod") {
//                    $for = Games::getGame($game->for,$this->link);
                    $title .= " (".$game->for." MOD)";
                }
                    
            } else {
                $title = $version->title;
            }
            if(!is_null($version->platform))
                $title .= " ".$version->platform;

            $text .= "\t".$title."\n";                
            $text .= "Paths:\n";
            
            $no_paths = true;
            foreach($version->file_types as $type) {        
                if($type->name!="Saves")
                    continue;
                foreach($type->files as $save) {
                    foreach($version->locations as $loc) {
                        switch(get_class($loc)) {
                            case "PathLocation":
                                switch($loc->ev) {
                                    case "localappdata":
                                    case "userdocuments":
                                    case "appdata":
                                    case "public":
                                    case "savedgames":
                                        $loc_text = "\t%".strtoupper($loc->ev)."%\\".$loc->path."\n";
                                        break;
                                    case "installlocation":
                                    case "steamcommon":
                                    case "steamuserdata":
                                    case "ubisoftsavestorage":
                                        continue 3;
                                    default:
                                        throw new Exception($loc->ev." not known");
                                }
                                break;
                            case "RegistryLocation":
                                $loc_text = "\t%REGISTRY%\\".$save->path."\n";
                                $loc_text .= "\t\tRegHive: HKEY_".strtoupper($loc->root)."\n";
                                $loc_text .= "\t\tRegPath: ".$loc->key."\n";
                                $loc_text .= "\t\tRegValue: ".$loc->value."\n";
                                break;
                            default:
                                continue 2;
                        }
                        $no_paths = false;
                        $loc_text .= "\t\t\tInclude: ";
                        if(is_null($save->filename)) {
                            $loc_text .= "*\n";
                        } else {
                            $loc_text .= $save->filename."\n";
                        }
                        if(sizeof($save->excepts)==0) {
                            $loc_text .= "\t\t\tExclude: N/A\n";
                        } else {
                            foreach($save->excepts as $except) {
                                $loc_text .= "\t\t\tExclude: ";
                                
                                if(is_null($except->filename)) {
                                    $loc_text .= "*\n";
                                } else {
                                    $loc_text .= $except->filename."\n";
                                }
                                
                            }
                        }
                        $text .= $loc_text;
                    }                    
                }
            }
            if($no_paths)
                continue;
            
            
            $text .= "\n";
            $this->titleTest($title,$game);
            $all .= $text;
        }
        return $all;
    }

}

?>
