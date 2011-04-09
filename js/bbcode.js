function storeCaret (textarea) { 
if (document.getElementById(textarea).createTextRange) document.getElementById(textarea).caretPos = document.selection.createRange().duplicate(); 
}
	

function insertAtCaret (textarea, icon) { 
if (document.getElementById(textarea).createTextRange && document.getElementById(textarea).caretPos) { 
var caretPos = document.getElementById(textarea).caretPos; 
selectedtext = caretPos.text; 
caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? icon + '' : icon; 
caretPos.text = caretPos.text + selectedtext; }
else if (document.getElementById(textarea).textLength > 0){
Deb = document.getElementById(textarea).value.substring( 0 , document.getElementById(textarea).selectionStart );
Fin = document.getElementById(textarea).value.substring( document.getElementById(textarea).selectionEnd , document.getElementById(textarea).textLength );
document.getElementById(textarea).value = Deb + icon + Fin; }
else{ document.getElementById(textarea).value = document.getElementById(textarea).value + icon; }
document.getElementById(textarea).focus(); 
}


function PopupinsertAtCaret (textarea, icon) { 
if (opener.document.getElementById(textarea).createTextRange && opener.document.getElementById(textarea).caretPos) { 
var caretPos = opener.document.getElementById(textarea).caretPos; 
selectedtext = caretPos.text; 
caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? icon + '' : icon; 
caretPos.text = caretPos.text + selectedtext; }
else if (opener.document.getElementById(textarea).textLength > 0){
Deb = opener.document.getElementById(textarea).value.substring( 0 , opener.document.getElementById(textarea).selectionStart );
Fin = opener.document.getElementById(textarea).value.substring( opener.document.getElementById(textarea).selectionEnd , opener.document.getElementById(textarea).textLength );
opener.document.getElementById(textarea).value = Deb + icon + Fin; }
else{ opener.document.getElementById(textarea).value = opener.document.getElementById(textarea).value + icon; }
}


function substr_count(str, ssstr) {
num = 0;
while(str.indexOf(ssstr) != -1) 
{
str = str.substr(str.indexOf(ssstr) + ssstr.length, str.length - (str.indexOf(ssstr)+ssstr.length));
num++;
}
return num;
}


function backslash(textarea) {
texte = document.getElementById(textarea).value;
//texte = texte.replace(/\\/g,"\\\\");
document.getElementById(textarea).value=texte;
}

function BBcode_close(textarea) {
texte = document.getElementById(textarea).value;
bbtags = new Array('[b]','[/b]','[i]','[/i]','[u]','[/u]','[quote','[/quote]','[code]','[/code]','[li]','[/li]','[img','[/img]','[url','[/url]','[email]','[/email]','[center]','[/center]','[flash','[/flash]','[color','[/color]','[font','[/font]','[size','[/size]','[align','[/align]','[blink]','[/blink]','[strike]','[/strike]','[marquee]','[/marquee]','[updown]','[/updown]','[flip]','[/flip]','[blink]','[/blink]','[blur]','[/blur]','[glow','[/glow]','[shadow','[/shadow]');
size= bbtags.length;

for (var i =0; i < size; i+=2) {
nb_open=substr_count(texte, bbtags[i]);
nb_close=substr_count(texte, bbtags[i+1]);
                         
if (nb_open>nb_close) {
for (var z =nb_open; z > nb_close; z--){
texte+=bbtags[i+1];}
}
if (nb_open < nb_close) {
for (var a =nb_open; a < nb_close; a++){
texte=bbtags[i]+texte;}
}
}  
document.getElementById(textarea).value=texte;    
}


function ajout_url(textarea, texturl, textname){
VarUrl = window.prompt(texturl,'http://');
VarNom = window.prompt(textname,'');
if (VarUrl.indexOf('http://') == -1) VarUrl = 'http://'+VarUrl;
if ((VarUrl != null) && (VarUrl != '') && (VarNom != null) && (VarNom != '')) {
insertAtCaret(textarea, '[url='+VarUrl+']'+VarNom+'[/url]');}
}
	

function ajout_img(textarea, textimgturl){
VarImg = window.prompt( textimgturl,'http://' );
if (VarImg.indexOf('http://') == -1) VarImg = 'http://'+VarImg;
if ((VarImg != null) && (VarImg != '')) {
insertAtCaret(textarea, '[img]'+VarImg+'[/img]');}
}

	
function ajout_flash(textarea, textflashurl, textflashwidth, textflashheight){
Varflash = window.prompt(textflashurl,'http://');
VarFWidth = window.prompt(textflashwidth,'100');
VarFHeight = window.prompt(textflashheight,'100');
if (Varflash.indexOf('http://') == -1) Varflash = 'http://'+Varflash;
if ((Varflash != null) && (Varflash != '')) {
insertAtCaret(textarea, '[flash='+VarFWidth+'x'+VarFHeight+']'+Varflash+'[/flash]');}
}


