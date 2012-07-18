$(document).ready(function() {
	if(window.location.hash == "#" || window.location.hash == "") {
		letter = "D";
        game_name = "DeusEx";

	} else if(window.location.hash == "#numeric") {
		letter = "numeric";
	} else {
		letter = window.location.hash.substring(1,2).toUpperCase();;
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
    $( "#search" ).click(function() {
        document.getElementById("search").value = "";
    });
    
});


function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}
var last_letter;
var game;
function hashchange() {
    document.getElementById("search").value = "Search...";
    $("#search").blur();
	var game_name = "";
	if(window.location.hash == "#" || window.location.hash == "") {
		letter = "D";
        game_name = "DeusEx";
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
        $("#games").buttonset('refresh');
	}
	last_letter = letter;

}
function loadLetter(letter, game_name) {
	$("#games").load("modules/games.php?letter=" + letter, function(game) {
        var radio = document.getElementById("radio"+letter);
        radio.checked = true;
        
        
	
        $("#letters").buttonset('refresh');
		loadGame(game_name);
        var tmp = $("#" + radio.id + "_label");
        
	});
}
var last_game;
function loadGame(game) {
	if(last_game != game ) {
        
    	if(game.length>1) {
			var radio  = document.getElementById(game);
			radio.checked = true;
		} else {
			var radio = document.getElementsByName("game");
            radio = radio[0];
			radio.checked = true;
		}
        $("#games").buttonset();

        $("#game").fadeOut(500);
        $(".spinner").fadeIn(500);

    	$("#game").empty();
    	$("#game").load("modules/game.php?name=" + game, function() {
            last_game = game;
            
            $("#game").stop(true, true).fadeIn(500);
            $(".spinner").stop(true, false).fadeOut(500);
            
            var game_label = $("#" + game + "_label");
            if(game_label.offset()!=null) {
                 $('#games').animate({
                    scrollTop: game_label.offset().top - $("#games").offset().top + $('#games').scrollTop() - 10
                }, 500);
            } else {
                 $('#games').animate({
                    scrollTop: 0
                }, 500);
            }

    		setUpToolTips();	
    	});    
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
