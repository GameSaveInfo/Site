<?php
include_once 'AModule.php';
include_once 'Contributors.php';
include_once 'shared/CompatabilityTable.php';

class GameData extends AModule
{
    public function draw() {
        echo '<div id="game_list"></div>';
        echo '<div id="game_data"></div>';
    }
    
    public function title() {
        return " - Game Data";   
    }
    
    public function headers() {
        echo '<link media="Screen" href="css/gamedata.css" type="text/css" rel="stylesheet" />';
        echo '<link media="Screen" href="css/support.css" type="text/css" rel="stylesheet" />';
        echo '<script type="text/javascript" src="javascript/gamedata.js"></script>';
    }
        public function footer() {
    }
    private static function printCommonPathAttributes($location) {
        if (get_class($location) != "PathLocation") {
            if ($location->append != null)
                echo '<td>' . $location->append . '</td>';
            else
                echo '<td></td>';

            if ($location->detract != null)
                echo '<td>' . $location->detract . '</td>';
            else
                echo '<td></td>';
        }
        if ($location->platform_version != null)
            echo '<td>Only works with ' . $location->platform_version . '</td>';

        if ($location->deprecated)
            echo '<td style="background-color:red">Deprecated</td>';
    }
              private static  function printFiles($files) {
                    
                    foreach ($files as $file) {
                        echo  '<tr>';
                        if ($file->mode == 'IDENTIFIER') {
                            echo '<td>' . $file->path . '</td>';
                            echo '<td>' . $file->name . '</td>';
                        } else {
        
                            echo '<td>' . $file->path . '</td>';
                            echo '<td>';
                            if ($file->name == null)
                                echo "* (Includes subfolders)";
                            else
                                echo $file->name;
                            echo '</td>';
                            echo '<td>' . $file->type . '</td>';
                            echo '<td>' . $file->modified_after . '</td>';
                        }
                        echo '</tr>';
                    }
                }


