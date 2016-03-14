// JavaScript Document

var previewRedirectUrl = 'none';

function maFonctionAjax(texte){
	var OAjax;
	if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
	else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
	OAjax.open('POST','index.php?file=Admin&page=discussion',true);
	OAjax.onreadystatechange = function(){
		if (OAjax.readyState == 4 && OAjax.status==200){
			if (document.getElementById){
				document.getElementById('affichefichier').innerHTML = OAjax.responseText;
                window.location = "index.php?file=Admin";
			}
		}
	}
	OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	OAjax.send('texte='+texte+'');
	$(document).trigger('close.facebox');
}

// Display frontend preview in administration if enabled
function screenon(previewUrl, redirectUrl) {
    previewRedirectUrl = redirectUrl;

    if (frontendPreview == 'off') {
        screenoff();
    }
    else {
        $('#iframe').html('<iframe style="border: 0" width="100%" height="80%" src="' + previewUrl + '"></iframe>');
        $('#screen').css('display', 'block');
    }
}

// Remove frontend preview in administration and redirect if needed
function screenoff(){
    $('#screen').css('display', 'none');

    if (previewRedirectUrl != 'none')
        window.location = previewRedirectUrl;
}

function maFonctionAjax2(texte,type){
	var OAjax;
	if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
	else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
	OAjax.open('POST','index.php?file=Admin&page=notification',true);
	OAjax.onreadystatechange = function(){
		if (OAjax.readyState == 4 && OAjax.status==200){
			if (document.getElementById){
				document.getElementById('texte').value = '';
				document.getElementById('type').value = '';
			}
		}
	}
	OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	OAjax.send('texte='+texte+'&type='+type+'');
	$(document).trigger('close.facebox')
}

$(document).ready(function() {
    $('#adminModuleMenuForm').submit(function() {
        $.ajax({
            type: 'POST',
            url: 'index.php?file=Admin&op=getAdminModuleMenu',
            data: { 'module' : $('#module').val() }
        }).done(function(adminMenu) {
            $('#adminModuleMenu').html(adminMenu);
        });

        return false;
    });
});


function del(id){
	var OAjax;
	if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
	else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
	OAjax.open('POST','index.php?file=Admin&page=notification&op=delete',true);
	OAjax.onreadystatechange = function(){
		if (OAjax.readyState == 4 && OAjax.status==200){
			if (document.getElementById) {}
		}
	}
	OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	OAjax.send('id='+id+'');
}

function getEditorContent(id) {
    var textValue;

    if (typeof CKEDITOR === "object")
        textValue = CKEDITOR.instances[id].getData();
    else if (typeof tinyMCE === "object")
        textValue = tinyMCE.get(id).getContent();

    return $(textValue).text();
}

function isEmail(id) {
    //if (document.getElementById(id).value.indexOf('@') == -1)
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
