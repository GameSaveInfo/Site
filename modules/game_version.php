

<?php
include_once '../../DBSettings.php';
include_once '../headers.php';

function printCommonPathAttributes($location) {
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

function printFiles($files) {    
    foreach ($files as $file) {
            echo '<tr>';
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



$name = $_GET["name"];
         
require_once '../shared/gamedata/Game.php';
$game_data = new Game();
$game_data->loadFromDb($name, null);
if($game_data->name==null) {
    $data = mysql_query("SELECT * FROM games"
                        ." WHERE name like '".$name."%'"
                        ." ORDER BY name ASC");
	if($row = mysql_fetch_array($data)) {
		$game_data->loadFromDb($row['name'], null);
	} else {
		throw new Exception($name." not found!");
	}            
}





echo '<h1>'.$game_data->title;
if($game_data->type!="Game")
    echo " (".$game_data->type.")";
echo '</h1>';    
echo '<div class="game_versions">';            
            
echo '<ul>';

            $i = 0;
//            foreach ($game_data->versions as $version) {
  //              case($version->os) {
    //                
      //          }
        //        echo '<li><a href="#tabs-'.$i.'">'.$version->getVersionTitle().'</a></li>';
          //      $i++;
        //    }
echo '</ul>';
$i = 0;

            foreach ($game_data->versions as $version) {
                echo '<div class="game_data_tab" id="tabs-'.$i.'">';
                

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

                                printCommonPathAttributes($location);
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    if (sizeof($version->registry_locations) > 0) {
                        echo '<table><caption>Registry Keys</caption><tr><th>Root</th><th>Key</th><th>Value</th><th>Append</th><th>Detract</th></tr>';
                        foreach ($version->registry_locations as $location) {
                            echo '<tr><td>' . $location->root . '</td><td>' . $location->key . '</td>';
                            if ($location->value == null)
                                echo '<td>(Default)</td>';
                            else
                                echo '<td>' . $location->value . '</td>';
                            printCommonPathAttributes($location);
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    if (sizeof($version->shortcut_locations) > 0) {
                        echo '<table><caption>Shortcuts</caption><tr><th>Environment Variable</th><th>Path</th><th>Append</th><th>Detract</th></tr>';
                        foreach ($version->shortcut_locations as $location) {
                            echo '<tr><td>' . $location->ev . '</td><td>' . $location->path . '</td>';
                            printCommonPathAttributes($location);
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                    if (sizeof($version->game_locations) > 0) {
                        echo '<table class="wikitable"><caption>Parent Game Versions</caption><tr><th>Game</th><th>Platform</th><th>Region</th><th>Append</th><th>Detract</th></tr>';
                        foreach ($version->game_locations as $location) {
                            echo '<tr><td><a href="#'.$location->name.'">'. $location->name . '</a></td><td>' . $location->platform . '</td><td>' . $location->region . '</td>';
                            printCommonPathAttributes($location);
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
                            printFiles($version->save_files);
                        echo '</table>';
                    }
                    if (sizeof($version->ignore_files) > 0) {
                        echo '<table><caption>To Ignore</caption>';
                        echo '<tr><th>Path</th><th>Filename</th>';
                        echo '<th>Type</th><th>Modified After</th>';
                        echo '</tr>';
                            printFiles($version->ignore_files);
                        echo '</table>';
                    }
                    if (sizeof($version->identifier_files) > 0) {
                        echo '<table><caption>Used To Identify Game</caption>';
                        echo '<tr><th>Path</th><th>Filename</th>';
                        echo '</tr>';
                            printFiles($version->identifier_files);
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
                
                // Side info box
                echo '<div class="contributor_list">';
        
                echo '<h3>This information was contributed by:</h3>';
                foreach ($version->contributors as $contributor) {
                    echo '<p>'.$contributor.'</p>';
                }
                echo '</div>';
                $data = mysql_query("SELECT * FROM "
                                        ."xml_exporters ex"
                                        ." ORDER BY name");

echo '<div class="exports">';

                while($row = mysql_fetch_array($data)) {
                    require_once '../shared/exporters/'.$row['file'];
        
                    echo '<details><summary>'.$row['name'].' XML</summary>';
                    $xml = new DOMDocument();
                    $xml->encoding = 'utf-8';
                    $xml->formatOutput = true;
                    $exporter = new $row['class'];
                    $exporter->xml = $xml;
                    $node = $exporter->exportGameVersion($game_data, $version);
                    $xml->appendChild($node);
                    
                    $geshi = new GeSHi($xml->saveXML($node),'xml');
                    echo '<div class="code">'.$geshi->parse_code().'</div></details>';

                }
echo '</div>';

}
?>
</div>
