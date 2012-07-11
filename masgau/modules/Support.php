<?php
include_once 'AModule.php';
include_once 'shared/CompatabilityTable.php';
include_once '../shared/gamedata/Games.php';
class Support extends AModule
{
    private $letters;
    private $letter;
    
    public function footer() {
    }

    private function init() {
        $this->letters = Games::getGameLetters($this->gdb);
        
        if(isset($_GET["filter"])) {
            $this->letter = $_GET["filter"];
        } else {
            $this->letter = "A";
        }
        
    }


    public static function CreateLink($name) {
        return '<a href="?module=support#'.urlencode($name).'">';
    }

    
    public function title() {
        $this->init();



        return " - Game Compatibility";
    }
    
    public function headers() {
        echo '<link media="Screen" href="css/support.css" type="text/css" rel="stylesheet" />';
        echo '<script type="text/javascript" src="javascript/support.js"></script>';
    }

    
    public function draw() {
        echo '<div id="game_data"></div>';

        echo '<div id="compat_table">';
    
        $data = $this->gdb->Select("update_history","max(timestamp) as date",null,null);
        $row = $data[0];
        
        echo '<p>This list reflects the game compatibility of the current data, which was released on ' . $row->date . '.</p>';
        echo '<p>According to this list ';
        Games::printGameCounts($this->gdb);
        echo ' are currently supported across various platforms.</p>';

        echo '<p>Unless implicitly stated only the US English install locations are supported. If you know the paths for other languages, please let me know.';
        echo '<p>If your experience differs from what is presented here, please e-mail me at <a href="mailto:sanmadjack@masgau.org">sanmadjack@masgau.org</a>. We will figure it out.';

        echo '<div id="ajax"></div>';
        
        echo '</div>';
}

private function drawLetters() {
        echo '<div style="width:100%;text-align:center;" class="filter_letters">';
        $links = '';
        
        
        foreach (array_keys($this->letters) as $key) {
            if ($key == $this->letter) {
                if ($key == 'numeric')
                    $links .= '#';
                else
                    $links .=  $key;
            } else {
                $links .= '<a href="?module=support#'.$key.'">';
                if ($key == 'numeric')
                    $links .= '#';
                else
                    $links .=  $key;
                $links .= '</a>';
            }
            $links .= ' ';
        }
        echo $links;

        echo '</div>';

}

public function ajax() {
        $this->init();
        $this->drawLetters();
//            if(!isset($this->filter))
//              return;


        $data = Games::getGamesForLetter($this->letter,$this->gdb);
    
        if(sizeof($data)>0) {
            $table = new CompatabilityTable($this->gdb);
            $output = $table->drawTable($data);
            echo $output;
        }
        
        $this->drawLetters();
        
    }

}
?>
