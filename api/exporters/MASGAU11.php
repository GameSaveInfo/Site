<?php

require_once 'AXmlExporter.php';
class MASGAU11 extends AXmlExporter {

    public function __construct($comment = null, $time = null) {
        parent::__construct("games.xsd",$comment,$time);
    }
    
    protected function createRootElement() {
        $root = $this->createElement("games");
        $this->setAttribute($root,"majorVersion","1");
        $this->setAttribute($root,"minorVersion","1");
        $this->setAttribute($root,"date",self::formatDate($this->updated));        
        return $root;
    }

    protected function createGameElement($game) {
//        $gele = $this->createElement("game");      
//        $this->processFields($game,$gele, array("type","title","comment"));
//        $gele->appendChild($this->createElement("title",$game->title));
        
        $geles = array();
        
        foreach($game->versions as $version) {
            if($version->revision!=null&&$version->revision!="0") {
                continue;
            }
            
            $gele = $this->createGameVersionElement($game, $version);
            if(is_null($gele))
                continue;
                
            array_push($geles, $gele);
        }
        

        return $geles;
    }

    protected function createGameVersionElement($game, $version) {
        if($version->type!=null)
            return null;
        
        $vele = $this->createElement("game");
        
        if(!$this->setGameVersionAttributes($vele, $version)) {
            return null;
        }
        
        $title = "";
        
        if($version->title!=null) {
            $title = $version->title;
        } else {
            $title = $game->title;
        }
        if(!is_null($version->media)&&$version->media!="Steam") {
            $title .= " ".$version->media;
        }
        if($game->type=="mod") {
            global $gdb;
            $pg = Games::getGame($game->for,$gdb);
            
            $title .= " (".$pg->title." Mod)";
        }
        
        if($game->deprecated) {
            $this->setAttribute($vele,"deprecated","true");
        }

        
        $vele->appendChild($this->createElement("title",$title));            


        if(sizeof($version->ps_codes)==0&&
            sizeof($version->locations)==0)
            return null;

        $ps_codes = false;
        foreach($version->ps_codes as $ps_code) {
            $pele = $this->createElement("ps_code");
            $this->processFields($ps_code,$pele,array("game_version","type","disc","append"));
            $vele->appendChild($pele);
            $ps_codes = true;
        }
        
        
        $leles = $this->createLocationsElements($version->locations);
        
        if(!is_null($leles)&&sizeof($leles)!=0) {
            foreach($leles as $lele) {                
               $vele->appendChild($lele);
            }
        } else {
            if(!$ps_codes) {
                return null;
            }
        }
        
        
        foreach($version->identifiers as $identifier) {
            $iele = $this->createElement("identifier");
            $this->processFields($identifier,$iele,array("game_version"));
            $vele->appendChild($iele);
        }


        foreach($version->file_types as $file_type) {
            $type = $file_type->name;
            foreach($file_type->inclusions as $file) {
                $sele = $this->createElement("save");
                $this->processFields($file,$sele,array("type"));
                if($file_type->name!="Saves")
                    $this->setAttribute($sele,"type",$file_type->name);
                $vele->appendChild($sele);
                
                foreach($file->exclusions as $except) {
                    $eele = $this->createElement("ignore");
                    $this->processFields($except,$eele,array("type","parent"));
                    if($file_type->name!="Saves")
                        $this->setAttribute($eele,"type",$file_type->name);
                    $vele->appendChild($eele);
                }
            }
        }
        
        foreach($version->scumm_vm as $scumm_vm) {
            $sele = $this->createElement("save");
            $this->setAttribute($sele,"filename",$scumm_vm->name.".*");
            $vele->appendChild($sele);
        }

        
        if($version->virtualstore=="ignore") {
            $vsele = $this->createElement("virtualstore");
            $this->setAttribute($vsele,"override","yes");
            $vele->appendChild($vsele);
        }
        if($version->detect=="required"){
            $vsele = $this->createElement("require_detection");
            $vele->appendChild($vsele);
        }


        $comment = "";
        if($game->comment!=null) {
            $comment .= $game->comment." ";
        }
        if($version->comment!=null) {
            $comment .= $version->comment;
        }
        if($comment!="") {
            $vele->appendChild($this->createElement("comment",$comment));            
        }
        
        if($version->restore_comment!=null) {
            $vele->appendChild($this->createElement("restore_comment",$version->restore_comment));            
        }

        // Contributors
        foreach ($version->contributors as $contributor) {
            $vele->appendChild($this->createElement("contributer", $contributor));
        }

        return $vele;
    }
    
