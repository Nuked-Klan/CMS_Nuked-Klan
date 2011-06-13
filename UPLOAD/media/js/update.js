NKUpdate={NKmess:null,NKmessThread:null,NKmessTable:new Array(),NKmod:null,NKmodThread:null,NKmodCallback:null,UpdateUrl:'',lng:'french',GetAjax:function(){var xhr=null;if(window.XMLHttpRequest){xhr=new XMLHttpRequest();}
else if(window.ActiveXObject){xhr=new ActiveXObject("Microsoft.XMLHTTP");}
return xhr;},SetNKorgMessById:function(id){if(this.NKmess!=null){document.getElementById(id).firstChild.data=this.NKmess;}
else{this.NKmessTable.push(id);if(this.NKmessThread==null){this.NKmessThread=this.GetAjax();this.NKmessThread.onreadystatechange=function(){if(this.readyState==4){if(this.status/100<4){NKUpdate.NKmess=this.responseText.replace(/\n/,'<br />');if(NKUpdate.NKmess==''){NKUpdate.NKmess='Aucun message...';}}
else{NKUpdate.NKmess='Error for connecting to nuked-klan.org';}
for(var i in NKUpdate.NKmessTable){document.getElementById(NKUpdate.NKmessTable[i]).innerHTML=NKUpdate.NKmess;document.getElementById(NKUpdate.NKmessTable[i]).style.display='block';}}}
this.NKmessThread.open('GET',this.UpdateUrl+'index.php?file=Update&nuked_nude=message&lng='+this.lng,true);this.NKmessThread.send(null);}}},SetModCallback:function(func,signs){this.NKmodCallback=func;if(this.NKmod!=null){for(var i in this.NKmod){this.NKmodCallback(this.NKmod[i]);}}
else if(this.NKmodThread==null){this.NKmodThread=this.GetAjax();this.NKmodThread.onreadystatechange=function(){if(this.readyState==4){if(this.status/100<4){var mods=this.responseXML.getElementsByTagName('Mod');NKUpdate.NKmod=new Array();for(var i=0;i<mods.length;i++){NKUpdate.NKmod[i]=new Array();NKUpdate.NKmod[i]['UpdateFile']=null;NKUpdate.NKmod[i]['Note']=null;for(var j in mods[i].childNodes){if(mods[i].childNodes[j].firstChild!=null){NKUpdate.NKmod[i][mods[i].childNodes[j].nodeName]=mods[i].childNodes[j].firstChild.data;}
else{NKUpdate.NKmod[i][mods[i].childNodes[j].nodeName]='';}}}
NKUpdate.SetModCallback(NKUpdate.NKmodCallback,null);}
else{alert('Error : we couldn\'t connect to nuked-klan.org');}}}
this.NKmodThread.open('POST',this.UpdateUrl+'index.php?file=Update&nuked_nude=xml',true);this.NKmodThread.setRequestHeader('Content-Type','application/x-www-form-urlencoded');var request='lng='+this.lng;for(var i in signs){request+='&sign'+i+'='+signs[i];}
this.NKmodThread.send(request);}}}