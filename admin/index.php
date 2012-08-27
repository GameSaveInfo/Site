<?php

    include_once '../headers.php';
    if(!class_exists("Database")) {
        include_once '../libs/Database.php';
   }
   

    if(isset($_POST['erase_game_safety'])) {
        
        if($_POST['erase_game']=="ALL GAME IN DATABASE") {
//            $result = AXmlData::SelectRow('games',"name",array("for"=>null,"follows"=>null),null,$con);
            
  //          while ($row = mysql_fetch_assoc($result)) {
                $db->Delete('games',null,"Deleting ALL From Database");
    //        }
        } else {
            $game = $_POST['erase_game'];
            $db->Delete('games',array("name"=>$game),"Deleting ".$game." From Database");
//            if(array_key_exists($game,$_POST)&&$_POST[$game]=="ALL") {
  //              $db->Delete('games',array('name'=>$_POST['erase_game']),"Deleting Game ".$_POST['erase_game']." From the Database");
    //        } else {
      //          $db->Delete('game_versions',array('id'=>$_POST[$game]),"Deleting Game ".$_POST['erase_game']."'s version ID ".$_POST[$game]." From the Database");
        //    }
        }
    }
?>
<style>
details details {
    margin-left:15px;
}
.version_select {
    display:none;
}
</style>
<script type="text/javascript" src="../javascript/jquery-1.7.2.min.js"></script>
<script type="text/javascript">

$(document).ready(function() {
    $('#game_select').change(function() {
        $('.version_select').hide();
        if($(this).val()!="ALL GAME IN DATABASE") {
            $('#'+$(this).val()).show();
        }
    });
    
    $('.version_select').load("ajax.php?module=game_data");
});
</script>

<body style="background-color:black;color:white;">
<div style="width:50%;float:left;">


<?php

        global $test_mode;
        global $branch;
        if($test_mode)
            $branch = "master/";
        else
            $branch = "update/";        

    echo 'XML File Import From '.$branch.' Branch Of GitHub';
?>
<form enctype="multipart/form-data" method="post">
<input type="hidden" name="action" value="upload" />
<input type="hidden" name="overwrite_existing" value="false" />
<!--<input name='overwrite' type='checkbox' />Overwrite Existing-->
<?php
//    $data = AXmlData::RunQuery("SELECT * FROM ".$db.".xml_files ORDER BY name ASC",$con);
//    while ($row = mysql_fetch_assoc($data)) {
//        echo '<br /><input type="radio" name="file"  value="'.$row['git_path'].'">' . $row['git_path'] . ' (Last Updated '.$row['last_updated'].')</input>';

//    }

?>
<br />

Import Limit: <input type="text" name="add_game_limit" value="500" /><br />

<select name="file" id="file">
<option>ALL XML FILES</option>
<option>new.xml</option><?php
$files = array("system.xml","deprecated.xml",);
$alphas = range('a', 'z');
array_push($files, "numeric.xml");
foreach($alphas as $alpha) {
    array_push($files, $alpha.".xml");
}
$cur_file = null;
if(isset($_POST['file'])) {
    $cur_file = $_POST['file'];
}
$select_next = false;

foreach($files as $file) {
    if($select_next)
        echo '<option value="'.$file.'" selected="true">' . $file . '</option>';
    else
        echo '<option value="'.$file.'">' . $file . '</option>';
        
    $select_next = $file==$cur_file;
}
?>

</select><br />
<input type="submit" value="IMPORT IT!" />
</form>
<form enctype="multipart/form-data" method="post">
<input type="hidden" name="add_game_limit" value="20000" />
<input type="hidden" name="action" value="upload" />
<input type="hidden" name="overwrite_existing" value="true" />
<input type="hidden" name="file" id="file" value="new.xml">
<input type="submit" value="IMPORT NEW.XML WHICH WILL OVERWRITE EXISTING GAME ENTRIES" />
</form>

DECLARE AN UPDATE! CHANGELOG:<br/>
<form enctype="multipart/form-data" method="post">
<input type="hidden" name="update_time" value="update" />
<textarea rows="5" cols"50" name="changelog"></textarea>
<input type="submit" value="INITIATE, I SAY!" />
</form>
</div>

<div style="width:50%;float:left;">
DATA PURGE, BEEYOTCH!<br />
<form enctype="multipart/form-data" method="post">
Erase game
<input type="checkbox" name="erase_game_safety" />Safety<br />
<select name="erase_game" id="erase_game">
<option selected="true">ALL GAME IN DATABASE</option>
<?php 
    $data = $db->Select("games",null,null,array("name"));
    foreach ($data as $row) {
        echo '<option value="'.$row->name.'">' . $row->name . '</option>';
    }
?>
</select><br />
<input type="submit" /></form><br/>

Upload a save archive!!<br />
<form enctype="multipart/form-data" method="post">
<input type="file" name="archive_upload" /><br/>
Description:
<textarea rows="5" cols"50" name="archive_description"></textarea>
<br />
<input type="submit" /></form><br/>



</div>
<?php

    if(isset($_POST['update_time'])) {
        $changelog = $_POST['changelog'];
        $db->Insert("update_history",array("changelog"=>$changelog),"UPDATEING UPDATE HISTROY!!!");
    }
        
    function loadfile($file, $open = true) {
        global $branch;
        $base_url = "https://raw.github.com/GameSaveInfo/Data/".$branch;
                $schema_url = $base_url.'GameSaveInfo20.xsd';
        echo "<details ";
        if($open)
            echo "open='true' ";
        
        echo "style='clear:both;'><summary>".$file."</summary>";
        $url = $base_url.$file;
        
        require_once('../gamedata/Games.php');
        Games::loadFromXml($url,$schema_url);
        echo "</details>";


    }
    
    
    if(isset($_POST['file'])) {
        $file = $_POST['file'];
        $max = $_POST['add_game_limit'];

        echo '<table style="clear:both;"><tr><td style="clear:both;vertical-align:top;width:48%">';
        if($file=="ALL XML FILES") {
            foreach($files as $file) {
                loadfile($file, false);
                
            }
        } else {
            loadfile($file);
        }            
        echo '</td><td style="clear:both;vertical-align:top;width:48%">';
        echo "Importing up to ".$max." entries<br/>";
        $overwrite_existing = false;
        if($_POST['overwrite_existing']=="true") {
            $overwrite_existing = true;
        }        
        Games::writeToDb($db,$max, $overwrite_existing);
        
        
        echo Game::$total_added." added<br/>".Game::$total_updated." updated";
        if(isset($_POST['update_time'])) {
		date_default_timezone_set("UTC");
            AXmlData::UpdateRow($db.'.xml_files',
                                array('git_path'=>$file),
                                array('last_updated'=>date("Y-m-d H:i:s")),
                                $con,"Updating modified time for ".$file);
        }

        echo '</td></tr></table>';
    }

?>
</body>
