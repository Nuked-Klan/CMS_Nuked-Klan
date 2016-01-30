$(document).ready(function() {
    $.ajax({
        async: true,
        type: 'POST',
        url: 'index.php?action=getPartners'
    }).done(function(html) {
        $('#partners').css('display', 'none')
            .html(html)
            .fadeIn('slow');
    });
});