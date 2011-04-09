//Tout JavaScript.com - Info Bulle DHTML
//Auteur original :Olivier Hondermarck  <webmaster@toutjavascript.com>
//Modifs compatibilité Netscape 6/Mozilla : Cédric Lamalle 09/2001 <cedric@cpac.embrapa.br>
//Correction Mac IE5 (Merci Fred)
//
// Modifié et corrigé par MaStErPsX (24/01/2005) - http://www.nuked-klan.org
//Utilisation :
//InitBulle('fond du texte','fond du titre et encadrement', 'espacement titre et encadrement');
//onMouseOver="AffBulle('titre','texte','largeur')" onMouseOut="HideBulle()"
//
// Exemple : 
//<script type="text/javascript" src="infobulle.js"></script>
//<script type="text/javascript">InitBulle('#FFFFFF','#000000', 2);</script>
//<a href="#" onmouseOver="AffBulle('titre infobulle', 'texte infobulle', 250)" onmouseOut="HideBulle()">Infobulle DHTML</a>

var ie = (document.all)? true:false;
var ns4 = (document.layers)? true:false;
var ns6 = (document.getElementById)? true:false;
var IB=new Object;
var posX=0;
var posY=0;
var xOffset=20;
var yOffset=20;

function AffBulle(titre, texte, w) {

  contenu="<table width='"+w+"' cellspacing='0' cellpadding='"+IB.NbPixel+"'><tr style='background: "+IB.ColContour+";'><td>&nbsp;<b>"+titre+"</b></td></tr><tr style='background: "+IB.ColContour+";'><td valign='top'><table width='100%' style='background: "+IB.ColFond+";' cellpadding='3' cellspacing='0'><tr><td>"+texte+"</td></tr></table></td></tr></table>&nbsp;";
  var finalPosX=posX-xOffset;
  if (finalPosX<0) finalPosX=0;

  if (ns4) {
    document.layers["infobulle"].document.write(contenu);
    document.layers["infobulle"].document.close();
    document.layers["infobulle"].top=posY+yOffset+"px";
    document.layers["infobulle"].left=finalPosX+"px";
    document.layers["infobulle"].visibility="show";}

  if (ie) {
    infobulle.innerHTML=contenu;
    document.all["infobulle"].style.top=posY+yOffset+"px";
    document.all["infobulle"].style.left=finalPosX+"px";
    document.all["infobulle"].style.visibility="visible";

  }else if (ns6) {
    document.getElementById("infobulle").innerHTML=contenu;
    document.getElementById("infobulle").style.top=posY+yOffset+"px";
    document.getElementById("infobulle").style.left=finalPosX+"px";
    document.getElementById("infobulle").style.visibility="visible";
  }

}

function getMousePos(e) {
  if (ie) {
  posX=event.x+document.documentElement.scrollLeft;
  posY=event.y+document.documentElement.scrollTop;
  } else {
  posX=e.pageX;
  posY=e.pageY; 
  }
}

function HideBulle() {
	if (ns4) {document.layers["infobulle"].visibility="hide";}
	if (ie) {document.all["infobulle"].style.visibility="hidden";}
	else if (ns6){document.getElementById("infobulle").style.visibility="hidden";}
}

function InitBulle(ColFond, ColContour, NbPixel) {
	IB.ColFond=ColFond;IB.ColContour=ColContour;IB.NbPixel=NbPixel;
	if (ns4) {
		document.write("<layer name='infobulle' top='0' left='0' visibility='hide'></layer>");
		window.captureEvents(Event.MOUSEMOVE);window.onMouseMove=getMousePos;
	}
	if (ie) {
		document.write("<div id='infobulle' style='position:absolute;top:0;left:0;visibility:hidden'></div>");
		document.onmousemove=getMousePos;
	}
	//modif CL 09/2001 - NS6 : celui-ci ne supporte plus document.layers mais document.getElementById
	else if (ns6) {
	        document.write("<div id='infobulle' style='position:absolute;top:0;left:0;visibility:hidden;'></div>");
	        document.onmousemove=getMousePos;
	}

}