$(document).ready(function () {
    $('.RL_getRow').each(function () {
        $(this).click(function (e) {
            e.preventDefault();
            RL_getFormBlock($(this).attr('id'));
        });
    });

    $('.RL_inputFile').change(function(){
        $(this).next().next().children('span').html($(this).val());
    });

    $('input[type=range]').bind('input', function () {
        $(this).trigger('change');
    });

    $('#RL_mainLogoMarginInput').change(function(){
       $(this).next().html($(this).val()+' px');
    });

    $('#RL_selectColor').change(function(){
        $(this).removeClass();
        $(this).addClass('RL_select RL_small RL_select_'+$(this).val());
    })

    function RL_getFormBlock(blockName){
        if($('#RL_formContainer').length == 0){
            RL_createContainer();
        }
        $('#RL_formContainer').center();

        $('#RL_formContainer').append($('.RL_form'+blockName));

        $('.RL_form'+blockName).css('display', 'block');

        $('#RL_formWrapper').fadeIn(function(){
            $('#RL_formContainer').slideDown();
        });

        $('#RL_formWrapper').click(function() {
            RL_hideFormBlock(blockName);
        });

        $('.button').click(function() {
            RL_hideFormBlock(blockName);
        });
    }

    function RL_hideFormBlock(blockName){
        $('#RL_formContainer').slideUp(function(){
            $('#RL_formWrapper').fadeOut();

            $('.RL_form'+blockName).css('display', 'none');

            $('#RL_formBlocks').append($('.RL_form'+blockName));
        });

        RL_checkForm();
    }

    function RL_createContainer(){
        $('body').append('<div id="RL_formWrapper"></div><div id="RL_formContainer"></div>');
    }

    function RL_checkForm(){
        var errors = 0;
        $('input[type=text], select, input[type=checkbox]').each(function(){
            if (($(this).attr('data-check') != $(this).val()) && $(this).attr('type') != 'checkbox') {
                errors++;
            }
            else if(($(this).attr('data-check') != $(this).attr('checked')) && $(this).attr('type') == 'checkbox'){
                errors++;
            }

            if (errors > 0) {
                $('.RL_alertForm').slideDown();
            }
            else {
                $('.RL_alertForm').slideUp();
            }
        });
    }

    $('#RL_addSponsor').click(function(){
        var content = $('#RL_sponsor').html();
        var tr = document.createElement('tr');
        $(tr).html(content);
        $('#RL_formSponsors>table').append(tr);
    });
});

jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2)) + "px");
    return this;
}
