<?php
include_once 'AModule.php';
include_once 'GameData.php';
class Contributors extends AModule
{
    private $name;
    public function title() {
        return " - Contributors";   
    }
        public function footer() {
    }

    public function headers() {
        echo '<link media="Screen" href="css/contributors.css" type="text/css" rel="stylesheet" />';
        echo '<script type="text/javascript" src="javascript/contributors.js"></script>';
    }
    
    public function draw() {
        if(!isset($this->name)) {
            echo "<h1>Game Data Contributors</h1>";
            
            $data = $this->gdb->RunStatement("SELECT contributor, count(*) as count"
                                ." FROM game_contributions con"
                                ." GROUP BY contributor"
                                ." ORDER BY count desc, contributor asc"
                                );
            
    
            if (sizeof($data)==0) {
                echo 'Where the hell are all the contributors!?!';
            } else {
                $per_column = ceil((float)sizeof($data) / (float)3);
                $column_count = null;
                echo '<div style="width:100%;" class="column">';
                foreach($data as $row) {
                    if ($column_count == null) {
                       echo '<div style="width:30%;float:left;overflow:hidden;position:relative;left:100px;">';
                        $column_count = 0;
                    }
                    echo '<li type="1" value="' . $row->count . '">';
                    echo $row->contributor;
                    echo '</li>';
                    
                    $column_count++;
                    if($column_count>=$per_column) {
                        echo '</div>';
                        $column_count = null;
                    }
                }
                echo "</div>";
                echo "</div>";
            }
        }
    }
    
    public function ajax() {


        $return = "";
        if(isset($_GET["name"])) {
            $name = $_GET["name"];
            
            $data = $this->runQuery("SELECT * FROM"
                                ." game_contributions con"
                                .", game_versions ver"
                                .", games game"
                                ." WHERE contributor = '"    . $name . "'"
                                ." AND con.game_version = ver.id"
                                ." AND game.name = ver.name"
                                ." ORDER BY game.name ASC"
                                );
                      $count = mysql_num_rows($data);
                      
            $return .= '<h2>'.htmlentities($name).' contributed data for '.$count.' game';
            if($count>1)
                $return .= 's';
            $return .= '!</h2>';

            $games = array();
            while($row = mysql_fetch_array($data)) {
                $game = GameData::CreateLink($row['name']).htmlentities($row['title']);
//                if($row['platform']!='UNIVERSAL')
  //                  $game .= ' ('.$row['platform'].')';
                
    //            if($row['region']!='UNIVERSAL')
      //              $game .= ' ('.$row['region'].')';
                    
                $game .= '</a>';
                $games[$row['name']] = $game;
            }
            
            $return .= $this->drawInColumns($games,2,true);
        } else {
             $return .= "<h1>Select A User For Details!</h1>";   
        }
        
        return $return;
    }
    
    public static function CreateLink($name) {
        return '<a href="?module=contributors#'.urlencode($name).'">';
    }

}
?>
