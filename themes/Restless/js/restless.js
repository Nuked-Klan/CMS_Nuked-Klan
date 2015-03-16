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