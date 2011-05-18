function storeCaret (textarea){ 
    if (document.getElementById(textarea).createTextRange) document.getElementById(textarea).caretPos = document.selection.createRange().duplicate(); 
}	

function insertAtCaret (textarea, icon){ 
    if (document.getElementById(textarea).createTextRange && document.getElementById(textarea).caretPos){ 
        var caretPos = document.getElementById(textarea).caretPos; 
        selectedtext = caretPos.text; 
        caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? icon + '' : icon; 
        caretPos.text = caretPos.text + selectedtext;
    }
    else if (document.getElementById(textarea).textLength > 0){
        Deb = document.getElementById(textarea).value.substring( 0 , document.getElementById(textarea).selectionStart );
        Fin = document.getElementById(textarea).value.substring( document.getElementById(textarea).selectionEnd , document.getElementById(textarea).textLength );
        document.getElementById(textarea).value = Deb + icon + Fin;
    }
    else{
        document.getElementById(textarea).value = document.getElementById(textarea).value + icon;
    }
    document.getElementById(textarea).focus(); 
}

function PopupinsertAtCaret (textarea, icon){ 
    if (opener.document.getElementById(textarea).createTextRange && opener.document.getElementById(textarea).caretPos){ 
        var caretPos = opener.document.getElementById(textarea).caretPos; 
        selectedtext = caretPos.text;
        caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == '' ? icon + '' : icon;        
        caretPos.text = caretPos.text + selectedtext;
    }
    else if (opener.document.getElementById(textarea).textLength > 0){
        Deb = opener.document.getElementById(textarea).value.substring( 0 , opener.document.getElementById(textarea).selectionStart );
        Fin = opener.document.getElementById(textarea).value.substring( opener.document.getElementById(textarea).selectionEnd , opener.document.getElementById(textarea).textLength );
        opener.document.getElementById(textarea).value = Deb + icon + Fin;
    }
    else{
        opener.document.getElementById(textarea).value = opener.document.getElementById(textarea).value + icon;
    }
}