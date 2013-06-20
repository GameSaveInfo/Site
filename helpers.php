<?php

function remove($string,$remove) {
    $str = trim(substr($string,0,strlen($string)-strlen($remove)),'\\');
    
    if($str=="")
        return null;
    
    return $str;
}
function endsWith($haystack,$needle) {
    $substr = substr($haystack,strlen($haystack)-strlen($needle),strlen($needle));
    return $needle = $substr;
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
        if($version->deprecated=="1") {
            continue;
        }
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


function getEvDescription($name,$db) {
    return '<div class="has_tooltip ev_name">' . $name . '<div class="tooltip">'.Location::getEvDescription($name,$db).'</div></div>';
}

function getCommonPathAttributes($location) {
    $output = '<ul>';
    if (get_class($location) != "PathLocation") {
    	if($location->detract!=null || $location->append !=null ) {	
    		$output .= "<li>BUT you have to";
    
            if ($location->detract != null) {
                $output .= ' detract "' . $location->detract . '" from the path';
        		if($location->append!=null) {
        			$output .= ' AND THEN';
    	    	}	
    		}
    		if ($location->append != null)
                    $output .= ' append "' . $location->append. '"';
    		if($location->detract == null )
    		    $output .= ' to the path';
        	$output .= '</li>';
    	}
	}
    if ($location->only_for != null)
        $output .= '<li>But it only works on ' . $location->only_for . '</li>';

    if ($location->deprecated)
        $output .= '<li>This location is no longer used by the game, but at one time it was!</li>';
	$output .= '</ul>';    
    return $output;
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

?>