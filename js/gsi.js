$(document).ready(function() {
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
    
});