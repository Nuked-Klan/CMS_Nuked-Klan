// JavaScript Document

var xtralink = 'non';

function maFonctionAjax(texte){
	var OAjax;
	if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
	else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
	OAjax.open('POST','index.php?file=Admin&page=discussion',true);
	OAjax.onreadystatechange = function(){
		if (OAjax.readyState == 4 && OAjax.status==200){
			if (document.getElementById){
				document.getElementById('affichefichier').innerHTML = OAjax.responseText;
				document.getElementById('texte').value = '';
			}
		}
	}
	OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	OAjax.send('texte='+texte+'');
	$(document).trigger('close.facebox');
	redirect("index.php?file=Admin", 1);
}

function screenon(lien,lien2){
	xtralink = lien2;
	document.getElementById('iframe').innerHTML = '<iframe style="border: 0" width="100%" height="80%" src="'+lien+'"></iframe>';
	if(condition_js == 1) screenoff();
	else document.getElementById("screen").style.display="block";
}

function screenoff(){
	document.getElementById('screen').style.display='none';
	if (xtralink != 'non') window.location = xtralink;
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

function maFonctionAjax3(texte){
	var OAjax;
	if (window.XMLHttpRequest) OAjax = new XMLHttpRequest();
	else if (window.ActiveXObject) OAjax = new ActiveXObject('Microsoft.XMLHTTP');
	OAjax.open('POST','modules/'+texte+'/menu/'+lang_nuked+'/menu.php',true);
	OAjax.onreadystatechange = function(){
		if (OAjax.readyState == 4 && OAjax.status==200){
			if (document.getElementById) document.getElementById('1').innerHTML = OAjax.responseText;
		}
	}
	OAjax.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	OAjax.send();
}

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