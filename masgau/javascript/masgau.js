var home_page = false;
var animation_timing = 200;

function openMenu(menu_name, menu_height) {
        $('#menu_' + menu_name).stop();
      $('#menu_' + menu_name).animate({
        height: menu_height}, animation_timing, function() {
      });
}

function closeMenu(menu_name) {
        $('#menu_' + menu_name).stop();
      $('#menu_' + menu_name).animate({
        height: 0}, animation_timing, function() {
      });
}

function setupMenuAnimation(menu_name, menu_height) {
    $('#menu_' + menu_name).mouseover(function() {
        openMenu(menu_name,menu_height);
    });
    $('#menu_' + menu_name).mouseout(function() {
        closeMenu(menu_name);
    });
    $('#menu_title_' + menu_name).mouseover(function() {
        openMenu(menu_name,menu_height);
    });
    $('#menu_title_' + menu_name).mouseout(function() {
        closeMenu(menu_name);
    });
}

function setUpFades() {
    $('.media_icon').mouseover(function() {
        jQuery(this).children("div").fadeIn(animation_timing);
    });
    $('.media_icon').mouseout(function() {
        jQuery(this).children("div").fadeOut(animation_timing);
    });

}

$(document).ready(function() {
$(".yoxview").yoxview({
    renderInfoPin: false,
    autoHideMenu: false,
    autoHideInfo: false
});
        if(!home_page) {
        setupMenuAnimation('1',180);
        setupMenuAnimation('2',200);
        setupMenuAnimation('3',170);
        setupMenuAnimation('4',100);
    } else {
        openMenu('1',180);
        openMenu('2',200);
        openMenu('3',170);
        openMenu('4',100);
    }
    setUpFades();
});

