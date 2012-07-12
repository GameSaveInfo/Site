<?php
	$site_name = "GameSave.Info";
    $title = $site_name;;
    include_once 'headers.php';
    include_once "gamedata/Games.php";
?>

<!DOCTYPE HTML>
<html>
<head>
<title><?php echo $title; ?></title>
<link media="Screen" href="css/ogsip.css" type="text/css" rel="stylesheet" />
<link media="Screen" href="libs/jquery/css/redmond/jquery-ui-1.8.21.custom.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="libs/jquery/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="libs/jquery/jquery-ui-1.8.21.custom.min.js"></script>
<script type="text/javascript" src="libs/yoxview/yoxview-init.js"></script>
<script type="text/javascript" src="javascript/ogsip.js"></script>
<script type="text/javascript">
var availableGames = [
<?php
$data = $db->Select("games",array("name","title"),null,array("name"));
 foreach($data as $row) {
    echo '{ label: "'.$row->title.'", value: "'.$row->name.'" },';
}
$data = $db->Select("game_versions",array("name","title"),"title IS NOT NULL",array("name"));
 foreach($data as $row) {
    echo '{ label: "'.$row->title.'", value: "'.$row->name.'" },';
}
?>
];

var site_title = "<?php echo $title; ?>";
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-32952901-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
<body>

<h1><?php echo $title; ?></h1>


<h3>There are currently 
<?php Games::printGameCounts($db) ?>
 in the database
</h3>

<div class="search">
<label for="search">Search:</label><input id="search" />
</div>

<form><div id="letters">

<?php
        $letters = Games::getGameLetters($db);

        echo '<input type="radio" id="radionumeric" name="letter" onclick="changeLetter(\'#\')" /><label for="radionumeric">#</label>'."\n";        
        foreach(array_keys($letters) as $letter) {
            if($letter!="numeric")
                echo '<input type="radio" id="radio'.$letter.'" name="letter" onclick="changeLetter(\''.$letter.'\')" \><label for="radio'.$letter.'">'.$letter.'</label>'."\n";
        }
?>
</div></form>

<div id="content">
<div id="games">
</div>
<div id="game">
</div>
</div>

<div id="credits">
The <?php echo $title ?> is mantained by Matthew Barbour and various <a href="">Contributors</a>
</div>

<div id="menu">
<a href="xml_format.php">XML Format</a>
<a href="https://github.com/GameSaveInfo/Data">XML Data Files on GitHub</a>
<a href="https://github.com/GameSaveInfo/Data/blob/master/changelog.txt">Changelog</a>
<a href="https://github.com/GameSaveInfo/Reports">Reports</a>
</div>

</body>
</html>

<?php $db->close(); ?>
