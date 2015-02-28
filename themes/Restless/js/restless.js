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
});