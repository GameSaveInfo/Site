<?php
    require_once 'headers.php';
    require_once 'helpers.php';
    require_once 'gamedata/Game.php';
    require_once "gamedata/Games.php";
    
    if(array_key_exists('game',$_GET)) {
        $game = $_GET['game'];
    } else {
        $game = "DeusEx";
    }
    
    if(array_key_exists('letter',$_GET)) {
        $letter = $_GET['letter'];
    } else {
        $letter = substr($game,0,1);
    }
             
    $row;
    $data = $db->Select("games",null,array("name"=>$game),array("name"=>"ASC"));
    if(sizeof($data)==0) {
        $data = $db->Select("games",null,"name LIKE '".$game."%'",array("name"=>"ASC"));
        if(sizeof($data)==0) {
            throw new Exception($game." not found!");
    	}                                            
    }
    $row = $data[0];                        
                
    $name = $row->name;
    $game_data = new Game();
    $game_data->loadFromDb($game, $row, $db);
    
    
?><!DOCTYPE HTML>
<html>
<head>
<title>GameSave.Info - 
<?php
    echo $game_data->title;
?>
</title>

<meta name="description" content="A save game backup and resture utility. Creates easy-to-use archives that can be restored to any supported system." />

<link media="Screen" href="/css/ogsip.css" type="text/css" rel="stylesheet" />
<link media="Screen" href="/css/ui-darkness/jquery-ui-1.9.0.custom.css" type="text/css" rel="stylesheet" />
<link media="Screen" href="/css/gsi.css" type="text/css" rel="stylesheet" />
<link media="Screen" href="/libs/tooltip.css" type="text/css" rel="stylesheet" />
<link media="Screen" href="/libs/popups.css" type="text/css" rel="stylesheet" />


<script type="text/javascript" src="/libs/jquery/jquery-1.8.2.js"></script>
<script type="text/javascript" src="/libs/jquery/jquery-ui-1.9.0.custom.min.js"></script>
<script type="text/javascript" src="/js/gsi.js"></script>
<script type="text/javascript" src="/libs/tooltip.js"></script>
<script type="text/javascript" src="/libs/popups.js"></script>
<script type="text/javascript">
var availableGames = [
<?php
$data = $db->Select("games",array("name","title"),null,array("name"));
 foreach($data as $row) {
    echo '{ label: "'.$row->title.'", value: "'.$row->name.'" },'."\n";
}
$data = $db->Select("game_versions",array("name","title"),"title IS NOT NULL",array("name"));
 foreach($data as $row) {
    echo '{ label: "'.$row->title.'", value: "'.$row->name.'" },'."\n";
}
?>
];

  var _gaq = _gaq || [];
  
<?php
  global $test_mode;

