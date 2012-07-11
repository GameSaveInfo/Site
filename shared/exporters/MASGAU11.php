<?php

require_once 'AXmlExporter.php';
class MASGAU11 extends AXmlExporter {

    public function __construct($comment = null) {
        parent::__construct("games.xsd",$comment);
    }
    
    protected function createRootElement() {
        $root = $this->createElement("games");
        $this->setAttribute($root,"majorVersion","1");
        $this->setAttribute($root,"minorVersion","1");
        return $root;
    }

    protected function createGameElement($game) {
//        $gele = $this->createElement("game");      
//        $this->processFields($game,$gele, array("type","title","comment"));
//        $gele->appendChild($this->createElement("title",$game->title));
        
        $geles = array();
        
        foreach($game->versions as $version) {
            $gele = $this->createGameVersionElement($game, $version);
            if(is_null($gele))
                continue;
                
            array_push($geles, $gele);
        }
        

        return $geles;
    }

    protected function createGameVersionElement($game, $version) {
        $vele = $this->createElement("game");
        
        $this->setGameVersionAttributes($vele, $version);
        
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
        
        foreach($version->ps_codes as $ps_code) {
            $pele = $this->createElement("ps_code");
            $this->processFields($ps_code,$pele,array("game_version","type","disc","append"));
            $vele->appendChild($pele);
        }
        
        
        $leles = $this->createLocationsElements($version->locations);
        
        if(!is_null($leles)) {
            foreach($leles as $lele) {                
               $vele->appendChild($lele);
            }
        }
        
        
        foreach($version->identifiers as $identifier) {
            $iele = $this->createElement("identifier");
            $this->processFields($identifier,$iele,array("game_version"));
            $vele->appendChild($iele);
        }


        foreach($version->file_types as $file_type) {
            $type = $file_type->name;
            foreach($file_type->files as $file) {
                $sele = $this->createElement("save");
                $this->processFields($file,$sele,array("type"));
                if($file_type->name!="Saves")
                    $this->setAttribute($sele,"type",$file_type->name);
                $vele->appendChild($sele);
                
                foreach($file->excepts as $except) {
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
                        
    protected function createLocationsElements($locations) {
        if(sizeof($locations)>0) {
            $leles = array();
            foreach($locations as $location) {
                $new_lele = $this->createElement(self::$loc_types[get_class($location)]);
    
                if(get_class($location)=="ShortcutLocation") {
                    if($location->ev!="startmenu")
                        continue;
                    $this->setAttribute($new_lele,"shortcut",$location->path);
                } else {
                    if(property_exists($location,"ev")&&!is_null($location->ev)) {
                        $this->setAttribute($new_lele,"environment_variable",$location->ev);
                    }
                    if(property_exists($location,"path")) {
                        $this->setAttribute($new_lele,"path",$location->path);
                    }
                }

                if(get_class($location)=="GameLocation") {
                    $this->setGameVersionAttributes($new_lele,$location);
                } else {
                    $this->processFields($location,$new_lele,array("game_version","ev","deprecated","path","only_for"));
                }

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
            $platform = $source->os;
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
                    throw new Exception($source->region ." not supported");
            }
            $this->setAttribute($element,"country",$country);
        }
        $this->processFields($source,$element, array("game_version","name", "virtualstore", "detect","title","comment","restore_comment","os","version","platform","media","release","region"));

        
    }

}

?>
