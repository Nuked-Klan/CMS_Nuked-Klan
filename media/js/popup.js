function showsup(){
    if (document.getElementById("popup_dhtml") && ScanCookie("popup") == 0){
		document.getElementById("popup_dhtml").style.width = larg+"px";
		document.getElementById("popup_dhtml").style.height = haut+"px";
		document.getElementById("popup_dhtml").innerHTML = html_haut+html_mil+html_bas;
		document.getElementById("popup_dhtml").style.visibility = "visible";
		setTimeout("closeSup()", 30000);
		CreationCookie("popup", false);
    }
}

function closeSup(){
    if (document.getElementById("popup_dhtml")){
		document.getElementById("popup_dhtml").style.width = 1+"px";
		document.getElementById("popup_dhtml").style.height = 1+"px";
		document.getElementById("popup_dhtml").innerHTML = "";
		document.getElementById("popup_dhtml").style.visibility = "hidden";
    }
}

function ScanCookie(variable){
    cook = document.cookie;
    variable += "=";
    place = cook.indexOf(variable,0);

    if (place <= -1){
		return("0");
    }
    else{
		end = cook.indexOf(";", place)
		if (end <= -1) return(unescape(cook.substring(place+variable.length,cook.length)));
		else return(unescape(cook.substring(place+variable.length,end)));
    }
}

function CreationCookie(nom, valeur, permanent){
    if (permanent){
		dateExp = new Date(2020,11,11);
		dateExp = dateExp.toGMTString();
		ifpermanent = "; expires=" + dateExp + ";";
    }
    else{
		ifpermanent = "";
    }
    document.cookie = nom + "=" + escape(valeur) + ifpermanent;
}

function popup(bgcolor, border, texte, close, url, w, h){
    larg = screen.width-50;
    haut = screen.height-50;
    html_haut = "<table width='"+larg+"' height='"+haut+"'><tr><td valign='middle' align='center'>";
    html_bas = "</td></tr></table>";
    html_mil = "<table style='background: "+border+"' width='"+w+"' cellpadding='2' cellspacing='0'><tr><td><table style='margin-left: auto;margin-right: auto;text-align: left;background: "+bgcolor+"' width='100%' cellpadding='0' cellspacing='0'>";
    html_mil += "<tr><td align='right'><a href='#' onclick='closeSup();return(false)'><img style='border: 0;' src='images/close.gif' alt='' title='"+close+"' /></a></td></tr><tr>";
    html_mil += "<td style='width: "+w+"px;height: "+h+"px;' align='center' onmouseover=this.style.cursor='pointer'; onclick=window.location.href='" +url+ "'><big><b>" +texte+ "</b></big></td>";
    html_mil += "</tr>";
    html_mil += "</table></td></tr></table>";
    window.onload = showsup;
}