
<?php
include_once '../headers.php';

function listFormatter($list) {
    $string = "";
    for($i = 0; $i < sizeof($list); $i++) {
    	if($i==0) {
    		$string = $list[$i];
    	} else {
    		if($i==sizeof($list)-1) {
    			$string .= " and ";
    		} else {
    			$string .= ", ";
    		}
    		$string .= $list[$i];
    	}	
    }
    return $string;
}

function printCommonPathAttributes($location) {
	
	echo '<ul>';
    if (get_class($location) != "PathLocation") {
    	if($location->detract!=null || $location->append !=null ) {	
    		echo "<li>BUT you have to";
    
            if ($location->detract != null) {
                echo ' detract ' . $location->detract . ' from the path';
        		if($location->append!=null) {
        			echo ' AND THEN';
    	    	}	
    		}
    		if ($location->append != null)
                    echo ' append ' . $location->append. '';
    		if($location->detract == null )
    		    echo ' to the path';
        	echo '</li>';
    	}
	}
    if ($location->only_for != null)
        echo '<li>But it only works on ' . $location->only_for . '</li>';

    if ($location->deprecated)
        echo '<li>This location is no longer used by the game, but at one time it was!</li>';
	echo '</ul>';    
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

function printEv($name) {
    global $ev;
    echo '<div class="has_tooltip">%' . strtoupper($name) . '%<div class="tooltip">'.$ev[$name].'</div></div>';
}

function exportFiles($files) {
	echo '<ul>';
    foreach($files as $file) {
        echo '<li>';
        if($file->filename==null) {
            if($file->path==null) {
                echo 'All the files in all the subfolders';
            } else {
                echo 'All the files in the "'.$file->path.'" subfolder';   
            }
        } else if($file->path==null) {
            echo 'All the files in the location that match "'.$file->filename;
        } else {
            echo 'The files in the "'.$file->path.'" subfolder that match "'.$file->filename.'"';   
        }
        echo '</li>';
    }
    echo '</ul>';
    
    
}


global $ev;
$ev = array();
$data = $db->Select("game_environment_variables",null,null,null);
foreach($data as $row) {
	$ev[$row->name] = $row->description;
}

$name = $_GET["name"];
         
require_once '../../shared/gamedata/Game.php';
$row;
$data = $db->Select("games",null,array("name"=>$name),array("name"=>"ASC"));
if(sizeof($data)==0) {
    $data = $db->Select("games",null,"name LIKE '".$name."%'",array("name"=>"ASC"));
    if(sizeof($data)==0) {
    	throw new Exception($name." not found!");
	}                                            
}
$row = $data[0];                        
            
$name = $row->name;
$game_data = new Game();
$game_data->loadFromDb($name, $row, $db);

        echo '<h1>'.$game_data->title;
if($game_data->type!="Game")
	echo " (".ucfirst($game_data->type).")";
echo '</h1>';    

if ($game_data->deprecated) {
    echo '<h4>NOTE: This game version has been marked as deprecated</h4>';
}
echo '<div class="game_versions">';            

$i = 0;
foreach ($game_data->versions as $version) {
echo '<input type="radio" id="radio'.$version->generateHash().'" name="version"';
if($i == 0) {
echo ' checked="checked"';
}
echo ' onclick="changeversion(\''.$version->generateHash().'\')" /><label for="radio'.$version->generateHash().'">'.$version->getVersionTitle().'</label>';
$i++;
}
echo "</div>";

$i = 0;
foreach ($game_data->versions as $version) {
if($i==0) {              
    echo '<div class="game_version" style="display:block;" id="'.$version->generateHash().'">';
                } else {
    echo '<div class="game_version" id="'.$version->generateHash().'">';
}
$i++;
// Begin title code

// End title code

if ($version->virtualstore=="ignore") {
    echo '<h4>NOTE: This game version does not recognize VirtualStore folders.</h4>';
}
if ($version->detect=="required") {
    echo '<h4>NOTE: Restoring this game version requires there to already be saves on the system.</h4>';
}

echo '<div class="locations">';


//PS codes
if (sizeof($version->ps_codes) > 0) {

$first_export = true;
$first_psp = true;
    foreach ($version->ps_codes as $path) {
switch($version->os) {
case "PS3":
case "PS2":
case "PS1":
	if($first_export) {
		echo '<details open="open"><summary>When exported to a USB drive from a PS3, the saves are in these locations:</summary>';
		$first_export = false;
        echo '<ul>';
	}
                echo '<li>';
	if($version->os=="PS3")
		echo "PS3\\SAVEDATA";
	else
		echo "PS3\\EXPORT\\PSV";
	echo '\\BA' . $path->prefix . '?' . $path->suffix;

	if($path->append!=null) {
		echo $path->append;
	}
	echo '*';
	if($version->os=="PS3")
		echo '\\';
	if($path->type!=null) {
		echo " (Contains ".$path->type." Data)";
	}

	break;
}

    }
if(!$first_export) 
echo '</ul></details>';
    foreach ($version->ps_codes as $path) {
        switch($version->os) {
                case "PS1":
case "PSP":
                        if($first_psp) {
                                echo '<details open="open"><summary>When saved to a PSP memory stick, the saves are in these folders:</summary>';
    				echo '<ul>';
                        $first_psp = false;
                        }
                        echo '<li>';
                        echo 'PSP\\SAVEDATA\\' . $path->prefix . $path->suffix;
                        if($path->append!=null) {
                                echo $path->append;
                        }
                        echo '*\\';
                        if($path->type!=null) {
                                echo " (Contains ".$path->type." Data)";
                        }
                        
                        break;
        }
        
    }
if(!$first_psp)
echo '</ul></details>';
}

// Paths




if (sizeof($version->locations) > 0) {
    if (sizeof($version->path_locations) > 0) {
    echo '<details open="open"><summary>Saves can be found in these locations:</summary>';
echo '<ul>';
        foreach ($version->path_locations as $location) {
            echo '<li>';
             printEv($location->ev); 
            echo '\\' . $location->path;
            printCommonPathAttributes($location);
            echo '</li>';
        }
        echo '</ul>';
    }
    if (sizeof($version->registry_locations) > 0) {
    echo '<details><summary>These registry entries usually point to where the game keeps its saves:</summary>';
        echo '<ul>';
foreach ($version->registry_locations as $location) {
            echo '<li>' . strtoupper($location->root) . '\\' . $location->key.'\\';
            if ($location->value == null)
                echo '(Default)';
            else
                echo $location->value;
            printCommonPathAttributes($location);
            echo '</tr>';
        }
        echo '</ul></details>';
    }
    if (sizeof($version->shortcut_locations) > 0) {
    echo '<details><summary>These shortcuts usually point to where the game keeps its saves:</summary>';
        echo '<ul>';
foreach ($version->shortcut_locations as $location) {
            echo '<li>';
printEv($location->ev);
echo '\\' . $location->path;
            printCommonPathAttributes($location);
            echo '</li>';
        }
        echo '</ul></details>';
    }
    if (sizeof($version->game_locations) > 0) {
    echo '<details open="open"><summary>This game shares a save location with another game:</summary>';
        echo '<ul>';
foreach ($version->game_locations as $location) {
            echo '<li><a href="#'.$location->name.'">'. $location->name . '</a>';
            printCommonPathAttributes($location);
            echo '</li>';
        }
        echo '</ul></details>';
    }
}
    if (sizeof($version->scumm_vm) > 0) {
    echo '<details open="open"><summary>Possible ScummVM names for this game include:</summary>';
        echo '<ul>';
foreach ($version->scumm_vm as $location) {
            echo '<li>'. $location->name;
            echo '</li>';
        }
        echo '</ul></details>';
    }

echo '</div><div class="files">';

if (sizeof($version->file_types) > 0) {
	foreach($version->file_types as $type) {
		echo '<details open="open"><summary>The '.$type->name.' files are:</summary>';
        exportFiles($type->files);
        echo '</details>';
	}
}                

echo '</div><div class="version_footer">';

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
echo '<h3>This information was contributed by ';

echo listFormatter($version->contributors);
echo '</h3>';  


echo '</div>';

/*
$data = mysql_query("SELECT * FROM "
                        ."xml_exporters ex"
                        ." ORDER BY name");
echo '<h3>Here\'s the export of this game for some game save backup programs:</h3>';
while($row = mysql_fetch_array($data)) {
    require_once '../shared/exporters/'.$row['name'].'.php';

    echo '<details><summary>'.$row['title'].' XML</summary>';
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
*/
echo '</div>';
?>
<script type="text/javascript">

document.title = site_title + " - <?php echo htmlspecialchars($game_data->title) ?>";

</script>

<?php

echo '</div>';
}
?>

