<?php
include_once '../headers.php';
include_once '../gamedata/Games.php';
$letter = $_GET['letter'];

$games = Games::getGamesForLetter($letter,$db);

foreach($games as $game) {
	echo '<a href="index.php?letter='.$letter.'&game='.$game->name.'" class="game">'.$game->title.'</a><br/>';
}
?>
