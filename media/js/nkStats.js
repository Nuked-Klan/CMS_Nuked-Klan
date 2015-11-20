$(document).ready(function() {
    $.ajax({
        url: 'index.php',
        data:'ajax=sendNkStats',
        type: 'GET',
        success: function(html) { }
    });
});