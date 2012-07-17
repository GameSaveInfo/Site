
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
                echo ' detract "' . $location->detract . '" from the path';
        		if($location->append!=null) {
        			echo ' AND THEN';
    	    	}	
    		}
    		if ($location->append != null)
                    echo ' append "' . $location->append. '"';
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

function printEv($name,$db) {
    echo '<div class="has_tooltip ev_name">' . $name . '<div class="tooltip">'.Location::getEvDescription($name,$db).'</div></div>';
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
        } else {
            if(strstr($file->filename,"*")||strstr($file->filename,"?")) {
                if($file->path==null) {
                    echo 'All the files that match "'.$file->filename;
                } else {
                    echo 'The files in the "'.$file->path.'" subfolder that match "'.$file->filename.'"';   
                }
            } else {
                if($file->path==null) {
                    echo 'The file named "'.$file->filename;
                } else {
                    echo 'The file named "'.$file->filename.'" in the "'.$file->path.'" subfolder';   
                }
            }
        }
        echo '</li>';
    }
    echo '</ul>';
}

function printFile($file) {
    
}



$name = $_GET["name"];
         
require_once '../gamedata/Game.php';
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
//echo '<div class="game_versions">';            

//$i = 0;
//foreach ($game_data->versions as $version) {
//    echo '<input type="radio" id="radio'.$version->generateHash().'" name="version"';
//    if($i == 0) {
//        echo ' checked="checked"';
//    }
//    echo ' onclick="changeversion(\''.$version->generateHash().'\')" /><label for="radio'.$version->generateHash().'">'.$version->getVersionTitle().'</label>';
//    $i++;
//}
//echo "</div>";

$i = 0;

global $locations_found;
$locations_found = false;

global $path_locations;
global $registry_locations;
global $shortcut_locations;
global $game_locations;

$path_locations = array();
$registry_locations = array();
$shortcut_locations = array();
$game_locations = array();
$scumm_vm = array();
$ps3_codes = array();
$psp_codes = array();
$contributors = array();
$file_types = array();

function endsWith($haystack,$needle) {
    $substr = substr($haystack,strlen($haystack)-strlen($needle),strlen($needle));
    return $needle = $substr;
}
function remove($string,$remove) {
    $str = trim(substr($string,0,strlen($string)-strlen($remove)),'\\');
    
    if($str=="")
        return null;
    
    return $str;
}

function ajdustLocation($location, $parent_location = null) {
    if(!is_null($parent_location)) {
        if(!is_null($parent_location->detract)) {
            if(get_class($location)=="PathLocation") {
                $location->path = remove($location->path,$parent_location->detract);
            } else {
                if(!is_null($location->append)) {
                    if(endsWith($location->append,$parent_location->detract)) {
                        $location->append = remove($location->append,$parent_location->detract);
                    } 
                } else if(is_null($location->detract)) {
                    $location->detract = $parent_location->detract;
                }
                
            }
        }
        if(!is_null($parent_location->append)) {
            if(get_class($location)=="PathLocation") {
                $location->path .= "\\".$parent_location->append;
            } else {
                if(is_null($location->append)) {
                    $location->append = $parent_location->append;
                }
            }

        }    
    }
    return $location;
}

function loadLocations($game_data, $db, $parent_location = null) {
    global $path_locations;
    global $registry_locations;
    global $shortcut_locations;
    global $game_locations;
    $locations_found = false;
    foreach ($game_data->versions as $version) {
        if(!is_null($parent_location)) {
            if(!$version->versionMatch($parent_location))
            continue;
        }
        
        foreach ($version->path_locations as $location) {
            $locations_found = true;
            array_push($path_locations,ajdustLocation($location,$parent_location));
        }
        foreach ($version->registry_locations as $location) {
            $locations_found = true;
            array_push($registry_locations,ajdustLocation($location,$parent_location));
        }
        foreach ($version->shortcut_locations as $location) {
            $locations_found = true;
            array_push($shortcut_locations,ajdustLocation($location,$parent_location));
        }
        foreach ($version->scumm_vm as $location) {
            $locations_found = true;
            array_push($scumm_vm,ajdustLocation($location,$parent_location));
        }
        foreach ($version->game_locations as $location) {
            $locations_found = true;
            $data = $db->Select("games",null,array("name"=>$location->name),array("name"=>"ASC"));
            $row = $data[0];                        
            $parent_name = $row->name;
            $parent_data = new Game();
            $parent_data->loadFromDb($parent_name, $row, $db);
            loadLocations($parent_data,$db,$location);
        }
    }
    return $locations_found;
}


$locations_found  = loadLocations($game_data,$db);

