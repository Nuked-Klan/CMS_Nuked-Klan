$(document).ready(function(){
    $('#nkNewPrivateMsgClose').click(function(){
        document.cookie = "popup=false";
        $(this).parent().slideUp(200);
    });
});

function getEditorContent(id) {
    var textValue;

    if (typeof CKEDITOR === "object")
        textValue = CKEDITOR.instances[id].getData();
    else if (typeof tinyMCE === "object")
        textValue = tinyMCE.get(id).getContent();

    return $(textValue).text();
}

function isEmail(id) {
    //if (document.getElementById(id).value.indexOf(\'@\') == -1)
    if (document.getElementById(id).value.match(/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/))
        return true;

    return false;
}

function getDateData(day, month, year) {
    var dt = new Date();
    dt.setFullYear(parseInt(year));
    dt.setMonth(parseInt(month) - 1);
    dt.setDate(parseInt(day))

    day = dt.getDate();
    month = dt.getMonth() + 1;
    year = dt.getFullYear();

    return new Array(day, month, year);
}

function checkDateValue(language, date, sep) {
    var validDate,
        dateData = date.split(sep);

    if (dateData.length == 3) {
        if (language == 'french') {
            dateData = getDateData(dateData[0], dateData[1], dateData[2]);

            validDate = ('0' + dateData[0]).slice(-2) + sep + 
                ('0' + dateData[1]).slice(-2) + 
                sep + dateData[2];
        }
        else {
            dateData = getDateData(dateData[1], dateData[0], dateData[2]);
            validDate = ('0' + dateData[1]).slice(-2) + sep + ('0' + dateData[0]).slice(-2) + sep + dateData[2];
        }
    }

    if (typeof validDate == 'string' && date == validDate)
        return true;

    return false;
}

function checkTimeValue(time) {
    var timeData = time.split(':');
    console.log(timeData);
    if (timeData.length == 2) {
        hour   = parseInt(timeData[0]);
        minute = parseInt(timeData[1]);

        if (! (hour > 24 || hour < 0 || minute > 60 || minute < 0))
            return true;
    }

    return false;
}
