<?php
require_once 'AXmlExporter.php';
class SaveGameBackup extends AXmlExporter {
    
    public function __construct($comment = null, $time = null) {
        parent::__construct(null,$comment,$time);
    }

    private $games, $applications, $games_found = false, $apps_found = false;
    protected function createRootElement() {
        $root = $this->createElement("entries");
        $this->setAttribute($root,"ver","7");
        $this->setAttribute($root,"reldate",self::formatDate($this->updated));
        
        $this->games = $this->createElement("games");
        $this->applications = $this->createElement("applications");

        return $root;
    }
    
    protected function createGameElements($root) {
        parent::createGameElements($root);
        if($this->games_found)
            $root->appendChild($this->games);
        if($this->apps_found)
            $root->appendChild($this->applications);
        
        
    }
    
    private $existing_names = array();

    private $current_entry;

    protected function createGameElement($game) {
        $this->root = $this->games;
        
        $this->current_entry = $this->createElement("entry");
        $this->setAttribute($this->current_entry,"name",$game->title);
        
        $entries = array();
        
        $paths_found = false;
        foreach($game->versions as $version) {
            if($this->processGameVersion($game,$version)) {
                $paths_found = true;
            }
        }
        
        if(!$paths_found) {
            return null;
        }
        
        if(!is_null($game->comment)) {
            $comment = $game->comment;
            $note = $this->createElement("note",$comment);
            $this->current_entry->appendChild($note);
        }
        
        switch($game->type) {
            case "game":
            case "expansion":
            case "mod":
                $this->games_found = true;
                $this->games->appendChild($this->current_entry);
                break;
            case "system":
                $this->apps_found = true;
                $this->applications->appendChild($this->current_entry);
                break;
            default:
                throw new Exception($game->type." not supported");
        }
        
        return null;
    }
    
    private $game_count = 0;
    protected function processGameVersion($game,$version) {
        $paths_found = false;
        $include = "";
        $exclude = "";
        $sep = "|";
        foreach($version->file_types as $type) {
            if($type->name!=null&&$type->name!="Saves")
                continue;
            $arr = $type->concat('|');
            $include .= $arr["include"]."|";
            $exclude .= $arr["exclude"]."|";
                
        }        
        $include = trim($include,$sep);
        $exclude = trim($exclude,$sep);

        foreach($version->locations as $location) {
            if($this->processLocation($location,$include,$exclude))
                $paths_found = true;
        }
        
        return $paths_found;
    }

    protected function processLocation($location, $include, $exclude) {
        $path = "";
        $type = null;
        $append = null;
        $remove = null;
        

        switch(get_class($location)) {
            case "PathLocation":
                $path = $location->path;
                if(!is_null($location->append)) {
                    $path .= "\\".$location->append;
                }
                if(!is_null($location->detract)) {
                    
                    $path .= "\\".$location->append;
                }
                
                switch($location->ev) {
                    case "steamuser":
                        $type="steamcache";                    
                        break;
                    case "steamuserdata":
                        $type="steamcloud";                    
                        break;
                    case "userdocuments";
                        $path = "%documents%\\".$path;
                        break;
                    case "steamcommon";
                        $path = "%steamcommon%\\".$path;
                        break;
                    case "savedgames";
                        $path = "%savedgames%\\".$path;
                        break;
                    case "localappdata";
                        $path = "%local_appdata%\\".$path;
                        break;
                    case "appdata";
                        $path = "%roaming_appdata%\\".$path;
                        break;
                    case "flashshared";
                        $path = "%roaming_appdata%\\Macromedia\\Flash Player\\#SharedObjects".$path;
                        break;
                    case "allusersprofile";
                        $path = "%all_users_profile%\\".$path;
                        break;
                    case "userprofile";
                        $path = "%userprofile%\\".$path;
                        break;
                    case "installlocation":
                    case "drive":
                    case "public":
                    case "ubisoftsavestorage":
                    case "steamsourcemods";
                    case "commonapplicationdata":
                        case "altsavepaths":
                        return false;
                    default:
                        throw new Exception($location->ev." not supported");                        
                }                    
                break;
            case "RegistryLocation":
                $path = "%registry%\\".$location->key;
                if(!is_null($location->value))
                    $path .= "\\".$location->value;
                    
                $append = $location->append;
                $remove = $location->detract;
                
                break;
            case "ShortcutLocation":
                return false;
            case "GameLocation":
                $found = false;
                foreach($location->getAdjustedParentLocations() as $subloc) {                    
                    if($this->processLocation($subloc,$include, $exclude))
                        $found = true;
                }
                return $found;
            default:
                throw new Exception(get_class($location)." not supported");
        }
        

        $entry = $this->createElement("datapath",$path);

        if(!is_null($include)&&$include!="")        
            $this->setAttribute($entry,"include",$include);
            
        if(!is_null($exclude)&&$exclude!="")        
            $this->setAttribute($entry,"exclude",$exclude);
                    
        if(!is_null($type))        
            $this->setAttribute($entry,"type",$type);
            

        if(!is_null($append))
            $this->setAttribute($entry,"append",$append);
            
            
        if(!is_null($remove))
            $this->setAttribute($entry,"remove",$remove);
        
        $this->current_entry->appendChild($entry);
        return true;
    }

}

?>
