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

    if($('#RL_followContent').length == 0){
        $('#RL_commentsContent').css('height', '278px');
    }

    if($('#RL_blockDownload').length == 0){
        $('#RL_commentsContent').parent().parent().css('width', '100%');
    }

    if($('#RL_commentsContent').length == 0 && $('#RL_followContent').length == 0){
        $('#RL_blockDownload').parent().parent().css('width', '100%');
    }

    if(RL_galleryLightbox === true){
        //$('#RL_gallery').children('div').children('figure').children('a').attr('rel', 'shadowbox');
    }

});