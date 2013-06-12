$(document).ready(function(){
    ct_script = $("#ct_script");
    form = ct_script.parents('form:first');

    firstInput = form.find('input[type=submit]');

    firstInput.click(function(){
        ct_script.val('klan');
    });

    form.keyup(function(e){
        if(e.keyCode == 13){
            ct_script.val('klan');
        }
    });
});
