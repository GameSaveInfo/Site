<?php
abstract class AXmlData {
    protected abstract function getId();
    public abstract function getFields();
    protected abstract function getSubObjects();
    protected abstract function getNodes();

    protected $parent_id = null;
    protected $table;
    
    
    function __construct($table,$parent_id) {
    	$this->table = $table;
    	$this->parent_id = $parent_id;
	}
    
    public function loadFromDb($id, $row, $con) {
        
        foreach($this->getFields() as $field) {
            $name = $field[1];
            if(!property_exists($row,$name))
                throw new Exception("The row provided for ".get_class($this)." does not have field ".$name);
                
            $value = $row->$name;
            $this->loadDbField($name,$value);
        }        
        
        $subs = $this->getSubObjects();
        if($subs!=null) {
            foreach(array_keys($subs) as $key) {
                $this->loadDbSubObject($id, $key,$con);
            }        
        }
    }
    public static $date_format = 'Y-m-d\TH:i:s';
    public static function formatDate($string) {
        date_default_timezone_set("UTC");
        return date_format(new DateTime($string), self::$date_format);
    }

    protected function loadDbField($name, $value) {
        $this->$name = $value;
    }
    
    protected function loadDbSubObject($id, $key, $con) {
        $subs = $this->getSubObjects();
        $class = $subs[$key];
        require_once $class.'.php';
        $dummy = new $class(null);
        $data = $dummy->getRowsFor($id,$con);
        foreach ($data as $row) {
            $obj = new $class($id);
            if(!property_exists($row,'id')) {
                $obj->loadFromDb(null,$row,$con);
            } else {
                $obj->loadFromDb($row->id,$row,$con);
            }
            array_push($this->$key,$obj);
        }
    }

    
    protected static function writeDataToDb($source, $table, $fields, $sub_objects, $con, $message = null) {
        $insert = array();
        foreach(array_keys($fields) as $key) {
            if(is_numeric($key))
                continue;
            $field = $fields[$key][1];    
                
            if($source->$field!=null)
                $insert[$field] = $source->$field;
        }
        
        $id = $source->getId();
        if($id!=null) {
            $insert['id'] = $id;
        }
        
        $con->Insert($table, $insert, $message); 
        
        if($sub_objects!=null) {
            foreach(array_keys($sub_objects) as $sub_object) {
                if(is_numeric($sub_object))
                    continue;
                $objects = $source->$sub_object;
                foreach($objects as $object) {
                    if(is_object($object)) {
                        $object->newWriteToDb($con);   
                    } else {
                        throw new Exception($sub_object. ' is not an object ');
                    }
                }
            }
        }        
    }

    protected static function combine($one, $two) {
        if(is_object($one)&&is_object($two)) {
            $obj = new stdClass();
            foreach($one as $key => $value) {
                $obj->$key = $value;
            }
            foreach($two as $key => $value) {
                $obj->$key = $value;
            }
            return $obj;
        } else {
            foreach(array_keys($two) as $key) {
                $one[$key] = $two[$key];
            }
            return $one;
        }
    }

    public function newWriteToDb($con) {
        self::writeDataToDb($this, $this->table,$this->getFields(),$this->getSubObjects(),$con, 'Writing '.get_class($this).' to database');
    }


    function loadXml($node) {
        foreach ($node->attributes as $attribute) {
            $this->loadAttribute($attribute->name,$attribute->value);
        }
        
        foreach ($node->childNodes as $child) {
            $this->loadSubNode($child);
        }
    }

    protected function loadAttribute($name, $value) {
        $fields = $this->getFields();

        if(array_key_exists($name,$fields)) {
            $type = $fields[$name][0];
            $field = $fields[$name][1];
            switch($type) {
                case "string":
                    $this->$field = $value;
                    break;
                case "boolean":
                    $this->$field = $value=="true";
                    break;
                case "integer":
                    $this->$field = intval($value);
                    break;
                default:
                    throw new Exception($type." not supported in " .get_class($this));
            }
        } else {
            throw new Exception($name . ' not supported in '. get_class($this));
        }
    }
    protected function loadSubNode($node) {
        $subs = $this->getNodes();
        $name = $node->localName;
        if($name=='')
            return;
            
        if(array_key_exists($name,$subs)) {
            $class = $subs[$name][0];
            $prop = $subs[$name][1];
            switch($class) {
                case "collection":
                    foreach ($node->childNodes as $child) {
                        $item = $this->loadSubNode($child);
                        if($item==null)
                            continue;
                        if($prop!=null)
                            array_push($this->$prop,$this->loadSubNode($child));
                        else
                            $this->loadSubNode($child);
                    }
                    break;
                case "string":
                    $value = $node->textContent;
                    if(is_array($this->$prop)) {
                        array_push($this->$prop,$value);
                    } else {
                        $this->$prop = $value;
                    }
                    break;
                default:
                    require_once $class.'.php';
                    $object = new $class($this->getId());
                    $object->loadXml($node);
                    if(is_array($this->$prop))
                        array_push($this->$prop,$object);
                    else 
                        $this->$prop = $object;
                        
                    return $object;
            }
        } else if(array_key_exists($name,$this->getFields())) {
            $value = $node->textContent;
            $this->loadAttribute($name, $value);            
        } else {
            throw new Exception($name . " not supported in ". get_class($this));
        }
    }


    public function generateOrder() {
		$order = array();
        
        $fields = $this->getFields();
        if($fields!=null) {
            foreach(array_keys($fields) as $key) {
                if(is_numeric($key)||$fields[$key][2]!=true) {
                    continue;
                }
                $field = $fields[$key][1];
                $order[$field] = "ASC";
            }
        }
        return $order;
	}


    
    
	public function generateHash() {
		$string = "parent_id:";
    	if($this->parent_id!=null)
			$string .= $this->parent_id."\n";
        else
            $string .= "null\n";
        
        $fields = $this->getFields();
        if($fields!=null) {
            foreach(array_keys($fields) as $key) {
                if(is_numeric($key)||$fields[$key][2]!=true) {
                    continue;
                }
                $field = $fields[$key][1];
                $string .= $field.':';
                
                if($this->$field!=null) {
                    $string .= $this->$field;
                } else {
                    $string .= 'null';
                }
                $string .= '\n';
            }
        }
//        echo $string;
		return hash("md5",$string);
	}


    protected function concatHelper($array) {
        $string = "";
        foreach($array as $item) {
            if($item != null) {
                $string .= $item;
            }
        }
        return $string;
    }




}
?>