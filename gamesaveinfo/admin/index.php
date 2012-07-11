<?php

    include_once '../headers.php';
    include_once '../../shared/Database.php';
   

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

XML File Import From Data Branch Of GitHub
<form enctype="multipart/form-data" method="post">
<input type="hidden" name="action" value="upload" />
<input name='overwrite' type='checkbox' />Overwrite Existing
<?php
//    $data = AXmlData::RunQuery("SELECT * FROM ".$db.".xml_files ORDER BY name ASC",$con);
//    while ($row = mysql_fetch_assoc($data)) {
//        echo '<br /><input type="radio" name="file"  value="'.$row['git_path'].'">' . $row['git_path'] . ' (Last Updated '.$row['last_updated'].')</input>';

//    }

?>
<br />
<br /><input type="radio" name="file"  value="games.xml">games.xml</input>
<br /><input type="radio" name="file"  value="system.xml">system.xml</input>
<br /><input type="radio" name="file"  value="deprecated.xml">deprecated.xml</input>
<input type="submit" value="IMPORT IT!" />
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

</div>
<?php

    if(isset($_POST['update_time'])) {
        $changelog = $_POST['changelog'];
        Database::Insert("update_history",array("changelog"=>$changelog),"UPDATEING UPDATE HISTROY!!!");
    }



    
    function doImport($file,$schema,$con) {

    }
    
    
    if(isset($_POST['file'])) {
        $file = $_POST['file'];
        $base_url = "https://raw.github.com/GameSaveInfo/Data/master/";
        $schema_url = $base_url.'games.xsd';
        
        echo "<details open='true' style='clear:both;'><summary>".$file."</summary>";
        $url = $base_url.$file;
        
        echo '<div style="width:50%;float:left;">File Load:<br />';
        require_once('../../shared/gamedata/Games.php');
        Games::loadFromXml($url,$schema_url);
        echo '</div>';
        echo '<div style="width:50%;float:left;">';
            Games::writeToDb($db,isset($_POST['overwrite']));
        echo '</div>';
        if(isset($_POST['update_time'])) {
		date_default_timezone_set("UTC");
            AXmlData::UpdateRow($db.'.xml_files',
                                array('git_path'=>$file),
                                array('last_updated'=>date("Y-m-d H:i:s")),
                                $con,"Updating modified time for ".$file);
        }

echo "</details>";
        
    }

?>
</body>
