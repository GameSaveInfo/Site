<?php
require_once 'AExporter.php';
abstract class AXmlExporter extends AExporter {
    private $xml;
    private $root;
    private $schema = null;
    
    public static $content_type = "text/xml";
    
    public function __construct($schema = null, $comment = null, $date = null) {
        parent::__construct();
        $this->xml = new DOMDocument();
        $this->xml->encoding = 'UTF-8';
        $this->xml->formatOutput = true;
        
        $this->root = $this->createRootElement();
        $this->setAttribute($this->root,"xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
        if($schema!=null) {
            $this->schema = $schema;
            $this->setAttribute($this->root,"xsi:noNamespaceSchemaLocation",$schema);   
        }
        date_default_timezone_set("UTC");
        $this->root->appendChild($this->xml->createAttribute("date"))->
                appendChild($this->xml->createTextNode(self::formatDate($date)));

        $this->xml->appendChild($this->root);
        
        if(!is_null($comment)) {
            $comment = $this->xml->createComment($comment);
            $this->root->appendChild($comment);
        }
        
        foreach(Games::$games as $game) {
            if(sizeof($game->versions)==0)
                continue;
            
            $game_element = $this->createGameElement($game);
            if($game_element==null)
                continue;
            
            if(is_array($game_element)) {
                foreach($game_element as $g) {
                    $this->root->appendChild($g);
                }
            } else {
                $this->root->appendChild($game_element);
            }
        }
    }
    
    public function doExport() {
        
        
        $text = $this->xml->saveXML();

        $document = new DOMDocument();
        $document->loadXML($text);
        

        if($this->schema!=null) {
            $folder =  dirname(__FILE__);
            $schema = $folder . '/../schemas/' . get_class($this).'.xsd';
            
            if (!file_exists($schema)) {
                throw new Exception("Can't find schema file ".$schema);
            }
            
            if (!$document->schemaValidate($schema)) {
                echo $text;
                $this->error_occured = true;
                throw new Exception("XML DID NOT PASS VALIDATION: " . $schema);
            }
        }

        return $text;
    }
    
    protected function createElement($name, $content = null) {
        if ($content == null) {
            return $this->xml->createElement($name);
        } else {
            return $this->xml->createElement($name, self::cleanUp($content));
        }
    }
    
    
    protected function setAttribute($element,$name,$value) {
        $element->appendChild($this->xml->createAttribute($name))->
                appendChild($this->createTextNode($value));
        
    }
    protected function createTextNode($text) {
        return $this->xml->createTextNode($text);
    }

    private static function cleanUp($string) {
        @$string = htmlspecialchars($string,ENT_COMPAT|ENT_XML1,'UTF-8');
        return $string;
    }
    
    protected function processFields($source, $element, $ignore_these_fields = null) {
        $fields = $source->getFields();
        
        foreach(array_keys($fields) as $key) {
            $field = $fields[$key][1];
            $type = $fields[$key][0];
            if($source->$field!=null) {
                $value = $source->$field;
                if($ignore_these_fields==null||!in_array($key,$ignore_these_fields)) {
                    switch($type) {
                        case "boolean":
                            if($value==1)
                                $this->setAttribute($element,$key,"true");
                            break;
                        case "string":   
                        case "integer":
                            $this->setAttribute($element,$key,$value);
                            break;
                        case "timestamp":
                            $this->setAttribute($element,$key,self::formatDate($value));                            
                            break;
                        default:
                            throw new Exception($type." NOT KNOWN");
                    }
                }
            }
        }        
    }




    protected abstract function createRootElement();
    protected abstract function createGameElement($game);

}

?>
