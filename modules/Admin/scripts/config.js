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
