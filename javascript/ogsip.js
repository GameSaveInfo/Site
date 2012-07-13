

$(document).ready(function() {
	if(window.location.hash == "#" || window.location.hash == "") {
		letter = "A";
	} else if(window.location.hash == "#numeric") {
		letter = "numeric";
	} else {
		letter = window.location.hash.substring(1,2);
        if(isNumber(letter)) {
            letter = "numeric";
        }
	}
	var radio  = document.getElementById("radio"+letter);
	radio.checked = true;
    $("#error").addClass("ui-state-highlight");
	$("#letters").buttonset();

    hashchange();
    window.addEventListener("hashchange", hashchange, false);
    $("#search").autocomplete({ source: availableGames });
    $("#search").autocomplete({ fx: { opacity: 'toggle' } });
    $( "#search" ).autocomplete({
        select: function(event, ui) { 
    	    changeLetter(ui.item.value);
        }
    });
});


function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
var last_letter;
var game;
function hashchange() {
	var game_name = "";
	if(window.location.hash == "#" || window.location.hash == "") {
		letter = "A";
	} else if(window.location.hash == "#numeric") {
		letter = "numeric";
	} else {
		letter = window.location.hash.substring(1,2).toUpperCase();
        if(isNumber(letter)) {
            letter = "numeric";
        }
		game_name = window.location.hash.substring(1).split(";")[0];
	}
	if(letter != last_letter) {
		loadLetter(letter, game_name);
	} else {
		loadGame(game_name);
	}
	last_letter = letter;

}
function loadLetter(letter, game_name) {
	$("#games").load("modules/games.php?letter=" + letter, function(game) {
        var radio = document.getElementById("radio"+letter);
        radio.checked = true;
        
        
		if(game_name.length>1) {
			var radio  = document.getElementById(game_name);
			radio.checked = true;
		} else {
			var radio = document.getElementsByName("game");

			radio[0].checked = true;
		}
	
		$("#games").buttonset();
		loadGame(game_name);
	});
}
var last_game;
function loadGame(game) {
	if(last_game != game ) {
	$("#game").empty();
	$("#game").load("modules/game.php?name=" + game, function() {
		
		$(".game_versions").buttonset();
		setUpToolTips();	
	});
	last_game = game;
	}
}


function changeLetter(letter) {
	if(letter == "#") {
		window.location.hash = "numeric";
	} else {
		window.location.hash = letter;
	}
}

function changeversion(hash) {
	$(".game_version").fadeOut(100,function() {
		var version = $("#"+hash);
		version.fadeIn(100);
	});
}
