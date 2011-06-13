var xOffset=5;var yOffset=5;var shown=false;var Params=new Object;var ie=(document.all)?true:false;var ns4=(document.layers)?true:false;var ns6=(document.getElementById)?true:false;var tOpacity=(ie)?15:25;var radius=2;var padding=2;var spacing=0;if(ie||ns4||ns6){var myBox;}
function ieRealBody(){if(document.compatMode&&document.compatMode!='BackCompat'){return document.documentElement;}
else{return document.body;}}
function moveMyBox(e){if(shown){if(ie){var posX=event.x+ieRealBody().scrollLeft;var posY=event.y+ieRealBody().scrollTop;if(!window.opera){var winWidth=ieRealBody().clientWidth;var winHeight=ieRealBody().clientHeight;var rightEdge=winWidth-event.clientX-xOffset;var bottomEdge=winHeight-event.clientY-yOffset;}}
else{var posX=e.pageX;var posY=e.pageY;var winWidth=window.innerWidth-20;var winHeight=window.innerHeight-20;var rightEdge=winWidth-e.clientX-xOffset;var bottomEdge=winHeight-e.clientY-yOffset;}
var leftEdge=(xOffset<0)?xOffset*-1:-1000;if(myBox.offsetWidth>winWidth/3){myBox.style.width=(winWidth/3)+'px';}
if(rightEdge<myBox.offsetWidth){myBox.style.left=5+posX-myBox.offsetWidth+'px';}
else{if(posX<leftEdge){myBox.style.left='5px';}
else{myBox.style.left=posX+xOffset+'px';}}
if(bottomEdge<myBox.offsetHeight){myBox.style.top=posY-myBox.offsetHeight-yOffset+'px';}
else{myBox.style.top=20+posY+yOffset+'px';}}}
function AffBulle(title,text,width){shown=true;title=title.replace(/^\s*/,'');var content;var t=(typeof title!='undefined'&&title!='')?true:false;content='<table style="border: '+Params.Border.Width+'px '+Params.Border.Style+' '+Params.Color+'; -moz-border-radius: '+radius+'px;" cellspacing="'+spacing+'" cellpadding="'+padding+'">';if(t){content+='	<tr>'+'		<td style="background: '+Params.Background+';">&nbsp;&raquo;&nbsp;&nbsp;<b>'+title+'</b>&nbsp;</td>'+'	</tr>';}
content+='	<tr>'+'		<td style=\"padding: 0px; margin: auto; height: 1px; background-color: '+Params.Color+';\"></td>'+'	</tr>'+'	<tr>'+'		<td style="background: '+Params.Background+'; vertical-align: top">'+'			<table width="100%" style="background: '+Params.Background+';" cellpadding="'+padding+'" cellspacing="'+spacing+'">'+'				<tr>'+'					<td>'+text+'</td>'+'				</tr>'+'			</table>'+'		</td>'+'	</tr>'+'</table>';if(ns4){myBox.document.write(content);myBox.document.close();myBox.visibility='show';}
else{myBox.innerHTML=content;myBox.style.visibility='visible';if(ie){myBox.style.filter='alpha(opacity='+Params.Opacity+')';}
else if(ns6){myBox.style.opacity=Params.Opacity/100;}}}
function HideBulle(){shown=false;if(ns4){myBox.visibility='hide';myBox.top='-1000px';myBox.bgcolor='';myBox.width='';}
else{myBox.style.visibility='hidden';myBox.style.top='-1000px';myBox.style.backgroundColor='';myBox.style.width='';}}
function InitBulle(background,color,params){Params.Background=background;Params.Color=color;Params.Opacity=85;Params.Border=new Object();Params.Border.Width=1;Params.Border.Style='solid';Params.Border.Color='#232323';if(ns4){document.write('<layer name="box" top="0" left="0" visibility="hide"></layer>');window.captureEvents(Event.MOUSEMOVE);window.onMouseMove=moveMyBox;myBox=document.layers['box'];}
else{document.write('<div id="box" style="position: absolute; top: 0px; left: 0px; visibility: hidden;"></div>');document.onmousemove=moveMyBox;if(ie){myBox=document.all['box'];}
else if(ns6){myBox=document.getElementById('box');}}}