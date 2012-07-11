<?php
include_once '../headers.php';
include_once '../../shared/gamedata/Games.php';
$letter = $_GET['letter'];

$games = Games::getGamesForLetter($letter,$db);

foreach($games as $game) {
	echo '<input type="radio" id="'.$game->name.'" name="game" onclick="window.location.hash = \''.$game->name.'\'" /><label for="'.$game->name.'">'.$game->title.'</label>';
}
?>
