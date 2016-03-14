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
