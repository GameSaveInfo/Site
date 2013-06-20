<?php
require_once 'AXmlExporter.php';
abstract class GameSaveInfo extends AXmlExporter {
    protected $majorVersion = null;
    protected $minorVersion = null;
    protected $revision = null;
    
    public function __construct($majorVersion, $minorVersion, $revision, $comment = null, $time = null) {
        $this->majorVersion = $majorVersion;
        $this->minorVersion = $minorVersion;
        $this->revision = $revision;
        $schema = "GameSaveInfo".$majorVersion.$minorVersion;
        if($revision!=null) {
            $schema .= $revision;
        }
        $schema .= ".xsd";
        
        parent::__construct($schema,$comment,$time);
    }
    
    
    protected function createRootElement() {
        $root = $this->createElement("programs");
        $this->setAttribute($root,"majorVersion",$majorVersion);
        $this->setAttribute($root,"minorVersion",$minorVersion);
        if($this->revision!=null) {
            $this->setAttribute($root,"minorVersion",$revision);
        }
        $this->setAttribute($root,"updated",self::formatDate($this->updated));        
        return $root;
    }

    protected function createGameElement($game) {
        $gele = $this->createElement($game->type);
        
        $this->processFields($game,$gele, array("type","title","comment"));

        $gele->appendChild($this->createElement("title",$game->title));
        
        
        foreach($game->versions as $version) {
            $gele->appendChild($this->createGameVersionElement($version));
        }
        
        if($game->comment!=null) {
            $gele->appendChild($this->createElement("comment",$game->comment));            
        }

        return $gele;
    }
    protected function createGameVersionElement($version) {
        $vele = $this->createElement("version");
        
        $this->processFields($version,$vele, array("name", "virtualstore", "detect","title","comment","restore_comment"));
        
        if($version->virtualstore=="ignore")
            $this->setAttribute($vele,"virtualstore","ignore");

        if($version->detect=="required")
            $this->setAttribute($vele,"detect","required");

        if($version->title!=null) {
            $vele->appendChild($this->createElement("title",$version->title));            
        }
    
        foreach($version->scumm_vm as $scumm_vm) {
            $sele = $this->createElement("scummvm");
            $this->processFields($scumm_vm,$sele,array("game_version"));
            $vele->appendChild($sele);
        }
        
        foreach($version->ps_codes as $ps_code) {
            $pele = $this->createElement("ps_code");
            $this->processFields($ps_code,$pele,array("game_version"));
            $vele->appendChild($pele);
        }
        
        
        $lele = $this->createLocationsElement($version->locations);
        
        if($lele!=null)
            $vele->appendChild($lele);

        foreach($version->file_types as $file_type) {
            $fele = $this->createElement("files");
            $this->processFields($file_type,$fele,array("game_version"));
            foreach($file_type->inclusions as $file) {
                $sele = $this->createElement("include");
                $this->processFields($file,$sele,array("type"));
                foreach($file->exclusions as $except) {
                    $eele = $this->createElement("exclude");
                    $this->processFields($except,$eele,array("type","parent"));
                    $sele->appendChild($eele);
                }
                $fele->appendChild($sele);
            }
            $vele->appendChild($fele);
        }
        
        foreach($version->registry_types as $reg_type) {
            $fele = $this->createElement("registry");
            $this->processFields($reg_type,$fele,array("game_version"));
            foreach($reg_type->entries as $entry) {
                $sele = $this->createElement("entry");
                $this->processFields($entry,$sele,array("type"));
                $fele->appendChild($sele);
            }
            $vele->appendChild($fele);
        }
        
        foreach($version->link_locations as $linkable) {
            $iele = $this->createElement("linkable");
            $this->processFields($linkable,$iele,array("game_version"));
            $vele->appendChild($iele);
        }
        
        foreach($version->identifiers as $identifier) {
            $iele = $this->createElement("identifier");
            $this->processFields($identifier,$iele,array("game_version"));
            $vele->appendChild($iele);
        }


        if($version->comment!=null) {
            $vele->appendChild($this->createElement("comment",$version->comment));            
        }
        if($version->restore_comment!=null) {
            $vele->appendChild($this->createElement("restore_comment",$version->restore_comment));            
        }

        // Contributors
        foreach ($version->contributors as $contributor) {
            $vele->appendChild($this->createElement("contributor", $contributor));
        }

        return $vele;
    }
    
    
    
    static $loc_types = array("PathLocation"=>"path","RegistryLocation"=>"registry",
                        "ShortcutLocation"=>"shortcut","GameLocation"=>"parent");
    protected function createLocationsElement($locations) {
        if(sizeof($locations)>0) {
            $lele = $this->createElement("locations");
            foreach($locations as $location) {
                $new_lele = $this->createElement(self::$loc_types[get_class($location)]);
                
                $this->processFields($location,$new_lele,array("game_version"));
                
                $lele->appendChild($new_lele);
            }
            return $lele;
        }
    }


}

?>
