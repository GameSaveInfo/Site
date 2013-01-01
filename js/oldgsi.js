$(document).ready(function() {
    adjustHeight();

    $(window).resize(function() {
        adjustHeight();
    });
    
    $("html").mousemove(function(event) {
      var msg = "Handler for .mousemove() called at ";
      msg += event.pageX + ", " + event.pageY;
      $(".log").html(msg);
      menuUpdate(event.pageX,event.pageY);
    });
    
    $('b.letter').click(function() {
        activateLetter($(this).html());
    });
    
  //  activateLetter(currentLetter);

});

var letter_height;

function activateLetter(letter) {
    //$(".games").load("modules/games.php?letter=" + letter, function() {
        //alert('loaded!');
    //});
}


function menuUpdate(mousex, mousey) {
    $(".log").html(mousey + "words!");
          
          var width = $(".letters").width();
          
          if(mousex>width + 50) {
         //  return;   
          }
          
    var height = $(".letters").height();

    
    for (var i=0;i<27;i++)
    {
     //   var half_height = letter_height / 2;
        
        var letter_pos =  $("#letter" + i).position().top + ($("#letter" + i).height() / 2);
        
        //var new_height = letter_height;
        
        var red_color = 255;
        var green_color = 255;
        var blue_color = 255;
        var opacity = 1;
        
        if(letter_pos == mousey) {
            //new_height = new_height * 2;
        } else if (letter_pos > mousey) {
            var dif = letter_pos - mousey;
            dif = 1 - (dif / height) * 4;
            //new_height = (half_height * dif ) + half_height;
            opacity = opacity * dif;
            red_color = red_color * (1 - dif);
            green_color = green_color * (1 - dif);
            blue_color = blue_color * (1 - dif);
            
        } else if ( mousey > letter_pos) {
            var dif = mousey - letter_pos;
            dif = 1 - (dif / height) * 4;
            //new_height = (new_height * dif) + half_height;
            opacity = opacity * dif;
            red_color = red_color * (1 - dif);
            green_color = green_color * (1 - dif);
            blue_color = blue_color * (1 - dif);
            
        }
            
            var new_color = 'rgba(255,255,255,' + opacity + ')';
        
    //    $("#letter" + i).css('background-color',new_color );         
            
            var new_color = 'rgb(' + Math.floor(red_color) + ',' + Math.floor(green_color) + ',' + Math.floor(blue_color) + ')';
      //  $("#letter" + i).css('color',new_color );         
    }
    
    $(".pointer").offset({ top: mousey, left:  $(".letters").width() + 10})
    
    
    
}

function adjustHeight() {
    var height = $(".letters").height();
    $('.games').height(height - 2);
    
    letter_height = (height / 27 );
    $("div.letter").height(letter_height);
    
        
   $('.letters').css('font-size', letter_height); 
   $('.letter').css('font-size', letter_height); 
 
 
   var width = $(".letters").width();
   
    $('.games_drawer').css('left',width + 10 );
    $('.game').css('left',width + 40 );
    $('.game_title').css('left',width + 40 );
    
}