if(!$test_mode) {
echo "
  _gaq.push(['_setAccount', 'UA-32952901-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();";
}


?>
</script>


</head>
<body>

<div class="logo">
<img src="images/logo.png" />
<b style="color:red;">Game</b><b style="color:green;">Save</b><b style="color:yellow;">.</b><b style="color:blue;">Info</b>

<div class="count">
There are currently 
<?php Games::printGameCounts($db) ?>
 in the database
</div>
</div>



<!--
<div class="pointer">
&#9658;
</div>
-->

<div class="log">
Thing
</div>

<div class="game_title">
<?php    
    echo $game_data->title;
    if(strtolower($game_data->type)!="game") {
        echo " (".ucfirst($game_data->type).")";
    }
    if ($game_data->deprecated) {
        echo '<h4>NOTE: This game version has been marked as deprecated</h4>';
    }
 ?>
</div>

<div class="game">
<?php

    // A little data setup
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

    $locations_found  = loadLocations($game_data,$db);


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



    foreach ($game_data->versions as $version) {
        foreach($version->file_types as $file) {
            if(!array_key_exists($file->name,$file_types)) {
                $file_types[$file->name] = $file->inclusions;   
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

    $printed = false;
    if($locations_found) {
        echo '<h2>PC Saves</h2>';
        echo '<div class="locations">';
        if(sizeof($path_locations)>0) {
            $install_output = "";
            $other_output = "";
            $steam_cloud_output = "";
            $uplay_output = "";
            $install_count = 0;
            $other_count = 0;
            $steam_cloud_count = 0;
            $uplay_count = 0;
            foreach ($path_locations as $location) {
                $output = '<li>';
                $output .= getEvDescription($location->ev,$db); 
                $output .= '\\' . $location->path;
                $output .= getCommonPathAttributes($location);
                $output .= '</li>';
                
                switch($location->ev) {
                    case "installlocation":
                    case "steamcommon":
                        $install_output .= $output;
                        $install_count++;
                        break;
                    case "steamuserdata":
                        $steam_cloud_output .= $output;
                        $steam_cloud_count++;
                        break;
                    case "ubisoftsavestorage":
                        $uplay_output .= $output;
                        $uplay_count++;
                        break;
                    default:
                        $other_output .= $output;
                        $other_count++;
                        break;
                }
            }
            if($install_output!="") {
                echo 'Saves can be found in the install folder, ';
                if($install_count==1)
                    echo 'an example of which is';
                else
                    echo 'some examples of which are';
                echo ':<ul>';
                echo $install_output;
                echo '</ul>';
            }
            if($other_output!="") {
                echo 'Saves can ';
                if($install_output!="")
                    echo 'also ';
                echo 'be found in ';
                if($other_count==1)
                    echo 'this location';
                else
                    echo 'these locations';
                echo ':<ul>';
                echo $other_output;
                echo '</ul>';
    
            }
            if($steam_cloud_output!="") {
                echo 'This game ';
                if($install_output!=""||$other_output!="")
                    echo 'also ';
                echo 'uses Steam Cloud, and puts its cloud data in ';
                if($steam_cloud_count==1)
                    echo 'this location';
                else
                    echo 'these locations';
                echo ':<ul>';
                echo $steam_cloud_output;
                echo '</ul>';
    
            }
            if($uplay_output!="") {
                echo 'This game ';
                if($install_output!=""||$other_output!="")
                    echo 'also ';
                echo 'uses UPlay\'s cloud sync, and puts its data in ';
                if($uplay_count==1)
                    echo 'this location';
                else
                    echo 'these locations';
                echo ':<ul>';
                echo $uplay_output;
                echo '</ul>';
    
            }
            
        } else if (sizeof($registry_locations) > 0) {
            echo 'Saves can be found in the install folder, which we unfortunately don\'t have any examples of.<br /><br/>';
        }
        if (sizeof($registry_locations) > 0) {
            echo '<details><summary>';
            if (sizeof($registry_locations) ==1)
                echo 'This registry entry usually points';
            else
                echo 'These registry entries usually point';
            echo ' to the game\'s install folder:</summary>';
            echo '<ul>';
            foreach ($registry_locations as $location) {
                echo '<li>' . strtoupper($location->root) . '\\' . $location->key.'\\';
                if ($location->value == null)
                    echo '(Default)';
                else
                    echo $location->value;
                echo getCommonPathAttributes($location);
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
            echo ' to the game\'s install folder:</summary>';
            echo '<ul>';
            foreach ($shortcut_locations as $location) {
                echo '<li>';
                echo getEvDescription($location->ev,$db);
                echo '\\' . $location->path;
                echo getCommonPathAttributes($location);
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
            echo '<script type="text/javascript"><!--
google_ad_client = "ca-pub-1492999866091035";
/* GameSave.Info */
google_ad_slot = "7844576363";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>';
        
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



</div>
<div class="letters">
<div class="letter"><b class="letter" id="letter0">#</b></div>
<?php
        $letters = Games::getGameLetters($db);
        $i = 1;
        foreach(array_keys($letters) as $letter) {
            if($letter!="numeric")
                echo '<div class="letter"><b class="letter" id="letter'.$i++.'">'.$letter.'</b></div>';
        }
?>
</div>
<div class="games">

</div>

</body>
</html>