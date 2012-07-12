
<?php
include_once('../../DBSettings.php');	
include_once('../headers.php');

	$letters = mysql_query("SELECT substr(name,1,1) as letter, COUNT(name) as count FROM games WHERE type NOT in ('system') GROUP BY letter ORDER BY letter ASC");
        while($row = mysql_fetch_array($letters)) {
		if(!is_numeric($row['letter']))
		echo '<input type="radio" id="'.$row['letter']."\>".$row['letter'].'</input>';                
        }
	echo '<input type="radio" id="#" name="letter">#</input>';                
?>
