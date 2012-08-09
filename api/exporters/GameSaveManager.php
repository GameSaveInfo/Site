<?php
require_once 'AXmlExporter.php';
class GameSaveManager extends AXmlExporter {
    
    public function __construct($comment = null, $time = null) {
        parent::__construct(null,$comment,$time);
    }

    
    protected function createRootElement() {
        $root = $this->createElement("GameSaveManager_DatabaseUpdate");
        $this->setAttribute($root,"version","7");
        $this->setAttribute($root,"build",self::formatDate($this->updated));
        return $root;
    }

    
    private $existing_names = array();

    protected function createGameElement($game) {
        $entries = array();
        
        foreach($game->versions as $version) {
            $entry = $this->processGameVersion($game,$version);
            if(!is_null($entry))
                array_push($entries,$entry);
        }    
        return $entries;
    }
    private $directories;
    private $game_count = 0;
    protected function processGameVersion($game,$version) {
        $entry = $this->createElement("entry");
        $this->setAttribute($entry,"new","true");
        $this->setAttribute($entry,"id",$this->game_count);
        
        $title = $game->title;
        if(!is_null($version->title)) {
            $title = $version->title;
        } else {
            switch($version->platform) {
                case "SteamCloud":
                    $title .= " (Steam Cloud)";
                    break;
                default:
                    break;
            }
        }
        
        if(in_array($title,$this->existing_names)) {
            throw new Exception("Duplicate name ".$title);
        }
        
        $title = $this->createElement("title",$title);
            
        $entry->appendChild($title);
        
        $comment = "";
        if(!is_null($version->comment)) {
            $comment = $version->comment;
        } else if(!is_null($game->comment)) {
            $comment = $game->comment;
        }
        
        $backup_warning = $this->createElement("backupwarning",$comment);
        $entry->appendChild($backup_warning);
        
        $comment = "";
        if(!is_null($version->restore_comment)) {
            $comment = $version->restore_comment;
        }
        
        $restore_warning = $this->createElement("restorewarning",$comment);
        $entry->appendChild($restore_warning);

        $loc_found = false;
        $this->directories = $this->createElement("directories");
        
        foreach($version->locations as $location) {
            foreach($version->file_types as $file_type) {
                $dir = $this->createDir($location, $file_type);
                if($dir!=null) {
                    $loc_found = true;
                }
            }
        }
        
        if($loc_found)
            $entry->appendChild($this->directories);

        if(!$loc_found)
            return null;
            
        array_push($this->existing_names,$title);

        $this->game_count++;        
        return $entry;
    }
    
    private $dir_count = 0;
    protected function createDir($location, $file_type) {
        $dir = $this->createElement("dir");
        $this->setAttribute($dir,"id",$this->dir_count);
        
        $specialpath = null;
        $path = "";
        
        $root = "";
        $key = "";
        $value = "";
        
        switch(get_class($location)) {
            case "PathLocation":
                $path = $location->path;
                
                switch($location->ev) {
                    case "userdocuments":
                        $specialpath = "%DOCUMENTS%";
                        break;
                    case "userprofile":
                        $specialpath = "%USER_PROFILE%";
                        break;
                    case "appdata":
                        $specialpath = "%SAVED_GAMES%";
                        break;
                    case "localappdata":
                        $specialpath = "%APPDATA_LOCAL%";
                        break;
                    case "savedgames":
                        $specialpath = "%APPDATA%";
                        break;
                    case "commonapplicationdata":
                        $specialpath = "%APPDATA_COMMON%";
                        break;
                    case "flashshared":
                        $specialpath = "%APPDATA%";
                        $path = "Macromedia/Flash Player/#SharedObjects/".$path;
                        break;
                    case "steamcommon":
                        $specialpath = "%STEAM%";
                        $path = "steamapps/common/".$path;
                        break;
                    case "steamsourcemods":
                        $specialpath = "%STEAM%";
                        $path = "steamapps/sourcemods/".$path;
                        break;
                    case "steamuser":
                        $specialpath = "%STEAM_CACHE%";
                        break;
                    case "steamuserdata":
                        $specialpath = "%STEAM_CLOUD%";
                        break;
                    case "ubisoftsavestorage":
                        $specialpath = "%UPLAY%";
                        break;
                    case "public":
                    case "installlocation":
                    case "altsavepaths":
                    case "drive":
                    case "allusersprofile":
                        return null;
                    default:
                        throw new Exception($location->ev." not supported");
                }
                break;
            case "RegistryLocation":
                $specialpath = "%REGISTRY%";
                $path = $location->append;
                switch($location->root) {
                    case "local_machine":
                        $root = "HKEY_LOCAL_MACHINE";
                        break;
                    case "current_user":
                        $root = "HKEY_CURRENT_USER";
                        break;
                    default:
                        throw new Exception($location->root." not supported");
                }
                $key = $location->key;
                $value = $location->value;
                break;
            case "ShortcutLocation":
                return;
            case "GameLocation":
                $test = null;
                foreach($location->getAdjustedParentLocations() as $subloc) {                    
                    if(!is_null($this->createDir($subloc, $file_type)))
                        $test = "something";
                }                
                return $test;
            default:
                throw new Exception(get_class($location)." not supported");
        }
        
        $path = str_replace("\\","/",$path);
        $path = $this->createElement("path",$path);
        $dir->appendChild($path);
        $this->setAttribute($path,"specialpath",$specialpath);


        $reg = $this->createElement("reg");
        $hive = $this->createElement("hive", $root);
        $reg->appendChild($hive);
        $key = str_replace("\\","/",$key);
        $path = $this->createElement("path", $key);
        $reg->appendChild($path);
        $value = $this->createElement("value", $value);
        $reg->appendChild($value);
        $dir->appendChild($reg);
        
        
        $arr = $file_type->concat('|',"*.*");
        $include = $arr["include"];
        $exclude = $arr["exclude"];
        
        $include = $this->createElement("include",$include);
        $dir->appendChild($include);

        $exclude = $this->createElement("exclude",$exclude);
        $dir->appendChild($exclude);
        
        
        $this->dir_count++;
        $this->directories->appendChild($dir);        
        return $dir;
    }

}

?>
