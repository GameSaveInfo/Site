<?php
include_once 'AModule.php';

class Page extends AModule
{
    private $name;
    private $body = null;
    private $footer;
    
    public function footer() {
        return $this->footer;
    }
    
    public function draw() {
        if($this->body==null){
            echo "the requested page ".$this->name." was not found";
        }
        
        
        // Checks for code snippets for highlighting
        $reg = ";<code lang=\"(?P<name>.+)\"( highlight=\"(?P<highlight>.+)\")?>(?P<code>.+)</code>;msU";
        $match_count = preg_match ($reg, $this->body , $matches , PREG_OFFSET_CAPTURE);
        while(count($matches)>0) {
//            print_r($matches);
  //          echo "<br />";
            $match = $matches[0];
            $output = '<div class="code">';
            $real_length = strlen($match[0]);
            
            $geshi = new GeSHi(trim($matches['code'][0]), $matches['name'][0]);
            if(isset($matches['highlight'][0])) {
                $tmp = explode(',',$matches['highlight'][0]);
                $geshi->highlight_lines_extra($tmp);
            }
            $output .= $geshi->parse_code();
            
            $output .= "</div>";
            $length = strlen($match[0]);
            
            $this->body = substr_replace($this->body,$output,$match[1],$real_length);
            
            $offset = $match[1] + $length;
            $match_count = preg_match ($reg, $this->body , $matches , PREG_OFFSET_CAPTURE, $offset);
        }


        $reg = ";<thumb src=\"(?P<name>.+)\" width=\"(?P<width>.+)\"( alt=\"(?P<alt>.+)\")?( style=\"(?P<style>.+)\")?[ ]*/>;msU";
        $match_count = preg_match ($reg, $this->body , $matches , PREG_OFFSET_CAPTURE);
        while(count($matches)>0) {
            $match = $matches[0];
            $real_length = strlen($match[0]);
            if(isset($matches['alt']))
                $output = self::CreateImageThumb($matches['name'][0],$matches['width'][0],$matches['alt'][0]);
            else
                $output = self::CreateImageThumb($matches['name'][0],$matches['width'][0]);
            //echo $output;

            $this->body = substr_replace($this->body,$output,$match[1],$real_length);
            
            $offset = $match[1] + $real_length;
            
            $match_count = preg_match ($reg, $this->body , $matches , PREG_OFFSET_CAPTURE, $offset);
        }
        
        
        $reg = ";<page name=\"(?P<name>.+)\"( target=\"(?P<target>.+)\")?>(?P<text>.+)</page>;msU";
        $match_count = preg_match ($reg, $this->body , $matches , PREG_OFFSET_CAPTURE);
        while(count($matches)>0) {
            $match = $matches[0];
            $output = '';
            $real_length = strlen($match[0]);
            
            $output .= self::CreateLink($matches['name'][0],$matches['target'][0]);
            $output .= $matches['text'][0];
            $output .= '</a>';            

            $this->body = substr_replace($this->body,$output,$match[1],$real_length);
            
            $offset = $match[1] + $real_length;
            $match_count = preg_match ($reg, $this->body , $matches , PREG_OFFSET_CAPTURE, $offset);
        }
        
        
         $reg = ";<module name=\"(?P<name>.+)\">(?P<text>.+)</module>;msU";
        $match_count = preg_match ($reg, $this->body , $matches , PREG_OFFSET_CAPTURE);
        while(count($matches)>0) {
            $match = $matches[0];
            $output = '';
            $real_length = strlen($match[0]);
            
            $output .= self::CreateLinkForModule($matches['name'][0]);
            $output .= $matches['text'][0];
            $output .= '</a>';            

            $this->body = substr_replace($this->body,$output,$match[1],$real_length);
            
            $offset = $match[1] + $real_length;
            $match_count = preg_match ($reg, $this->body , $matches , PREG_OFFSET_CAPTURE, $offset);
        }   
        echo $this->body;
    }
        
    public function title() {
        
        if(isset($_GET["name"])) {
            $this->name = $_GET["name"];

            $data = $this->db->Select("site_pages",null,array("name"=>$this->name),null);
            
            
            if(sizeof($data)==1) {
                $row = $data[0];
                $this->body = $row->content;
                $this->footer = $row->footer;
                return " - ".$row->title;
            }
            
        }
    }
    
    public function headers() {
        echo '<link media="Screen" href="css/pages.css" type="text/css" rel="stylesheet" />';
        echo '<script type="text/javascript" src="javascript/pages.js"></script>';
    }

    
    public function ajax() { 
        
    }
    
    public static function CreateLink($name, $target = null) {
        if($target==null)
            return '<a href="?module=page&name='.urlencode($name).'">';
        else
            return '<a href="?module=page&name='.urlencode($name).'#'.urlencode($target).'">';        
    }

}
?>
