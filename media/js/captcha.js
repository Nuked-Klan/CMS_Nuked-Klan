$(document).ready(function(){
    var ct_script = $(".ct_script");
    ct_script.each(function(){
        var input = this;
        var form = $(this).parents('form:first').submit(function(){
            $(input).val('klan');
        });

    });
});