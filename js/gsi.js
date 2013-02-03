$(document).ready(function() {
    if(window.location.hash == "#" || window.location.hash == "") {
	} else if(window.location.hash == "#numeric") {
	} else {
    	game_name = window.location.hash.substring(1).split(";")[0];
        var path = "http://" + document.domain + "/" + game_name + "/";
        window.location = path;
        
	}

    $("#search").autocomplete({ source: availableGames });
    $("#search").autocomplete({ fx: { opacity: 'toggle' } });
    $( "#search" ).autocomplete({
        select: function(event, ui) { 
            var path = "http://" + document.domain + "/" + ui.item.value + "/";
            window.location = path;
        }
    });
    $( "#search" ).click(function() {
        document.getElementById("search").value = "";
    });
    
    $(".game_list").click(function(event) {
        $(".list_of_games").hide();
        var temp = $(this).children();
        temp.show();
    });
    
});