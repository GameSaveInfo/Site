<?php

require_once 'Location.php';
class ShortcutLocation extends Location {
    //put your code here
    
    public $ev;
    public $path;
    
	public static $table_name = 'game_location_shortcuts';

	function __construct($parent_id) {
		parent::__construct(self::$table_name,$parent_id);
	}

    protected function getSubFields() {
        return array("ev"=>array("string","ev",true),
                    "path"=>array("string","path",true));
    }

    public function getRowsFor($id,$con) {
        return $this->getConcatRowsFor(self::$table_name,$id,$con);
    }

}

?>
