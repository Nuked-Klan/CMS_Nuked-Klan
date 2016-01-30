var nbInfo,
    currentInfo = 1,
    delay = 15000;

function infoSlide() {
    $('#slide' + currentInfo).fadeOut();
    $('#slide' + currentInfo).hide();

    currentInfo++

    if (currentInfo > nbInfo) currentInfo = 1;

    $('#slide' + currentInfo).fadeIn();
    setTimeout('infoSlide()', delay);
}

$(document).ready(function() {
    nbInfo = $('#information>div').length;

    for (var n = 2; n < nbInfo + 1; n++)
        $('#slide' + n).hide();

    setTimeout('infoSlide()', delay);
});