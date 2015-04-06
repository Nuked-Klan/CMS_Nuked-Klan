$(document).ready(function() {
    $('#RL_connectButton').click(function(e){
        e.preventDefault();
        $('#RL_loginPopupContainer').fadeIn(function(){
            $('#RL_loginPopupContainer>form').slideDown();
        });
    });

    $('#RL_loginPopupContainer').click(function(){
        $('#RL_loginPopupContainer>form').slideUp(function(){
            $('#RL_loginPopupContainer').fadeOut();
        });
    });

    $('#RL_loginPopupContainer>form').click(function(e){
        e.stopPropagation();
    });

    $('#RL_sliderPrev').click(function(e){
        e.preventDefault();
        moveSlider('left');
    })

    $('#RL_sliderNext').click(function(e){
        e.preventDefault();
        moveSlider('right');
    })

    if($('#RL_followContent').length == 0){
        if($('#RL_blockDownload').length > 0) {
            $('#RL_commentsContent').css('height', (parseInt($('#RL_blockDownload').css('height')) + 12));
        }
        else{
            $('#RL_commentsContent').css('height', '278px');
        }
    }
    else{
        $('#RL_commentsContent').css('height', (parseInt($('#RL_blockDownload').css('height')) - 105));
    }

    if($('#RL_blockDownload').length == 0){
        $('#RL_commentsContent').parents('div:first').css('width', '100%');
    }

    if($('#RL_commentsContent').length == 0 && $('#RL_followContent').length == 0){
        $('#RL_blockDownload').parents('div:first').css('width', '100%');
    }


});

function moveSlider(side){
    var maxWidth = parseInt($('#RL_slider').css('width'));
    var currentLeft = parseInt($('#RL_slider').attr('data-left'));
    var elementWidth = parseInt($('#RL_sliderWrapper').attr('data-width'));
    var newValue = null;

    $('.RL_sliderCurrent').removeClass('RL_sliderCurrent');

    if(side == 'left'){
        if((currentLeft + elementWidth)  > 0){
            newValue = parseInt('-'+(maxWidth-elementWidth));
            $('#RL_slider').css('left', newValue);
        }
        else {
            newValue = parseInt(currentLeft + elementWidth);
            $('#RL_slider').css('left', newValue);
        }
    }

    if(side == 'right'){
        if((currentLeft - elementWidth)  <= parseInt('-'+maxWidth)){
            newValue = 0;
            $('#RL_slider').css('left', "0px");
        }
        else{
            newValue = parseInt(currentLeft - elementWidth);
            $('#RL_slider').css('left', newValue);
        }
    }

    var idElement = (Math.abs(newValue)/elementWidth) + 1;
    $('#RL_sliderElement'+idElement).addClass('RL_sliderCurrent');

    $('#RL_slider').attr('data-left', newValue);
}