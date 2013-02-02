<?php
require_once 'libs/ARSSFeed.php';
require_once 'headers.php';
class GSIRSSFeed extends ARSSFeed {
    private static $url = "http://gamesave.info/";
    
    public function __construct() {
        parent::__construct();
        
        $id = $this->addChannel("GameSave.Info Updates",self::$url, "The update news and information for GameSave.Info");
        
        global $db;
        $news = $db->Select("anouncements",null,null,array("timestamp"));
        foreach($news as $row) {
            $this->addItem($id,$row->subject , self::$url, $row->body,
                            null,$row->timestamp);
        }
    }

}

?>
