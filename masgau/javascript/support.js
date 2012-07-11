var current_hash = false;
var loaded = false;
$(document).ready(function() {
        $('#game_data').fadeOut(animation_timing);
        $('#back_link').fadeOut(animation_timing);
    setInterval(function() {
        if(!window.location.hash&&!loaded) {
            loadLetter("A");
            $('#ajax').load("ajax.php?module=support&filter=A");
            loaded = true;
        } else {
            if(window.location.hash != current_hash) {
                current_hash = window.location.hash;
                name = current_hash.substring(1);
                loadLetter(name);
            }  
        }
        
    }, 100); 
    
    $('.back_link').click(function(event) {
        event.preventDefault(); 
        $('.back_link').fadeOut(animation_timing);
        $('#game_data').fadeOut(animation_timing,function() {
            $('#compat_table').fadeIn(animation_timing);

            });
        
    });

});

function setUpFades() {
    $('.media_icon').mouseover(function() {
        jQuery(this).children("div").fadeIn(animation_timing);
    });
    $('.media_icon').mouseout(function() {
        jQuery(this).children("div").fadeOut(animation_timing);
    });

}

function loadLetter(letter) {
        $('#ajax').fadeOut(animation_timing,function() {
            $('#ajax').load("ajax.php?module=support&filter="+letter, function() {
                $('#ajax').fadeIn(animation_timing);
                $('.game_data_link').click(function(event) {
                    event.preventDefault(); 
                    var url = "ajax.php?module=game_data&name=" + $(this).attr('href');
                    $('#game_data').load(url,function() {
                        $('#compat_table').fadeOut(animation_timing,function() {
                            $('.back_link').fadeIn(animation_timing);
                            $('#game_data').fadeIn(animation_timing);
                        });
                    });
                });
                loaded = true;
            });
        });


}