    static $loc_types = array("PathLocation"=>"location_path","RegistryLocation"=>"location_registry",
                        "ShortcutLocation"=>"location_shortcut","GameLocation"=>"location_game");
                        
    protected function createLocationElement($leles, $location) {
        $new_lele = $this->createElement(self::$loc_types[get_class($location)]);

        switch(get_class($location)) {
            case "GameLocation":
                $this->setGameVersionAttributes($new_lele,$location);
                break;
            case "ShortcutLocation":
                if($location->ev!="startmenu")
                    return $leles;
                $this->setAttribute($new_lele,"shortcut",$location->path);
                break;
            case "PathLocation":
                switch($location->ev) {
                    case "ubisoftsavestorage":
                        return $leles;
                    case "commonapplicationdata":
                        $location->ev = "allusersprofile";
                        $location->only_for = "WindowsVista";
                        $leles = $this->createLocationElement($leles,$location);
                        $location->only_for = "WindowsXP";                        
                        $location->path .= "\Application Data";
                        break;
                }
                if(property_exists($location,"ev")&&!is_null($location->ev)) {
                    $this->setAttribute($new_lele,"environment_variable",$location->ev);
                }
                if(property_exists($location,"path")) {
                    $this->setAttribute($new_lele,"path",$location->path);
                }
                break;
        }

        if(get_class($location)!="GameLocation")
                $this->processFields($location,$new_lele,array("game_version","ev","deprecated","path","only_for","os","revision"));

        if(!is_null($location->only_for)) {
            $pv = null;
            switch($location->only_for) {
                case "WindowsXP":
                    $pv = "XP";
                    break;
                case "WindowsVista":
                    $pv = "Vista";
                    break;
                default:
                    throw new Exception($location->only_for . " not supported");
            }
            $this->setAttribute($new_lele,"platform_version",$pv);
        }

        if($location->deprecated) {
            $this->setAttribute($new_lele,"read_only","true");
        }

        array_push($leles,$new_lele);
        return $leles;
    }
                        
    protected function createLocationsElements($locations) {
        if(sizeof($locations)>0) {
            $leles = array();
            foreach($locations as $location) {
                if(get_class($location)=="GameLocation"&&$location->revision!=null&&$location->revision!="0") {
                    continue;
                }
                $leles = $this->createLocationElement($leles,$location);
            }
            return $leles;
        }
        return null;
    }
    
    protected function setGameVersionAttributes($element,$source) {

        $name = $source->name;
        if(!is_null($source->release)) {
            $name .= $source->release;
        }

        $platform = null;
        
        if(!is_null($source->os)) {
            switch($source->os) {
                case "Windows":
                case "DOS":
                case "PS1":
                case "PS2":
                case "PS3":
                case "PSP":
                    $platform = $source->os;
                case "Android":
                    break;
                default:
                    throw new Exception($source->os." not known");
                    break;
            }
        }

        
        if(!is_null($source->media)) {
            switch($source->media) {
                case "Steam":
                    $platform = $source->media;
                    break;
                case "Floppy":
                case "CD":
                case "GoG":
                    $name .= $source->media;
                    break;
                default:
                    throw new Exception($source->media." not known");
                    break;
            }
        }
        
        if(!is_null($source->platform)) {
            switch($source->platform) {
                case "SteamCloud":
                    $platform = "Steam";
                    break;
                case "ScummVM":
                case "Flash":
                    $platform = $source->platform;
                    break;
                case "UbisoftSaveStorage":
                case "RenPy":
                    break;
                default:
                    throw new Exception($source->platform." not known");
                    break;
            }
        }
        
        $this->setAttribute($element,"name",$name);

        if(!is_null($platform)) {
            $this->setAttribute($element,"platform",$platform);
        }

        if(!is_null($source->region)) {
            $country = "";
            switch($source->region) {
                case "EU":
                    $country = "EUR";
                    break;
                case "USA":
                    $country = $source->region;
                    break;
                default:
                    return false;
//                    throw new Exception($source->region ." not supported");
            }
            $this->setAttribute($element,"country",$country);
        }
        
        $this->processFields($source,$element, array("game_version","name", "virtualstore", "detect","title","comment","restore_comment","os","version","platform","media","release","region"));

        return true;
    }

}

?>