foreach ($game_data->versions as $version) {


    foreach($version->file_types as $file) {
        if(!array_key_exists($file->name,$file_types)) {
            $file_types[$file->name] = $file->files;   
        }
    }

//PS codes
if (sizeof($version->ps_codes) > 0) {
    foreach ($version->ps_codes as $path) {
        switch($version->os) {
            case "PS3":
            case "PS2":
            case "PS1":
                array_push($ps3_codes,$path);
                break;
        }
        switch($version->os) {
            case "PS1":
            case "PSP":
                array_push($psp_codes,$path);
                break;
        }
    }
}


foreach($version->contributors as $contrib) {
    if(!in_array($contrib,$contributors))
        array_push($contributors,$contrib);   
}

}

echo '<div class="game_version">';
$printed = false;
if($locations_found) {
    echo '<h2>PC Saves</h2>';
    echo '<div class="locations">';
    if(sizeof($path_locations)>0) {
        echo '<details open="open"><summary>Saves can be found in ';
        if(sizeof($path_locations)>0)
            echo 'this location';
        else
            echo 'these locations';
        echo ':</summary>';
        echo '<ul>';
        foreach ($path_locations as $location) {
            echo '<li>';
            printEv($location->ev,$db); 
            echo '\\' . $location->path;
            printCommonPathAttributes($location);
            echo '</li>';
        }
        echo '</ul></details>';
    }
    if (sizeof($registry_locations) > 0) {
        echo '<details><summary>';
        if (sizeof($registry_locations) ==1)
            echo 'This registry entry usually points';
        else
            echo 'These registry entries usually point';
        echo ' to where the game keeps its saves:</summary>';
        echo '<ul>';
        foreach ($registry_locations as $location) {
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
    if (sizeof($shortcut_locations) > 0) {
        echo '<details><summary>';
        if (sizeof($shortcut_locations) == 1) 
            echo 'This shortcut usually points';
        else
            echo 'These shortcuts usually point';
        echo ' to where the game keeps its saves:</summary>';
        echo '<ul>';
        foreach ($shortcut_locations as $location) {
            echo '<li>';
            printEv($location->ev,$db);
            echo '\\' . $location->path;
            printCommonPathAttributes($location);
            echo '</li>';
        }
        echo '</ul></details>';
    }
    if (sizeof($scumm_vm) > 0) {
        echo '<details open="open"><summary>Possible ScummVM names for this game include:</summary>';
        echo '<ul>';
        foreach ($scumm_vm as $location) {
            echo '<li>'. $location->name;
            echo '</li>';
        }
        echo '</ul></details>';
    }


    echo '</div><div class="files">';
    
    if (sizeof($file_types) > 0) {
        foreach(array_keys($file_types) as $type) {
    		echo '<details open="open"><summary>The '.$type.' files are:</summary>';
            exportFiles($file_types[$type]);
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
    $printed = true;
}
if($printed) {
    $printed = false;
    echo '<hr />';
}
if(sizeof($ps3_codes)>0||sizeof($psp_codes)>0) {
    echo '<h2>PlayStation Saves</h2>';

}

if(sizeof($ps3_codes)>0) {
    echo '<details open="open"><summary>When exported to a USB drive from a PS3, the saves are in ';
    if(sizeof($ps3_codes)==1)
        echo 'this location';
    else
        echo 'these locations';
    echo ':</summary>';
    echo '<ul>';
    foreach($ps3_codes as $code) {
            echo '<li>';
            if($code->disc!=null) {
                echo "Disc ".$code->disc.": ";
            }
            if($version->os=="PS3")
        		echo "PS3\\SAVEDATA";
        	else
        		echo "PS3\\EXPORT\\PSV";
        	echo '\\BA' . $code->prefix . '?' . $code->suffix;
        	if($code->append!=null) {
        		echo $code->append;
        	}
        	echo '*';
        	if($version->os=="PS3")
        		echo '\\';
        	if($code->type!=null&&$code->type!="Saves") {
        		echo " (Contains ".$code->type." Data)";
        	}
            echo "</li>";

    }
    echo '</ul></details>';
}
if(sizeof($psp_codes)>0) {
    echo '<details open="open"><summary>When saved to a PSP memory stick, the saves are in ';
    if(sizeof($psp_codes)==1)
        echo 'this location';
    else
        echo 'these locations';
    echo ':</summary>';
    echo '<ul>';
    foreach($psp_codes as $code) {
            echo '<li>';
            if($code->disc!=null) {
                echo "Disc ".$code->disc.": ";
            }
            echo 'PSP\\SAVEDATA\\' . $code->prefix . $code->suffix;
            if($code->append!=null) {
                echo $code->append;
            }
            echo '*\\';
            if($code->type!=null&&$code->type!="Saves") {
                    echo " (Contains ".$code->type." Data)";
            }
            echo "</li>";
    }
    echo '</ul></details>';
    $printed = true;
}

if($printed) {
    $printed = false;
    echo '<hr />';
}

echo '<div class="contributor_list">';
echo '<h3>This information was contributed by ';

echo listFormatter($contributors);
echo '</h3>';  


echo '</div>';


echo '</div>';




// Side info box

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

