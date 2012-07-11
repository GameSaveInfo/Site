<?php
require_once 'AUpdateList.php';
class MASGAU11UpdateList extends AUpdateList {
    
    public function __construct($sitelink,$gamelink) {
        parent::__construct($sitelink,$gamelink);
    }
    protected function programCriteria() {
        return array("edition"=>"installable",
                    "stable"=>true);   
    }

    protected function exporterName() {
        return "MASGAU11";
    }



}
?>