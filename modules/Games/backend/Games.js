$(document).ready(function() {
    $('#addMap').click(function() {
        newMap = $.trim($('#gMapInput').val());

        if (newMap != '') {
            if (! $('#mapList ul').length)
                $('#mapList').append('<ul><li>' + newMap + '</li></ul>');
            else
                $('#mapList ul').append('<li>' + newMap + '</li>');

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