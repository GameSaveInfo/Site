var current_hash = true;
var last_name = false;

$(document).ready(function() {
    setInterval(function() {
        if(window.location.hash != current_hash) {
            current_hash = window.location.hash;
            if(current_hash==false) {
                $('#game_list').load("ajax.php?module=game_data",function() {
                        $('.back_link').fadeOut(animation_timing);
                        $('#game_data').fadeOut(animation_timing, function() {
                            $('#game_list').fadeIn(animation_timing);
                        });
                    });
            } else {
                name = current_hash.substring(1);
                $('#game_data').load("ajax.php?module=game_data&name="+name, function() {
                        $('#game_list').fadeOut(animation_timing, function() {
                            $('#game_data').fadeIn(animation_timing);
                            $('.back_link').fadeIn(animation_timing);
                        });
                        prepareTabs();                        
                    });
                name = name.replace('+','_');
                if(last_name!=false) {
                    $('#'+last_name+'_link').removeClass('selected_link');
                }
                $('#'+name+'_link').addClass('selected_link');
                last_name = name;
            }
        }        
    }, 100); 
});

function prepareTabs() {
}

