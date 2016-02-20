$(document).ready(function() {
    $('#addMap').click(function() {
        newMap = $.trim($('#gMapInput').val());

        if (newMap != '') {
            $('#mapList').append(newMap + '<br />');

            var mapList = $('#map').val();

            if (mapList == '')
                $('#map').val(mapList + newMap);
            else
                $('#map').val(mapList + '|' + newMap);

            $('#gMapInput').val('');
        }
    });

    $('#resetMapList').click(function() {
        $('#mapList').empty();
        $('#map').val('');
        $('#gMapInput').val('');
    });
});