    public function ajax() {
        
        
        return;
        
        if(isset($_GET["name"])) {
        echo '<div class="back_link"><a href="javascript:history.go(-1)">Go Back</a></div>';                
            $name = $_GET["name"];
        
        
            require_once 'shared/gamedata//Game.php';
            $game_data = new Game();
            $game_data->loadFromDb($name, null);
            
            $data = $this->runQuery("SELECT * FROM games"
                                    ." WHERE name = '".$name."'"
                                    ." ORDER BY name ASC");
                                    
            if(mysql_num_rows($data)>0) {
                $compat_table = new CompatabilityTable($this->connection);
                echo $compat_table->drawTable($data,false);
            }
            
            
            echo '<div id="tabs"><ul>';
            $i = 0;
            foreach ($game_data->versions as $version) {
                echo '<li><a href="#tabs-'.$i.'">'.$version->getVersionTitle().'</a></li>';
                $i++;
            }
            echo '</ul>';
            $i = 0;
            foreach ($game_data->versions as $version) {
                echo '<div class="game_data_tab" id="tabs-'.$i.'">';
                $i++;
                // Begin title code
    
                // End title code
                
                if ($version->deprecated) {
                    echo '<h4>NOTE: This game version is no longer supported for backup. Archives previously made for this version can still be restored.</h4>';
                }
                if ($version->override_virtualstore) {
                    echo '<h4>NOTE: This game version does not recognize VirtualStore folders.</h4>';
                }
                if ($version->require_detection) {
                    echo '<h4>NOTE: Restoring this game version requires there to already be saves on the system.</h4>';
                }

                
                
                // Side info box
                echo '<div class="contributor_list">';
        
                echo '<p>Contributors:</p>';
                foreach ($version->contributors as $contributor) {
                    echo '<p>'.Contributors::CreateLink($contributor).$contributor.'</a></p>';
                }
                echo '</div>';

                //PS codes
                if (sizeof($version->ps_codes) > 0) {
                    echo '<table><caption>PlayStation Codes</caption>';
                    echo '<tr><th>Prefix</th><th>Suffix</th><th>Append</th><th>Type</th></tr>';
                    foreach ($version->ps_codes as $path) {
                        echo '<tr><td>' . $path->prefix . '</td><td>' . $path->suffix.'</td>'
                                .'<td>'.$path->append.'</td><td>'.$path->type.'</td></tr>';
                    }
                    echo '</table>';
                }
                
                // Paths
                if (sizeof($version->locations) > 0) {
                    echo '<h3>Locations</h3>';
                    if (sizeof($version->scumm_locations) > 0) {
                        echo '<table class="wikitable"><caption>ScummVM Name(s)</caption>';
                        foreach ($version->scumm_locations as $location) {
                            echo '<tr><td>'. $location->name . '</td><td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    if (sizeof($version->path_locations) > 0) {
                        echo '<table><caption>Paths</caption><tr><th>Environment Variable</th><th>Path</th></tr>';
                        foreach ($version->path_locations as $location) {
                            echo '<tr><td>' . $location->ev . '</td><td>' . $location->path . '</td>';     

                                self::printCommonPathAttributes($location);
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    if (sizeof($version->registry_locations) > 0) {
                        echo '<table><caption>Registry Keys</caption><tr><th>Root</th><th>Key</th><th>Value</th><th>Append</th><th>Detract</th></tr>';
                        foreach ($version->registry_locations as $location) {
                            echo  '<tr><td>' . $location->root . '</td><td>' . $location->key . '</td>';
                            if ($location->value == null)
                                echo '<td>(Default)</td>';
                            else
                                echo '<td>' . $location->value . '</td>';
                            self::printCommonPathAttributes($location);
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    if (sizeof($version->shortcut_locations) > 0) {
                        echo '<table><caption>Shortcuts</caption><tr><th>Environment Variable</th><th>Path</th><th>Append</th><th>Detract</th></tr>';
                        foreach ($version->shortcut_locations as $location) {
                            echo '<tr><td>' . $location->ev . '</td><td>' . $location->path . '</td>';
                            self::printCommonPathAttributes($location);
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    if (sizeof($version->game_locations) > 0) {
                        echo '<table class="wikitable"><caption>Parent Game Versions</caption><tr><th>Game</th><th>Platform</th><th>Region</th><th>Append</th><th>Detract</th></tr>';
                        foreach ($version->game_locations as $location) {
                            echo '<tr><td>'.GameData::CreateLink($location->name). $location->name . '</a></td><td>' . $location->platform . '</td><td>' . $location->region . '</td>';
                            self::printCommonPathAttributes($location);
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                }

                if (sizeof($version->files) > 0) {
                    echo '<h3>Files</h3>';
                    if (sizeof($version->save_files) > 0) {
                        echo '<table><caption>To Save</caption>';
                        echo '<tr><th>Path</th><th>Filename</th>';
                        echo '<th>Type</th><th>Modified After</th>';
                        echo '</tr>';
                            self::printFiles($version->save_files);
                        echo '</table>';
                    }
                    if (sizeof($version->ignore_files) > 0) {
                        echo '<table><caption>To Ignore</caption>';
                        echo '<tr><th>Path</th><th>Filename</th>';
                        echo '<th>Type</th><th>Modified After</th>';
                        echo '</tr>';
                            self::printFiles($version->ignore_files);
                        echo '</table>';
                    }
                    if (sizeof($version->identifier_files) > 0) {
                        echo '<table><caption>Used To Identify Game</caption>';
                        echo '<tr><th>Path</th><th>Filename</th>';
                        echo '</tr>';
                            self::printFiles($version->identifier_files);
                        echo '</table>';
                    }
                }
    
                if ($version->comment != null) {
                    echo '<h3>Comment</h3>';
                    echo $version->comment;
                }
    
                if ($version->restore_comment != null) {
                    echo '<h3>Restore Comment</h3>';
                    echo $version->restore_comment;
                }
                
                $data = $this->runQuery("SELECT * FROM xml_versions ver"
                                        .", xml_exporters ex"
                                        ." WHERE ver.exporter = ex.name"
                                        ." ORDER BY major desc, minor desc");

                if($row = mysql_fetch_array($data)) {
                    require_once 'shared/exporters/'.$row['file'];
        
                    echo '<h3>MASGAU '.$row['string'].' XML</h3>';
                    $xml = new DOMDocument();
                    $xml->encoding = 'utf-8';
                    $xml->formatOutput = true;
                    $exporter = new Exporter();
                    $exporter->xml = $xml;
                    $node = $exporter->exportGameVersion($game_data, $version);
                    $xml->appendChild($node);
                    
                    $geshi = new GeSHi($xml->saveXML($node),'xml');
                    echo '<div class="code">'.$geshi->parse_code().'</div>';

                }
                
                echo '<div class="clear_both"></div>';
                
                echo '</div>';
                    
                echo '<script type="text/javascript">$("#tabs").tabs();</script>';

            }
            
        } else {
            echo '<h3>These are all the games and other things that MASGAU has information for</h3>';
            $data = $this->runQuery("SELECT * FROM games"
                                ." ORDER BY name ASC");
            
            $games = array();
            while($row = mysql_fetch_array($data)) {
                $games[$row['name']] = self::CreateLink($row['name']).$row['title'].'</a>';
            }
            echo self::drawInColumns($games,3,true);

        }
    }

    public static function CreateLink($game = null) {
        if($game==null)
        return '<a href="?module=game_data#">';
        else
        return '<a href="?module=game_data#'.urlencode($game).'">';
    }

}
?>