function ajout_text(textarea, entertext, tapetext, balise){
if (document.selection && document.selection.createRange().text != ''){
document.getElementById(textarea).focus();
VarTxt = document.selection.createRange().text;
document.selection.createRange().text = '['+balise+']'+VarTxt+'[/'+balise+']';}
else if (document.getElementById(textarea).selectionEnd && (document.getElementById(textarea).selectionEnd - document.getElementById(textarea).selectionStart > 0)){
valeurDeb = document.getElementById(textarea).value.substring( 0 , document.getElementById(textarea).selectionStart );
valeurFin = document.getElementById(textarea).value.substring( document.getElementById(textarea).selectionEnd , document.getElementById(textarea).textLength );
objectSelected = document.getElementById(textarea).value.substring( document.getElementById(textarea).selectionStart , document.getElementById(textarea).selectionEnd );
document.getElementById(textarea).value = valeurDeb+'['+balise+']'+objectSelected+'[/'+balise+']'+valeurFin;}
else{
VarTxt = window.prompt(entertext,tapetext);
if ((VarTxt != null) && (VarTxt != '')) insertAtCaret(textarea, '['+balise+']'+VarTxt+'[/'+balise+']');}
}


function color(couleur, textarea, entertext, tapetext){
if (document.selection && document.selection.createRange().text != ''){
document.getElementById(textarea).focus();
VarTxt = document.selection.createRange().text;
document.selection.createRange().text = '[color='+couleur+']'+VarTxt+'[/color]';}
else if (document.getElementById(textarea).selectionEnd && (document.getElementById(textarea).selectionEnd - document.getElementById(textarea).selectionStart > 0)){
valeurDeb = document.getElementById(textarea).value.substring( 0 , document.getElementById(textarea).selectionStart );
valeurFin = document.getElementById(textarea).value.substring( document.getElementById(textarea).selectionEnd , document.getElementById(textarea).textLength );
objectSelected = document.getElementById(textarea).value.substring( document.getElementById(textarea).selectionStart , document.getElementById(textarea).selectionEnd );
document.getElementById(textarea).value = valeurDeb+'[color='+couleur+']'+objectSelected+'[/color]'+valeurFin;}
else{
if (couleur != null && couleur != '') VarTxt = window.prompt(entertext,tapetext);
if (VarTxt != null && VarTxt != '' && couleur != '') insertAtCaret(textarea, '[color='+couleur+']'+VarTxt+'[/color]');}
}


function taille(size, textarea, entertext, tapetext){
if (document.selection && document.selection.createRange().text != ''){
document.getElementById(textarea).focus();
VarTxt = document.selection.createRange().text;
document.selection.createRange().text = '[size='+size+']'+VarTxt+'[/size]';}
else if (document.getElementById(textarea).selectionEnd && (document.getElementById(textarea).selectionEnd - document.getElementById(textarea).selectionStart > 0)){
valeurDeb = document.getElementById(textarea).value.substring( 0 , document.getElementById(textarea).selectionStart );
valeurFin = document.getElementById(textarea).value.substring( document.getElementById(textarea).selectionEnd , document.getElementById(textarea).textLength );
objectSelected = document.getElementById(textarea).value.substring( document.getElementById(textarea).selectionStart , document.getElementById(textarea).selectionEnd );
document.getElementById(textarea).value = valeurDeb+'[size='+size+']'+objectSelected+'[/size]'+valeurFin;}
else{
if (size != null && size != '') VarTxt = window.prompt(entertext,tapetext);
if (VarTxt != null && VarTxt != '' && size != '') insertAtCaret(textarea, '[size='+size+']'+VarTxt+'[/size]');}
}


function police(font, textarea, entertext, tapetext){
if (document.selection && document.selection.createRange().text != ''){
document.getElementById(textarea).focus();
VarTxt = document.selection.createRange().text;
document.selection.createRange().text = '[font='+font+']'+VarTxt+'[/font]';}
else if (document.getElementById(textarea).selectionEnd && (document.getElementById(textarea).selectionEnd - document.getElementById(textarea).selectionStart > 0)){
valeurDeb = document.getElementById(textarea).value.substring( 0 , document.getElementById(textarea).selectionStart );
valeurFin = document.getElementById(textarea).value.substring( document.getElementById(textarea).selectionEnd , document.getElementById(textarea).textLength );
objectSelected = document.getElementById(textarea).value.substring( document.getElementById(textarea).selectionStart , document.getElementById(textarea).selectionEnd );
document.getElementById(textarea).value = valeurDeb+'[font='+font+']'+objectSelected+'[/font]'+valeurFin;}
else{
if (font != null && font != '') VarTxt = window.prompt(entertext,tapetext);
if (VarTxt != null && VarTxt != '' && font != '') insertAtCaret(textarea, '[font='+font+']'+VarTxt+'[/font]');}
}


function ajout_mail(textarea, textmail){
VarMail = window.prompt(textmail,'mail@gmail.com');
if ((VarMail != null) && (VarMail != '')) {
insertAtCaret(textarea, '[email]'+VarMail+'[/email]');}
}