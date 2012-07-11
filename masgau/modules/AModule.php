<?php
abstract class AModule
{
    // property declaration
    public $var = 'a default value';
    protected $db = null;
    
    abstract public function title();
    abstract public function draw();
    abstract public function ajax();
    abstract public function headers();
    abstract public function footer();
    
    protected $gdb;

    protected function __construct($db) {
        $this->db = $db;
        $this->gdb = Databases::$gamesaveinfo;
        $this->gdb->connect();
    }
    
    protected static function CreateImageThumb($file,$width,$text = null) {
        $output = '';
            $output .= '<div class="clear_both"></div><div class="image_thumb yoxview">';
        
        $output .= '<a href="images/'.$file.'">'
                .'<img src="images.php?name='.urlencode($file).'&width='.urlencode($width).'"';
                
        if($text!=null) {
            $output .= ' alt="'.$text.'"';
            $output .= ' title="'.$text.'"';
        }
                
        $output .= ' />'
                .'</a>';
        if($text!=null) {
            $output .= '<br />'.$text;
        }
                
            $output .= '</div>';
        return $output;
    }
    
    // method declaration
    public function displayVar() {
        echo $this->var;
    }
    
    public static function CreateLinkForModule($type, $option = null) {
        switch ($type) {
            case "page":
                include_once 'modules/Page.php';
                return Page::CreateLink($option);
            case "support":
                include_once 'modules/Support.php';
                return Support::CreateLink($option);
            case "game_data":
                include_once 'modules/GameData.php';
                return GameData::CreateLink($option);
            case "contributors":
                include_once 'modules/Contributors.php';
                return Contributors::CreateLink($option);
            case "hyperlink":
                return '<a href="'.$option.'" target="_blank">';
            case "downloads":
                include_once 'modules/Downloads.php';
                return Downloads::CreateLink();
            default:
                throw new Exception("Module ".$type." is not found");
        }
    }
    
    public static function LoadModule($name, $con) {
        switch ($name) {
            case "page":
                include_once 'modules/Page.php';
                $module = new Page($con);
                break;
            case "support":
                include_once 'modules/Support.php';
                $module = new Support($con);
                break;
            case "game_data":
                include_once 'modules/GameData.php';
                $module = new GameData($con);
                break;
            case "contributors":
                include_once 'modules/Contributors.php';
                $module = new Contributors($con);
                break;
            case "downloads":
                include_once 'modules/Downloads.php';
                $module = new Downloads($con);
                break;
            default:
                throw new Exception("Module ".$module." is not found");
        }
        return $module;    
    
    }
    
    protected static function drawInColumns($list,$columns,$letters = false) {
        $return = "";
        $count = count($list);
        $per_column = ceil($count / $columns);
        $i = null;
        $last_letter = null;
                
        foreach ($list as $key => $value) {
            if($letters) {
                $letter = strtoupper(substr($key,0,1));
                if(is_numeric($letter))
                    $letter = '#';
            }
            
            if($i==null) {
                $return .='<div style="width:'.(100/$columns-2).'%;float:left;overflow:hidden;padding:5px;">';
                if($letters&&$last_letter!=null&&$last_letter==$letter) {
                    $return .='<h3>'.$letter.' Cont.</h3>';
                }
                $i = 0;
            }
            
                if($last_letter==null||$last_letter!=$letter) {
                    $return .='<h3>'.$letter.'</h3>';
                }
            $return .= $value.'<br />';
            $i++;

            $last_letter = $letter;
            if($i>=$per_column) {
                $return .= '</div>';
                $i = null;
            }
        }
        if($i!=null)
                        $return .= '</div>';

        return $return;
        
    }    
}
?>
