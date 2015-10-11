function addConfigInputError(input, errorMsg) {
    $('#infos').html(i18n.db_connect_fail + '<br/>' + errorMsg);
    $('#loading_img').remove();
    if (input !== null) input.addClass('error');
}

function checkConfigForm(process) {
    var host        = $('input[name="db_host"]');
    var user        = $('input[name="db_user"]');
    var pass        = $('input[name="db_pass"]');
    var dbname      = $('input[name="db_name"]');
    var prefix      = $('input[name="db_prefix"]');
    var bddError    = null;
    var formErrors  = 0;

    $('input[id]').each(function() {
        if ($(this).attr('type') == 'text' || $(this).attr('type') == 'password') {
            if (($(this).val() == '' && $(this).attr('name') != 'db_pass' && $(this).attr('name') != 'db_prefix')
                || ($(this).attr('name') == 'db_pass' && user.val() != 'root' && $(this).val() == '')
            ) {
                $(this).addClass('error');
                formErrors++;
            }
        }
    });

    if (formErrors != 0) {
        return false;
    }
    else {
        $('#infos')
        .text(i18n.wait + '  ')
        .append('<img src="media/images/loading.gif" alt="" id="loading_img" />');

        //password = encodeURIComponent(pass.val());

        $.ajax({
            async: false,
            type: 'POST',
            url: 'index.php?action=dbConnectTest',
            data: {
                'db_host' : host.val(),
               'db_user' : user.val(),
               'db_pass' : pass.val(),
               'db_name' : dbname.val(),
               'db_prefix' : prefix.val()
            }
        }).done(function(txt) {
            host.removeClass('error');
            user.removeClass('error');
            pass.removeClass('error');
            dbname.removeClass('error');
            prefix.removeClass('error');

            if (txt == 'OK') {
                bddError = false;
            }
            else {
                bddError = true;

                if (txt == 'DB_HOST_ERROR') {
                    addConfigInputError(host, i18n.db_host_error);
                }
                else if (txt == 'DB_LOGIN_ERROR') {
                    addConfigInputError(pass, i18n.db_login_error);

                    if (process == 'install')
                        user.addClass('error');
                }
                else if (txt == 'DB_NAME_ERROR') {
                    addConfigInputError(dbname, i18n.db_name_error);
                }
                else if(txt == 'DB_PREFIX_ERROR') {
                    addConfigInputError(prefix, i18n.db_prefix_error);
                }
                else if(txt == 'DB_CHARSET_ERROR') {
                    addConfigInputError(null, i18n.db_charset_error);
                }
                else {
                    $('#infos').html(txt);
                }
            }
        });

        if (bddError == false)
            $('#form_config').submit();
    }
}

function checkConfigInput(input) {
    if (($(input).val() == '' && $(input).attr('name') != 'db_pass')
        || ($(input).attr('name') == 'db_pass' && $('input[name="db_user"]').val() != 'root' && $(input).val() == '')
    )
        $(input).addClass('error');
        else
            $(input).removeClass('error');
}