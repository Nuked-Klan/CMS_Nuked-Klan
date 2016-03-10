var nbInfo,
    currentInfo = 1,
    delay       = 45000,
    infoHeight  = [];

function infoSlide() {
    $('#slide' + currentInfo).fadeOut();
    $('#slide' + currentInfo).hide();

    currentInfo++

    if (currentInfo > nbInfo) currentInfo = 1;

    $('#slide' + currentInfo).fadeIn();
    setTimeout('infoSlide()', delay);
}

$(document).ready(function() {
    var maxHeight = 0;

    nbInfo = $('#information>div').length;

    for (var n = 1; n < nbInfo + 1; n++) {
        infoHeight[n] = $('#slide' + n).outerHeight(true);

        if (infoHeight[n] > maxHeight)
            maxHeight = infoHeight[n];

        if (n > 1)
            $('#slide' + n).hide();
    }

    $('#information').css('height', maxHeight + 30);
    setTimeout('infoSlide()', delay);
});