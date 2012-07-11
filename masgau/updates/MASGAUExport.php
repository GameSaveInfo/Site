<?php
include_once '../../shared/exporters/ExportController.php';

class MASGAUExport extends ExportController {
    protected $slink;
    
    public function __construct($sitelink, $gamelink) {
        parent::__construct($gamelink);
        $this->slink = $sitelink;
    }

    
    protected function drawFileList($exporter) {
        include_once $exporter."UpdateList.php";
        $class = $exporter."UpdateList";
        $updates = new $class($this->slink,$this->link);
        $updates->drawPage();
    }

}

?>
