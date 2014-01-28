<?php
require_once 'AGameSaveInfo2.php';
class GameSaveInfo202 extends AGameSaveInfo2 {
    public function __construct($comment = null, $time = null) {
        parent::__construct(2, 0, 2,$comment,$time);
        array_push($this->ignore_fields,"revision");
    }
    
    protected function createGameVersionElement($version) {
        return $this->createGameVersionElementBase($version);
    }


    protected function createLocationElement($location) {
        return $this->createLocationElementBase($location);
    }
}

?>
