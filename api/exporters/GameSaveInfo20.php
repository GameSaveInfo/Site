<?php
require_once 'AGameSaveInfo2.php';
class GameSaveInfo20 extends AGameSaveInfo2 {
    public function __construct($comment = null, $time = null) {
        parent::__construct(2,0,null,$comment,$time);
    }
    
    
    protected function createGameVersionElement($version) {
        if($version->revision!=null&&$version->revision!="0") {
            return null;
        }
        return $this->createGameVersionElementBase($version);
    }


    protected function createLocationElement($location) {
        if(property_exists($location,"revision")&&$location->revision!=null&&$location->revision!="0") {
            return null;
        }
        return $this->createLocationElementBase($location);
    }
}

?>
