$(document).ready(function(){
    ct_script = $("#ct_script");
    form = ct_script.parents('form:first');

    firstInput = form.find('input:first');

    firstInput.focus(function(){
        ct_script.val('klan');
    });
});
