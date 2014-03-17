<?php
abstract class AExporter {
    public $error_occured = false;
    
    private $filename;
    public function __construct() {
    }

    public static $date_format = 'Y-m-d\TH:i:s';
    public static function formatDate($string) {
        date_default_timezone_set("UTC");
        return date_format(new DateTime($string), self::$date_format);
    }
    
    public function export() {
        return $this->doExport();
    }

    protected abstract function doExport();
}

?>
