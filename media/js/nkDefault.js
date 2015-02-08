$(document).ready(function(){
    $('#nkNewPrivateMsgClose').click(function(){
        document.cookie = "popup=false";
        $(this).parent().slideUp(200);
    });
});
